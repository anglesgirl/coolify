---
type: fixed
---

Fixed single-line bound array attributes that could break Blade component compilation

- Converted all 44 single-line `:actions="[__('...')]"` style attributes across 33 Blade templates to multi-line array form, matching the pattern already used elsewhere
- Fixed a stray `]` left after the `:actions` attribute in the server proxy page
