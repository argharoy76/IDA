<?php

namespace App\Services;

class McqPdfParserService
{
    public function __construct()
    {
        // Ensure Smalot PdfParser autoloader is active
        spl_autoload_register(function ($class) {
            if (str_starts_with($class, 'Smalot\\PdfParser\\')) {
                $base = function_exists('base_path') ? base_path() : dirname(__DIR__, 2);
                $path = $base . '/vendor/smalot/pdfparser/src/' . str_replace('\\', '/', $class) . '.php';
                if (file_exists($path)) {
                    require_once $path;
                }
            }
        });
    }

    /**
     * Parse text directly from an uploaded PDF file path with geometric reading order.
     */
    public function parsePdfFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \Exception("File not found at: {$filePath}");
        }

        @ini_set('memory_limit', '512M');

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            $pages = $pdf->getPages();

            $fullText = '';
            foreach ($pages as $page) {
                $pageText = $this->extractPageTextGeometric($page);
                $fullText .= $pageText . "\n\n";
            }

            if (trim($fullText) === '') {
                $fullText = $pdf->getText();
            }

            return $this->parseText($fullText);
        } catch (\Throwable $e) {
            // Fallback: try raw stream read if standard parser encounters specific encoding
            $rawContent = @file_get_contents($filePath);
            if ($rawContent) {
                return $this->parseText($rawContent);
            }
            throw new \Exception("Failed to extract text from PDF: " . $e->getMessage());
        }
    }

    /**
     * Extract text from a PDF page using coordinates from getDataTm() to preserve
     * human reading flow, columns, and prevent side-by-side equations from being scrambled.
     */
    public function extractPageTextGeometric($page): string
    {
        try {
            $dataTm = $page->getDataTm();
            if (empty($dataTm)) {
                return $page->getText();
            }

            // Collect non-empty text items with coordinates
            $items = [];
            $minX = INF;
            $maxX = -INF;
            foreach ($dataTm as $entry) {
                $tm = $entry[0] ?? [];
                $text = $entry[1] ?? '';
                if (trim($text) === '') {
                    continue;
                }
                $x = (float)($tm[4] ?? 0);
                $y = (float)($tm[5] ?? 0);
                if ($x < $minX) $minX = $x;
                if ($x > $maxX) $maxX = $x;
                $items[] = ['x' => $x, 'y' => $y, 'text' => $text];
            }

            if (empty($items)) {
                return $page->getText();
            }

            $pageWidth = $maxX - $minX;
            $midX = $minX + ($pageWidth / 2);

            // Check if this page is a distinct 2-column layout
            $hasLeftQuestions = false;
            $hasRightQuestions = false;
            $questionMarkerRegex = '/^(?:Q(?:uestion)?\s*)?[0-9০-৯]{1,3}[\.\)]/ui';

            foreach ($items as $item) {
                $trimmed = trim($item['text']);
                if (preg_match($questionMarkerRegex, $trimmed)) {
                    if ($item['x'] < $midX) {
                        $hasLeftQuestions = true;
                    } else {
                        $hasRightQuestions = true;
                    }
                }
            }

            if ($hasLeftQuestions && $hasRightQuestions && $pageWidth > 200) {
                // Two distinct question columns: extract Left column first, then Right column
                $leftItems = array_filter($items, fn($i) => $i['x'] < $midX);
                $rightItems = array_filter($items, fn($i) => $i['x'] >= $midX);

                $leftText = $this->sortItemsToLines($leftItems);
                $rightText = $this->sortItemsToLines($rightItems);
                return $leftText . "\n\n" . $rightText;
            }

            // Single column layout (with possible side-by-side formulas/options)
            return $this->sortItemsToLines($items);
        } catch (\Throwable $e) {
            return $page->getText();
        }
    }

    /**
     * Group items into horizontal lines based on Y coordinate, then sort left-to-right by X.
     */
    protected function sortItemsToLines(array $items, float $lineThreshold = 4.0): string
    {
        $lines = [];
        foreach ($items as $item) {
            $y = $item['y'];
            $matchedKey = null;
            foreach ($lines as $lineY => &$rowItems) {
                if (abs($y - $lineY) <= $lineThreshold) {
                    $matchedKey = $lineY;
                    $rowItems[] = $item;
                    break;
                }
            }
            if ($matchedKey === null) {
                $lines[$y] = [$item];
            }
        }

        // Sort lines by Y descending (PDF Y=0 is at bottom, so largest Y is at top)
        krsort($lines, SORT_NUMERIC);

        $out = [];
        foreach ($lines as $rowItems) {
            // Sort items in this line by X ascending (left to right)
            usort($rowItems, fn($a, $b) => $a['x'] <=> $b['x']);
            $lineStr = '';
            foreach ($rowItems as $it) {
                $t = $it['text'];
                if ($lineStr !== '' && !str_ends_with($lineStr, ' ') && !str_starts_with($t, ' ')) {
                    $lineStr .= ' ';
                }
                $lineStr .= $t;
            }
            $trimmed = trim($lineStr);
            if ($trimmed !== '') {
                $out[] = $trimmed;
            }
        }

        return implode("\n", $out);
    }

    /**
     * Parse raw string containing 1 to 500+ MCQs.
     * Preserves LaTeX math regions during splitting to prevent formula
     * content from being misinterpreted as question/option delimiters.
     */
    public function parseText(string $rawText): array
    {
        // UTF-8 Normalization
        $text = mb_convert_encoding($rawText, 'UTF-8', 'auto');
        // Handle BOM markers
        $text = preg_replace('/^\xEF\xBB\xBF/', '', $text);
        // Normalize line endings
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = trim($text);

        // Pre-normalize: separate option letters touching math closing delimiters
        // e.g. $$A) -> $$\nA)  or  $B) -> $\nB)
        $text = preg_replace('/(\$\$|\$|\\\\\]|\\\\\))(?=(?:[\-\*]\s*)?\(?[A-Da-dক-ঘ][\.\)\:\-\]])/u', "$1\n", $text);

        // Synthesize STEM LaTeX: bracket matrices [[...]], bare Greek letters, math operators
        $text = self::synthesizeStemLatex($text);

        // --- LaTeX Preservation: Extract math regions before splitting ---
        $latexPlaceholders = [];
        $latexIndex = 0;

        // 1. $$...$$ (display math)
        $text = preg_replace_callback('/\$\$([\s\S]+?)\$\$/u', function($m) use (&$latexPlaceholders, &$latexIndex) {
            $ph = '__LATEXPH_' . ($latexIndex++) . '__';
            $latexPlaceholders[$ph] = $m[0];
            return $ph;
        }, $text);

        // 2. $...$ (inline math)
        $text = preg_replace_callback('/\$([^\$]+?)\$/u', function($m) use (&$latexPlaceholders, &$latexIndex) {
            $ph = '__LATEXPH_' . ($latexIndex++) . '__';
            $latexPlaceholders[$ph] = $m[0];
            return $ph;
        }, $text);

        // 3. \[...\] (display math)
        $text = preg_replace_callback('/\\\\\[([\s\S]+?)\\\\\]/u', function($m) use (&$latexPlaceholders, &$latexIndex) {
            $ph = '__LATEXPH_' . ($latexIndex++) . '__';
            $latexPlaceholders[$ph] = $m[0];
            return $ph;
        }, $text);

        // 4. \(...\) (inline math)
        $text = preg_replace_callback('/\\\\\(([\s\S]+?)\\\\\)/u', function($m) use (&$latexPlaceholders, &$latexIndex) {
            $ph = '__LATEXPH_' . ($latexIndex++) . '__';
            $latexPlaceholders[$ph] = $m[0];
            return $ph;
        }, $text);

        // 5. Standalone \begin{...}...\end{...} environments
        $text = preg_replace_callback('/\\\\begin\{([a-zA-Z*]+)\}[\s\S]*?\\\\end\{\1\}/u', function($m) use (&$latexPlaceholders, &$latexIndex) {
            $ph = '__LATEXPH_' . ($latexIndex++) . '__';
            $latexPlaceholders[$ph] = $m[0];
            return $ph;
        }, $text);

        // Break text into individual question blocks.
        // Handles horizontal dividers (---, ***, ___), markdown headers (#, ##, ###),
        // markdown bold/italic (**1. Title**, *1.*, _1._), question prefixes (Q, Question, Prob, Ex),
        // and standard ASCII/Bengali numerals.
        $splitPattern = '/(?:\r?\n|^)(?:[\-\*_]{3,}\s*(?:\r?\n)+)?(?=(?:#+\s*)?(?:\*{0,2}|_{0,2})(?:(?:Q(?:uestion)?|Ex(?:ample)?|Prob(?:lem)?)\s*[\.:\-]?\s*)?[0-9০-৯]{1,4}(?:\([a-zA-Z0-9]+\))?(?:[\.\)\:\-]|(?:\*{1,2}|_{1,2})[\.\)\:\-]|[\.\)\:\-](?:\*{1,2}|_{1,2}))\s+)/ui';
        
        $chunks = preg_split($splitPattern, $text, -1, PREG_SPLIT_NO_EMPTY);

        $parsedQuestions = [];
        $validCount = 0;
        $flaggedCount = 0;
        $qIndex = 1;

        foreach ($chunks as $chunk) {
            $chunk = trim($chunk);
            if (strlen($chunk) < 8) {
                continue;
            }

            // Parse chunk while LaTeX placeholders are still masked,
            // preventing formula contents (e.g. "- A)") from matching option regexes.
            $item = $this->parseQuestionChunk($chunk, $qIndex, $latexPlaceholders);
            if ($item) {
                if ($item['is_valid']) {
                    $validCount++;
                } else {
                    $flaggedCount++;
                }
                $parsedQuestions[] = $item;
                $qIndex++;
            }
        }

        return [
            'total_detected' => count($parsedQuestions),
            'valid_count' => $validCount,
            'flagged_count' => $flaggedCount,
            'questions' => $parsedQuestions,
        ];
    }

    /**
     * Parse a single question block.
     */
    protected function parseQuestionChunk(string $chunk, int $index, array $latexPlaceholders = []): ?array
    {
        // 1. Extract Answer Key if present
        $correctAnswer = null;
        if (preg_match('/(?:^|\s|\n)(?:\*{0,2}(?:Ans(?:wer)?|Correct(?:\s*Ans(?:wer)?)?|Key|উত্তর|সঠিক উত্তর)[:.\-=]?\*{0,2}\s*[:.\-=]?\s*\(?\*{0,2}([A-Da-dক-ঘ])\*{0,2}\)?)/ui', $chunk, $ansMatch)) {
            $ans = mb_strtoupper(trim($ansMatch[1]), 'UTF-8');
            $ansMap = ['ক' => 'A', 'খ' => 'B', 'গ' => 'C', 'ঘ' => 'D'];
            $correctAnswer = $ansMap[$ans] ?? $ans;
            
            // Remove the answer line from chunk to keep cleaner text
            $chunk = preg_replace('/(?:^|\s|\n)(?:\*{0,2}(?:Ans(?:wer)?|Correct(?:\s*Ans(?:wer)?)?|Key|উত্তর|সঠিক উত্তর)[:.\-=]?\*{0,2}\s*[:.\-=]?\s*\(?\*{0,2}[A-Da-dক-ঘ]\*{0,2}\)?)(?:\n|$|\s*)/ui', "\n", $chunk);
        }

        // 2. Extract Explanation / Note / Solution if present
        // Must appear on a new line (or start of chunk) and require a colon or hyphen.
        // Specifically protects Bengali phrases like "কোনো সমাধান নেই" from false matches.
        $explanation = null;
        if (preg_match('/(?:\r?\n|^)\s*(?:\*{0,2}(?:Explanation|Expl|Note|Hint|Solution|ব্যাখ্যা)\*{0,2}\s*[:=\-]\s*(.+)|(?:\*{0,2}সমাধান\*{0,2}\s*[:\-]\s*(.+)))$/usi', $chunk, $expMatch)) {
            $explanation = trim(!empty($expMatch[1]) ? $expMatch[1] : ($expMatch[2] ?? ''));
            $chunk = preg_replace('/(?:\r?\n|^)\s*(?:\*{0,2}(?:Explanation|Expl|Note|Hint|Solution|ব্যাখ্যা)\*{0,2}\s*[:=\-]\s*.+|(?:\*{0,2}সমাধান\*{0,2}\s*[:\-]\s*.+))$/usi', '', $chunk);
        }

        // 3. Extract Options A, B, C, D
        $optionA = '';
        $optionB = '';
        $optionC = '';
        $optionD = '';

        // Option regex pattern that splits options while preserving labels
        // Handles "* **A)**", "**A)**", "(A)", "A)", "A.", "ক)", "- A)", etc.
        $optionRegex = '/(?:\r?\n|^|\s+)(?:[\-\*]\s*)?(?:\*{0,2}\(?\*{0,2}([A-Da-dক-ঘ])\*{0,2}[\.\)\:\-\]]\*{0,2}\s*)/ui';

        $splitOptions = preg_split($optionRegex, $chunk, -1, PREG_SPLIT_DELIM_CAPTURE);
        $questionText = '';

        if (count($splitOptions) >= 9) {
            // First element is the question prompt
            $questionText = trim($splitOptions[0]);

            for ($i = 1; $i < count($splitOptions); $i += 2) {
                $key = mb_strtoupper(trim($splitOptions[$i]), 'UTF-8');
                $val = trim($splitOptions[$i + 1] ?? '');
                
                $ansMap = ['ক' => 'A', 'খ' => 'B', 'গ' => 'C', 'ঘ' => 'D'];
                $mappedKey = $ansMap[$key] ?? $key;

                match ($mappedKey) {
                    'A' => $optionA = $val,
                    'B' => $optionB = $val,
                    'C' => $optionC = $val,
                    'D' => $optionD = $val,
                    default => null,
                };
            }
        } else {
            // Fallback: Individual extraction
            if (preg_match('/(?:(?:\r?\n|^|\s+)(?:[\-\*]\s*)?\*{0,2}\(?\*{0,2}[Aaক]\*{0,2}[\.\)\:\-\]]\*{0,2}\s*)(.+?)(?=(?:(?:\r?\n|^|\s+)(?:[\-\*]\s*)?\*{0,2}\(?\*{0,2}[Bbখ]\*{0,2}[\.\)\:\-\]]\*{0,2}\s*)|$)/usi', $chunk, $mA)) {
                $optionA = trim($mA[1]);
            }
            if (preg_match('/(?:(?:\r?\n|^|\s+)(?:[\-\*]\s*)?\*{0,2}\(?\*{0,2}[Bbখ]\*{0,2}[\.\)\:\-\]]\*{0,2}\s*)(.+?)(?=(?:(?:\r?\n|^|\s+)(?:[\-\*]\s*)?\*{0,2}\(?\*{0,2}[Ccগ]\*{0,2}[\.\)\:\-\]]\*{0,2}\s*)|$)/usi', $chunk, $mB)) {
                $optionB = trim($mB[1]);
            }
            if (preg_match('/(?:(?:\r?\n|^|\s+)(?:[\-\*]\s*)?\*{0,2}\(?\*{0,2}[Ccগ]\*{0,2}[\.\)\:\-\]]\*{0,2}\s*)(.+?)(?=(?:(?:\r?\n|^|\s+)(?:[\-\*]\s*)?\*{0,2}\(?\*{0,2}[Ddঘ]\*{0,2}[\.\)\:\-\]]\*{0,2}\s*)|$)/usi', $chunk, $mC)) {
                $optionC = trim($mC[1]);
            }
            if (preg_match('/(?:(?:\r?\n|^|\s+)(?:[\-\*]\s*)?\*{0,2}\(?\*{0,2}[Ddঘ]\*{0,2}[\.\)\:\-\]]\*{0,2}\s*)(.+?)$/usi', $chunk, $mD)) {
                $optionD = trim($mD[1]);
            }

            // The question text is everything before the first option (A)
            $firstOptPos = -1;
            if (preg_match('/(?:\r?\n|^|\s+)(?:[\-\*]\s*)?\*{0,2}\(?\*{0,2}[Aaক]\*{0,2}[\.\)\:\-\]]\*{0,2}\s*/ui', $chunk, $matchA, PREG_OFFSET_CAPTURE)) {
                $firstOptPos = $matchA[0][1];
            }

            if ($firstOptPos > 0) {
                $questionText = trim(substr($chunk, 0, $firstOptPos));
            } else {
                $questionText = trim($chunk);
            }
        }

        // Clean leading question numbering (e.g. "**1. Characteristic Polynomial...**", "1.", "Q1. ", "Question 4(b):", "### Question 1:", "Ex. 12:", Bengali numerals)
        // 1. Remove leading horizontal dividers
        $questionText = preg_replace('/^(?:[\-\*_]{3,}\s*)+/u', '', $questionText);

        // 2. Markdown bold/italic header where number and title are wrapped together:
        // e.g. **1. Characteristic Polynomial and Matrix Trace**
        $questionText = preg_replace_callback('/^(\*\*|\*)(?:#+\s*)?(?:(?:Q(?:uestion)?|Ex(?:ample)?|Prob(?:lem)?)\s*[\.:\-]?\s*)?[0-9০-৯]{1,4}(?:\([a-zA-Z0-9]+\))?[\.:\-]\s*(.+?)\1(?:\r?\n+|$)/ui', function($m) {
            $title = trim($m[2]);
            return $title !== '' ? "**{$title}**\n\n" : '';
        }, $questionText);

        // 3. Normal question number prefix (including bold numbers like **1.** or **Question 1:** or 1. or ### 1.)
        $questionText = preg_replace('/^(?:#+\s*)?(?:\*{0,2}|_{0,2})(?:(?:Q(?:uestion)?|Ex(?:ample)?|Prob(?:lem)?)\s*[\.:\-]?\s*)?[0-9০-৯]{1,4}(?:\([a-zA-Z0-9]+\))?(?:[:.\-=]?\*{0,2}[:.\-=]?)\s*/ui', '', $questionText);
        $questionText = trim($questionText);

        // Restore LaTeX math placeholders into question text, options, and explanation
        if (!empty($latexPlaceholders)) {
            $phKeys = array_keys($latexPlaceholders);
            $phVals = array_values($latexPlaceholders);
            $questionText = str_replace($phKeys, $phVals, $questionText);
            $optionA = str_replace($phKeys, $phVals, $optionA);
            $optionB = str_replace($phKeys, $phVals, $optionB);
            $optionC = str_replace($phKeys, $phVals, $optionC);
            $optionD = str_replace($phKeys, $phVals, $optionD);
            if ($explanation !== null) {
                $explanation = str_replace($phKeys, $phVals, $explanation);
            }
        }

        // Validation checks
        $errors = [];
        if ($questionText === '') {
            $errors[] = 'Missing question text';
        }
        if ($optionA === '' || $optionB === '') {
            $errors[] = 'Options A or B are incomplete';
        }
        if (empty($correctAnswer)) {
            $errors[] = 'Missing correct answer key';
        }

        return [
            'index' => $index,
            'question_text' => $questionText,
            'option_a' => $optionA,
            'option_b' => $optionB,
            'option_c' => ($optionC !== '' ? $optionC : 'None of the above'),
            'option_d' => ($optionD !== '' ? $optionD : 'All of the above'),
            'correct_answer' => $correctAnswer, // null if missing
            'explanation' => $explanation,
            'is_valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Synthesize STEM content into valid KaTeX / MathJax expressions.
     * Converts bracket matrices like [[1, 2], [3, 4]] into $\begin{pmatrix} 1 & 2 \\ 3 & 4 \end{pmatrix}$,
     * converts bare Unicode Greek characters into LaTeX expressions,
     * and wraps bare math commands in $...$ WITHOUT nesting or breaking existing valid LaTeX.
     */
    public static function synthesizeStemLatex(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // 1. Bracket Matrices: [[1, 2], [3, 4]] -> $\begin{pmatrix} 1 & 2 \\ 3 & 4 \end{pmatrix}$
        // Handle optional outer $ or $$ so we don't nest delimiters
        $matrixPattern = '/(?:\$)?\[\s*(\[\s*[^\[\]]+?\s*\](?:\s*,\s*\[\s*[^\[\]]+?\s*\])*)\s*\](?:\$)?/u';
        $text = preg_replace_callback($matrixPattern, function ($matches) {
            $inner = $matches[1];
            if (preg_match_all('/\[\s*([^\[\]]+?)\s*\]/u', $inner, $rowMatches)) {
                $rows = [];
                foreach ($rowMatches[1] as $rowContent) {
                    $elements = array_map('trim', explode(',', $rowContent));
                    $rows[] = implode(' & ', $elements);
                }
                $latexRows = implode(' \\\\ ', $rows);
                return '$\\begin{pmatrix} ' . $latexRows . ' \\end{pmatrix}$';
            }
            return $matches[0];
        }, $text);

        // 2. CRITICAL STEP: Extract all valid LaTeX math regions to protect them from double-wrapping
        $mathPlaceholders = [];
        $phIndex = 0;

        // Display math $$...$$
        $text = preg_replace_callback('/\$\$([\s\S]+?)\$\$/u', function($m) use (&$mathPlaceholders, &$phIndex) {
            $ph = '__STEMMATHPH_' . ($phIndex++) . '__';
            $mathPlaceholders[$ph] = $m[0];
            return $ph;
        }, $text);

        // Display math \[...\]
        $text = preg_replace_callback('/\\\\\[([\s\S]+?)\\\\\]/u', function($m) use (&$mathPlaceholders, &$phIndex) {
            $ph = '__STEMMATHPH_' . ($phIndex++) . '__';
            $mathPlaceholders[$ph] = $m[0];
            return $ph;
        }, $text);

        // Inline math \(...\)
        $text = preg_replace_callback('/\\\\\(([\s\S]+?)\\\\\)/u', function($m) use (&$mathPlaceholders, &$phIndex) {
            $ph = '__STEMMATHPH_' . ($phIndex++) . '__';
            $mathPlaceholders[$ph] = $m[0];
            return $ph;
        }, $text);

        // Inline math $...$
        $text = preg_replace_callback('/\$([^\$]+?)\$/u', function($m) use (&$mathPlaceholders, &$phIndex) {
            $ph = '__STEMMATHPH_' . ($phIndex++) . '__';
            $mathPlaceholders[$ph] = $m[0];
            return $ph;
        }, $text);

        // Standalone environments \begin{...}...\end{...} on their own line -> display math $$...$$
        $text = preg_replace_callback('/(\n\s*|^)\\\\begin\{([a-zA-Z*]+)\}([\s\S]*?)\\\\end\{\2\}(\s*\n|$)/u', function($m) use (&$mathPlaceholders, &$phIndex) {
            $ph = '__STEMMATHPH_' . ($phIndex++) . '__';
            $mathPlaceholders[$ph] = "\n$$\\begin{" . $m[2] . "}" . $m[3] . "\\end{" . $m[2] . "}$$\n";
            return $ph;
        }, $text);

        // Any remaining inline \begin{...}...\end{...} outside math mode -> inline math $...$
        $text = preg_replace_callback('/\\\\begin\{([a-zA-Z*]+)\}[\s\S]*?\\\\end\{\1\}/u', function($m) use (&$mathPlaceholders, &$phIndex) {
            $ph = '__STEMMATHPH_' . ($phIndex++) . '__';
            $mathPlaceholders[$ph] = '$' . $m[0] . '$';
            return $ph;
        }, $text);

        // 3. Map raw Unicode Greek letters & symbols in OUTSIDE text to LaTeX
        $unicodeMap = [
            'α' => '$\\alpha$', 'β' => '$\\beta$', 'γ' => '$\\gamma$', 'δ' => '$\\delta$',
            'ε' => '$\\epsilon$', 'ζ' => '$\\zeta$', 'η' => '$\\eta$', 'θ' => '$\\theta$',
            'ι' => '$\\iota$', 'κ' => '$\\kappa$', 'λ' => '$\\lambda$', 'μ' => '$\\mu$',
            'ν' => '$\\nu$', 'ξ' => '$\\xi$', 'π' => '$\\pi$', 'ρ' => '$\\rho$',
            'σ' => '$\\sigma$', 'τ' => '$\\tau$', 'υ' => '$\\upsilon$', 'φ' => '$\\phi$',
            'χ' => '$\\chi$', 'ψ' => '$\\psi$', 'ω' => '$\\omega$',
            'Γ' => '$\\Gamma$', 'Δ' => '$\\Delta$', 'Θ' => '$\\Theta$', 'Λ' => '$\\Lambda$',
            'Ξ' => '$\\Xi$', 'Π' => '$\\Pi$', 'Σ' => '$\\Sigma$', 'Φ' => '$\\Phi$',
            'Ψ' => '$\\Psi$', 'Ω' => '$\\Omega$',
            '∞' => '$\\infty$', '≠' => '$\\ne$', '≤' => '$\\le$', '≥' => '$\\ge$',
            '±' => '$\\pm$', '×' => '$\\times$', '÷' => '$\\div$', '·' => '$\\cdot$',
            '√' => '$\\sqrt{}$'
        ];
        $text = strtr($text, $unicodeMap);

        // 4. Wrap bare LaTeX commands OUTSIDE math mode in $...$
        // Complex commands with arguments
        $text = preg_replace_callback('/(?<![\$\\\])\\\\(operatorname\{[^{}]+\}|frac\{[^{}]+\}\{[^{}]+\}|sqrt(?:\[[^\]]+\])?\{[^{}]+\}|vec\{[^{}]+\}|hat\{[^{}]+\}|dot\{[^{}]+\}|overline\{[^{}]+\}|mathbf\{[^{}]+\})(?!\$)/u', function($m) {
            return '$' . $m[0] . '$';
        }, $text);

        // Greek symbols, linear algebra, and common math operators
        $greekOrSymbols = 'lambda|alpha|beta|gamma|delta|epsilon|zeta|eta|theta|iota|kappa|mu|nu|xi|pi|rho|sigma|tau|upsilon|phi|chi|psi|omega|Delta|Gamma|Theta|Lambda|Xi|Pi|Sigma|Phi|Psi|Omega|nabla|partial|infty|approx|ne|leq|geq|times|div|pm|mp|cdot|int|oint|sum|prod|cup|cap|subset|subseteq|in|notin|forall|exists|det|tr|dim|ker|deg|gcd|max|min|lim|sin|cos|tan|sec|csc|cot|sinh|cosh|tanh|ln|log|exp';
        $text = preg_replace_callback('/(?<![\$\\\])\\\\(' . $greekOrSymbols . ')(?:[_^](?:\{[^{}]+\}|[a-zA-Z0-9]+))*(?![a-zA-Z])(?!\$)/u', function($m) {
            return '$' . $m[0] . '$';
        }, $text);

        // 5. Restore protected math blocks
        foreach ($mathPlaceholders as $ph => $original) {
            // Also normalize any raw Unicode inside the math block itself (e.g. λ inside $...$ -> \lambda)
            $innerUnicodeMap = [
                'α' => '\\alpha ', 'β' => '\\beta ', 'γ' => '\\gamma ', 'δ' => '\\delta ',
                'ε' => '\\epsilon ', 'ζ' => '\\zeta ', 'η' => '\\eta ', 'θ' => '\\theta ',
                'ι' => '\\iota ', 'κ' => '\\kappa ', 'λ' => '\\lambda ', 'μ' => '\\mu ',
                'ν' => '\\nu ', 'ξ' => '\\xi ', 'π' => '\\pi ', 'ρ' => '\\rho ',
                'σ' => '\\sigma ', 'τ' => '\\tau ', 'υ' => '\\upsilon ', 'φ' => '\\phi ',
                'χ' => '\\chi ', 'ψ' => '\\psi ', 'ω' => '\\omega ',
                'Γ' => '\\Gamma ', 'Δ' => '\\Delta ', 'Θ' => '\\Theta ', 'Λ' => '\\Lambda ',
                'Ξ' => '\\Xi ', 'Π' => '\\Pi ', 'Σ' => '\\Sigma ', 'Φ' => '\\Phi ',
                'Ψ' => '\\Psi ', 'Ω' => '\\Omega ',
                '∞' => '\\infty ', '≠' => '\\ne ', '≤' => '\\le ', '≥' => '\\ge ',
                '±' => '\\pm ', '×' => '\\times ', '÷' => '\\div ', '·' => '\\cdot '
            ];
            $cleanOriginal = strtr($original, $innerUnicodeMap);
            $text = str_replace($ph, $cleanOriginal, $text);
        }

        return $text;
    }

    /**
     * Render STEM content with styled Diagram specifications and KaTeX/MathJax compatibility.
     */
    public static function renderStemContent(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        // 1. Synthesize bracket matrices and bare LaTeX symbols if present
        $text = self::synthesizeStemLatex($text);

        // 2. Modernize math operator typography before opening delimiters: \det( -> \det\,(
        $text = preg_replace('/\\\\(det|operatorname\{[^{}]+\}|dim|ker|deg|gcd|max|min|sup|inf|lim|limsup|liminf|ln|lg|log|exp|sin|cos|tan|sec|csc|cot|sinh|cosh|tanh)\s*(\(|\[|\\\{)/u', '\\\$1\\,$2', $text);

        // 3. Transform [Diagram: ...] into a dedicated visual indicator
        $text = preg_replace_callback('/\[Diagram:\s*([^\]]+)\]/i', function ($matches) {
            $desc = htmlspecialchars(trim($matches[1]), ENT_QUOTES, 'UTF-8');
            return '<div class="stem-diagram-callout" style="display: flex; align-items: flex-start; gap: 9px; margin: 10px 0; background: rgba(59, 130, 246, 0.1); border: 1px dashed rgba(59, 130, 246, 0.4); border-radius: 8px; padding: 10px 14px; color: #93c5fd; font-size: 12.5px; line-height: 1.5;"><i class="fa-solid fa-bezier-curve" style="color: #60a5fa; margin-top: 3px; font-size: 14px;"></i><div><strong style="color: #bfdbfe;">[Diagram Specification]:</strong> ' . $desc . '</div></div>';
        }, $text);

        // 4. Protect existing math blocks before markdown/newline transformation
        $mathHolders = [];
        $mhIndex = 0;
        $text = preg_replace_callback('/(\$\$[\s\S]+?\$\$|\\\\\[[\s\S]+?\\\\\]|\\\\\([\s\S]+?\\\\\)|\$[^\$]+?\$)/u', function($m) use (&$mathHolders, &$mhIndex) {
            $key = '___MATH_HLD_' . ($mhIndex++) . '___';
            $mathHolders[$key] = $m[0];
            return $key;
        }, $text);

        // 5. Convert leading/block Markdown bold **Heading** to distinct styled question title
        $text = preg_replace('/(?:^|\n)\s*\*\*([^\*]+?)\*\*(?:\s*(?:\n|$))/u', '<strong style="font-weight: 800; color: #0f172a; font-size: 1.06em; display: block; margin-top: 2px; margin-bottom: 8px; letter-spacing: -0.01em;">$1</strong>', $text);

        // Convert any remaining inline Markdown bold **text** to styled bold
        $text = preg_replace('/\*\*([^\*]+?)\*\*/u', '<strong style="font-weight: 700; color: inherit;">$1</strong>', $text);

        // 6. Convert newlines
        $text = trim($text);
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        // 2 or more newlines -> clean paragraph spacer
        $text = preg_replace('/\n{2,}/u', '<div style="margin-bottom: 12px;"></div>', $text);
        // Single newline -> <br>
        $text = preg_replace('/\n/u', '<br>', $text);

        // 7. Restore protected math blocks
        foreach ($mathHolders as $key => $val) {
            $text = str_replace($key, $val, $text);
        }

        return $text;
    }
}

