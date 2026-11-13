---
name: Institutional Ledger & Rails
colors:
  surface: '#fbf9f9'
  surface-dim: '#dbdad9'
  surface-bright: '#fbf9f9'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f5f3f3'
  surface-container: '#efeded'
  surface-container-high: '#e9e8e7'
  surface-container-highest: '#e3e2e2'
  on-surface: '#1b1c1c'
  on-surface-variant: '#3f4940'
  inverse-surface: '#303031'
  inverse-on-surface: '#f2f0f0'
  outline: '#6f7a6f'
  outline-variant: '#becabd'
  surface-tint: '#006d3a'
  primary: '#006837'
  on-primary: '#ffffff'
  primary-container: '#16834a'
  on-primary-container: '#e8ffe9'
  inverse-primary: '#78db98'
  secondary: '#5f5e5e'
  on-secondary: '#ffffff'
  secondary-container: '#e2dfde'
  on-secondary-container: '#636262'
  tertiary: '#595a5a'
  on-tertiary: '#ffffff'
  tertiary-container: '#727272'
  on-tertiary-container: '#faf8f8'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#94f7b2'
  primary-fixed-dim: '#78db98'
  on-primary-fixed: '#00210e'
  on-primary-fixed-variant: '#00522b'
  secondary-fixed: '#e5e2e1'
  secondary-fixed-dim: '#c8c6c5'
  on-secondary-fixed: '#1c1b1b'
  on-secondary-fixed-variant: '#474746'
  tertiary-fixed: '#e4e2e2'
  tertiary-fixed-dim: '#c7c6c6'
  on-tertiary-fixed: '#1b1c1c'
  on-tertiary-fixed-variant: '#464747'
  background: '#fbf9f9'
  on-background: '#1b1c1c'
  surface-variant: '#e3e2e2'
  surface-canvas: '#FFFFFF'
  surface-muted: '#F7F8F7'
  border-subtle: '#E5E5E5'
  text-primary: '#171717'
  text-secondary: '#666666'
  text-muted: '#858585'
  state-success: '#16834A'
  state-success-hover: '#0B6A39'
  state-success-bg: '#EFF8F1'
  state-failed: '#C43D3D'
  state-failed-bg: '#FFF1F1'
  state-pending: '#D97706'
  state-pending-bg: '#FEF3C7'
typography:
  display-lg:
    fontFamily: Geist
    fontSize: 32px
    fontWeight: '600'
    lineHeight: 40px
    letterSpacing: -0.02em
  display-lg-mobile:
    fontFamily: Geist
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Geist
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
    letterSpacing: -0.015em
  headline-sm:
    fontFamily: Geist
    fontSize: 16px
    fontWeight: '600'
    lineHeight: 24px
    letterSpacing: -0.01em
  body-md:
    fontFamily: Geist
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
    letterSpacing: 0em
  body-sm:
    fontFamily: Geist
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 18px
    letterSpacing: 0em
  body-code:
    fontFamily: JetBrains Mono
    fontSize: 12px
    fontWeight: '400'
    lineHeight: 18px
    letterSpacing: 0em
  label-code-strong:
    fontFamily: JetBrains Mono
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.02em
  label-uppercase-xs:
    fontFamily: Geist
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 14px
    letterSpacing: 0.06em
  label-table-header:
    fontFamily: Geist
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 14px
    letterSpacing: 0.05em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-desktop: 1.5rem
  margin: 1rem
  margin-tablet: 1.5rem
  margin-desktop: 2.5rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 0.75rem
  space-lg: 1rem
  space-xl: 1.5rem
  space-2xl: 2.5rem
---

## Brand & Style
This design system defines an institutional financial infrastructure tracking environment modeled on SWIFT, ISO 20022 messaging standards, and central banking payment systems. The brand communicates stability, high throughput, zero tolerance for ambiguity, and absolute auditability. The visual tone eschews consumer fintech ornamentation, glowing neon gradients, and playful soft cards in favor of a rigorous Swiss/International style: razor-sharp alignments, tabular precision, understated status signifiers, and deliberate hierarchy.

Users—primarily compliance officers, treasury managers, settlement operations directors, and infrastructure engineers—require high information density without visual clutter. The interface emphasizes dense legibility, instantaneous scanning of transaction states, and strict structural boundaries.

## Colors
The palette leverages stark clinical clarity: pure `#FFFFFF` primary surface backgrounds paired with `#F7F8F7` for alternating table rows, metadata panels, and inset code/payload previews. Structural containment is established strictly using `#E5E5E5` hairline borders.

Typography is anchored by high-contrast `#171717` for data values, IDs, and primary headers, complemented by `#666666` for field keys and `#858585` for timestamps or non-critical hints. Chromatic accents are restrained strictly to functional telemetry:
- **Verified / Settled / Active**: Institutional Emerald (`#16834A`) on `#EFF8F1` container fills, shifting to `#0B6A39` on interactive hover.
- **Failed / Rejected / Revoked**: Institutional Crimson (`#C43D3D`) on `#FFF1F1`.
- **In-Flight / Processing / Investigation**: Amber (`#D97706`) on `#FEF3C7`.

Color is never applied decoratively; it serves strictly as an informational status flag.

## Typography
Typography is configured to mirror terminal-grade auditing instruments. `Geist` provides structural neutrality for headers, metadata keys, and human-readable narratives, while `JetBrains Mono` handles financial identifiers (UETR, BIC, IBAN, hashes, and ISO timestamps).

- **Data Alignment**: Tabular figures (`font-variant-numeric: tabular-nums`) must be enabled universally across numeric fields, amounts, and latency metrics.
- **Labels & Micro-headers**: Uppercase micro-labels (`label-uppercase-xs` and `label-table-header`) are strictly set in all-caps with positive tracking (`+0.05em` to `+0.06em`) using `#666666` or `#858585`.
- **Identifiers**: Financial tracking IDs (such as 36-character UETR strings) must render using `body-code` or `label-code-strong` to eliminate character confusion (0 vs O, 1 vs I).

## Layout & Spacing
The layout follows a fluid 12-column grid capped at a maximum width of `1600px` for multi-column terminal dashboards, with fixed sidebars (240px or 64px collapsed).

- **Vertical Rhythm**: Layout units scale in multiples of `4px` and `8px`. Dense horizontal layouts prioritize data throughput; vertical padding inside transaction rows and data keys remains tight (`8px` to `10px`).
- **Dividers vs Space**: Instead of wide margins, use crisp `1px` borders (`border-subtle`) to delimit panels, navigation rails, and summary feeds.
- **Breakpoints**:
  - **Mobile (< 768px)**: Single-column stacked cards. Transaction tables convert into key-value definition lists. Margin is `1rem`.
  - **Tablet (768px - 1023px)**: 8-column layout. Split views collapse into tabbed panels. Margin is `1.5rem`.
  - **Desktop (>= 1024px)**: Full 12-column telemetry with split-pane audit inspectors (list on the left 60%, granular message payload inspector on the right 40%). Margin is `2.5rem`.

## Elevation & Depth
Depth is created through low-contrast outlines and tonal layering, completely rejecting skeuomorphic drop-shadows and generic AI SaaS glows.

- **Flat Architecture**: Surfaces sit flush on the `#FFFFFF` canvas or `#F7F8F7` utility bands.
- **Hairline Outlines**: Boundaries are defined by a singular `1px solid #E5E5E5` perimeter border. Hover interactions on table rows or list items switch the background tone to `#F7F8F7` without spatial displacement.
- **Overlay Elevation**: Drawers, inspector flyouts, and command palettes use a subtle boundary shadow: `box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 0 0 1px #E5E5E5`. No ambient diffusion or colored blurs are permitted.

## Shapes
Geometry is restrained and architectural. The default corner radius is `0.25rem` (4px), extending to `0.375rem` (6px) maximum for large panels and modals. 

- **Containers & Tables**: Outer borders of containers and data sheets use `rounded-sm` (4px) or completely square (`0px`) corners to preserve screen real estate and maximize tabular scan lines.
- **Badges & Status Indicators**: Status chips must use a maximum radius of `3px` or `4px`. Pill shapes (full rounded capsules) are explicitly disallowed.
- **Inputs & Action Triggers**: Buttons and form elements strictly share the `0.25rem` radius for consistent alignment along 32px and 36px control heights.

## Components

### Buttons
- **Primary**: Solid `#171717` background with `#FFFFFF` text and `4px` corner radius. Height: `32px` or `36px`. Hover: `#333333`. No shadows.
- **Secondary / Outlined**: `1px solid #E5E5E5`, background `#FFFFFF`, text `#171717`. Hover: `#F7F8F7` with border `#D4D4D4`.
- **Destructive**: Background `#FFF1F1`, border `1px solid #FCA5A5`, text `#C43D3D`.
- **Text / Ghost**: Transparent background, text `#666666`, hover text `#171717`, hover background `#F7F8F7`.

### Status Badges & Chips
- Status tokens are compact tags with `4px` border radius, padding `2px 6px`, and uppercase `10px` or `11px` monospace typography.
- **Completed**: `#EFF8F1` background, `#16834A` text, `1px solid #D1EEDB`.
- **Processing**: `#FEF3C7` background, `#D97706` text, `1px solid #FDE68A`.
- **Failed**: `#FFF1F1` background, `#C43D3D` text, `1px solid #FECACA`.

### Data Tables & Transaction Logs
- Table headers use `label-table-header`, uppercase text in `#666666`, set against a `#F7F8F7` background with a bottom border of `1px solid #E5E5E5`.
- Rows have a fixed height of `40px` (standard) or `32px` (dense), featuring a bottom divider `1px solid #E5E5E5`. Row hover is `#F7F8F7`.
- Monospace strings (UETR, account numbers) are truncated with an ellipsis with an instant inline copy action.

### Input Fields & Search Bars
- Background: `#FFFFFF`. Border: `1px solid #E5E5E5`. Height: `32px` or `36px`. Radius: `4px`.
- Typography: `body-sm` (`13px`). Placeholder text: `#858585`.
- Focus state: `1px solid #171717` with zero outer fuzzy halo.
- Monospace filter inputs: Preceded by micro-labels (e.g., `UETR:`, `BIC:`) in `#858585`.

### Checkboxes & Radio Buttons
- Checkbox: `14px x 14px` square with a `2px` radius. Unchecked border: `1px solid #D4D4D4`. Checked: `#171717` with white checkmark.
- Radio: `14px x 14px` circle. Active dot is `#171717`.

### Cards & Telemetry Panels
- Border: `1px solid #E5E5E5`. Background: `#FFFFFF`. Radius: `4px`.
- Card headers feature an uppercase label, a small divider line, and metric counters rendered in `headline-md` tabular numbers.
- Zero floating shadows or card lifts on hover.