# IDA Academy: Platform Design Guidelines & UI Standards

These design standards must be maintained across all current and future views, admin panels, cadet portals, and components.

---

## 1. Color Palette (Strict 2–3 Color System)

| Role | Hex Code | Description & Usage |
| :--- | :--- | :--- |
| **Card Surface** | `#181c26` | Primary background for action cards, panels, and modal boxes. |
| **Deep Background** | `#0f1218` / `#131620` | Page background, table backgrounds, and outer canvas. |
| **Recessed Inset Tile** | `#11141c` | Sunken squircle background for icon containers. |
| **Primary Brand Accent** | `#ff5757` | IDA Brand Coral/Red for icons, active tabs, buttons, and highlights. |
| **Primary Text** | `#ffffff` | Headings, card titles, and emphasized metrics. |
| **Muted Slate Subtext** | `#717d96` / `#8c96a8` | Paragraph descriptions, card metrics, and helper captions. |
| **Section Trackers** | `#64748b` | Uppercase category captions (`QUICK ACTIONS`, `WING DIRECTORIES`). |
| **Borders & Dividers** | `rgba(255, 255, 255, 0.05)` | Hairline dividers and card borders. |

> **IMPORTANT**: Avoid introducing arbitrary vibrant colors (cyan, lime, neon purple, yellow). All elements should harmonize within this dark slate and coral palette.

---

## 2. Card Components & Shapes

### A. Action Cards (`.dark-action-card`, `.simple-box`)
- **Border Radius**: `22px`
- **Background**: `#181c26`
- **Border**: `1px solid rgba(255, 255, 255, 0.05)`
- **Padding**: `34px 24px 28px`
- **Box Shadow**: `0 8px 24px rgba(0, 0, 0, 0.25)`
- **Hover State**:
  - `transform: translateY(-5px);`
  - `border-color: rgba(255, 87, 87, 0.35);`
  - `box-shadow: 0 16px 35px -8px rgba(0, 0, 0, 0.45), 0 0 24px rgba(255, 87, 87, 0.12);`

### B. Inset Squircle Icon Container (`.dark-action-icon-wrap`)
- **Dimensions**: `58px × 58px`
- **Border Radius**: `18px` (Squircle / rounded rectangle, never a plain circle)
- **Background**: `#11141c`
- **Box Shadow**: `inset 0 2px 5px rgba(0, 0, 0, 0.5), 0 2px 6px rgba(0, 0, 0, 0.25)`
- **Icon Color**: `#ff5757`
- **Icon Size**: `20px` (FontAwesome outline or standard icon)
- **Hover State**:
  - `transform: scale(1.08);`
  - `background: #ff5757;`
  - `color: #ffffff;`
  - `box-shadow: 0 0 20px rgba(255, 87, 87, 0.4);`

### C. Typography
- **Section Caption**: `font-size: 11.5px; font-weight: 800; letter-spacing: 1.2px; color: #64748b; text-transform: uppercase;`
- **Card Title (`h3`)**: `font-size: 18px; font-weight: 800; color: #ffffff; font-family: 'Poppins', sans-serif; margin-bottom: 6px;`
- **Card Subtext (`p`)**: `font-size: 12.5px; color: #717d96; line-height: 1.45; margin: 0;`

---

## 3. Layout Conventions
- **3-Row Command Layout**:
  - Row 1: 2 cards
  - Row 2: 2 cards
  - Row 3: 1 card (centered, identical card proportions)
- **Max Width**: Centered container `max-width: 820px; margin: 0 auto;`
