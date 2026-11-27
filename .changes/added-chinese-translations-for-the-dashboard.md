---
type: added
---
Added Chinese translations for the `Dashboard` and `Server` pages

- Wrapped hardcoded English UI strings in `__()` calls on the Dashboard and all Server pages, reusing the fork's namespaced keys where the English matches and adding the rest as JSON keys
- Added 1314 new keys to `lang/en.json` and `lang/zh-cn.json` (2554 keys each, in sync)
