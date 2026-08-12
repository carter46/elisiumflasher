# Design System Specification: The Fluid Precision Framework

## 1. Overview & Creative North Star: "The Digital Curator"
This design system rejects the "boxed-in" nature of traditional fintech. Our Creative North Star is **The Digital Curator**: an experience that feels like a high-end editorial gallery rather than a ledger. We achieve this through **Atmospheric Depth**—using light, glass, and tonal shifts to guide the eye instead of rigid borders and heavy lines.

The "template" look is broken through:
*   **Intentional Asymmetry:** Hero elements and data visualizations should bleed off-canvas or sit off-center to create a sense of scale.
*   **Tonal Layering:** Replacing 1px strokes with background color transitions to define "zones."
*   **Sophisticated Contrast:** Pairing oversized `display-lg` typography with generous `20` (5rem) white space.

---

## 2. Colors & Surface Architecture

### The Palette
We utilize a primary system centered on the local Nigeria transfer experience.

*   **Local/Naira (Primary accent):** `secondary` (#006c49) and `secondary_container` (#6cf8bb). These emerald tones represent growth and local vitality. Legacy `primary` (#4648d4) tokens may still appear in older assets but are not used for a separate international flow.
*   **Neutral Foundation:** `surface` (#faf8ff) for the canvas and `on_surface` (#131b2e) for high-contrast, slate-toned legibility.

### The "No-Line" Rule
**Explicit Instruction:** Designers are prohibited from using 1px solid borders to section content. Boundaries must be defined by:
1.  **Background Shifts:** Placing a `surface_container_lowest` (#ffffff) card atop a `surface_container` (#eaedff) background.
2.  **Tonal Transitions:** Using the spacing scale (e.g., `8` / 2rem) to create clear mental models of separation.

### The "Glass & Gradient" Rule
To capture the premium essence of high-end fintech:
*   **Signature Gradients:** Use a linear gradient (135°) from `primary` to `tertiary` for hero CTAs.
*   **Glassmorphism:** For floating modals or navigation bars, use `surface_container_lowest` at 70% opacity with a `24px` backdrop-blur. This allows the vibrant "brand glows" to bleed through the interface.

---

## 3. Typography: Editorial Authority
We use **Inter** as our typographic backbone. It is engineered for numerical clarity and UI legibility.

| Level | Size | Weight | Use Case |
| :--- | :--- | :--- | :--- |
| **display-lg** | 3.5rem | 700 | Large balance displays or hero statements. |
| **headline-md** | 1.75rem | 600 | Page headers and section starts. |
| **title-sm** | 1rem | 500 | Card titles and sub-headers. |
| **body-md** | 0.875rem | 400 | Standard transaction details and descriptions. |
| **label-sm** | 0.6875rem | 600 | All-caps utility text (e.g., "PENDING", "USD"). |

**The Hierarchy Principle:** Always pair a `display-lg` element with a `body-md` element nearby. The extreme jump in scale creates the "Editorial" feel found in premium finance apps.

---

## 4. Elevation & Depth: Atmospheric Layering

### The Layering Principle (Z-Axis)
Depth is achieved through "Tonal Stacking" rather than structural shadows:
1.  **Level 0 (Floor):** `surface` (#faf8ff)
2.  **Level 1 (Sections):** `surface_container_low` (#f2f3ff)
3.  **Level 2 (Cards):** `surface_container_lowest` (#ffffff)
4.  **Level 3 (Pop-overs):** `surface_bright` with Glassmorphism.

### Ambient Shadows
When a physical lift is required (e.g., a floating Action Button), use an **Ambient Shadow**:
*   **Color:** Use `on_surface` at 6% opacity.
*   **Blur:** `32px` to `48px`.
*   **Y-Offset:** `16px`.
*   *Note:* The shadow should feel like a soft glow of light being blocked, not a dark smudge.

### The "Ghost Border" Fallback
If a container sits on a background of the same color (accessibility requirement), use a **Ghost Border**: `outline_variant` at **15% opacity**. Never use a 100% opaque border.

---

## 5. Components

### Buttons
*   **Primary:** Linear gradient (`primary` to `primary_container`), `xl` (1.5rem) border radius, `body-lg` medium weight text.
*   **Secondary:** `surface_container_high` background with `on_surface` text. No border.
*   **Tertiary:** Transparent background, `primary` text color, used for low-priority actions.

### Minimalist Inputs
*   **Style:** No background fill. Only a bottom "Ghost Border" using `outline_variant`.
*   **Focus State:** The bottom border transitions to `primary` (2px thickness), and the label floats upward using `label-md` styling.

### Cards & Lists
*   **Radius:** Always use `xl` (1.5rem) or `lg` (1rem).
*   **Separation:** **Prohibit divider lines.** Use vertical white space `4` (1rem) or `5` (1.25rem) to separate list items. Use a `surface_container_low` background on hover to indicate interactivity.

### Featured Component: The "Vault Card"
A signature element for this system. A `surface_container_highest` card with a subtle `10%` opacity radial gradient of `secondary_fixed` in the top-right corner, creating a "metallic" sheen for premium account tiers.

---

## 6. Do’s and Don’ts

### Do
*   **DO** use the 8px grid religiously. Every gap should be a multiple of 8 (`2`, `4`, `8`, `12`).
*   **DO** use `full` (9999px) border radius for chips and small tags to contrast with the `xl` card corners.
*   **DO** leave "air" in the design. If a screen feels crowded, increase the spacing to the next tier in the scale.

### Don’t
*   **DON'T** use pure black (#000000). Use `on_surface` (#131b2e) for all deep tones.
*   **DON'T** use 1px dividers to separate transactions. Use white space and typography weight instead.
*   **DON'T** use standard "drop shadows." Only use the Ambient Shadow specification for floating elements.
*   **DON'T** mix unrelated accent colors in the same flow. Prefer the local emerald secondary tones for transfer UI.