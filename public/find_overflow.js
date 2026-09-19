
const over = [];
document.querySelectorAll('*').forEach(el => {
  const r = el.getBoundingClientRect();
  if (r.right > window.innerWidth + 2 || el.scrollWidth > window.innerWidth + 2) {
    over.push({
      tag: el.tagName,
      id: el.id,
      className: el.className,
      rectRight: Math.round(r.right),
      width: Math.round(r.width),
      scrollWidth: el.scrollWidth,
      text: (el.innerText || '').slice(0, 30).replace(/\n/g, ' ')
    });
  }
});
fetch('/ida/public/save_nav_debug.php', { method: 'POST', body: JSON.stringify(over, null, 2) });
