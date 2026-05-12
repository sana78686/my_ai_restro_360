# AiRestro360 Themes

Multi-theme architecture for tenant storefronts. Each theme is a self-contained UI package.

## Structure

```
themes/
├── classic/          # Default theme (current design)
│   ├── main.js       # Theme entry point
│   ├── App.vue       # Theme root component
│   ├── router.js     # Theme-specific routes
│   ├── layouts/      # Header, footer, page shells
│   ├── pages/        # Landing, About, Contact, etc.
│   ├── components/   # Theme-specific UI pieces
│   └── assets/       # Theme CSS, images
│       └── tokens.css  # Design tokens (colors, fonts, spacing)
│
├── modern/           # Modern theme (bold, full-bleed)
│   └── ...
│
├── minimal/          # Minimal theme (clean, whitespace)
│   └── ...
│
└── _base/            # Shared theme utilities (optional)
    └── ...
```

## How it works

1. **Tenant settings** store `theme_slug` (e.g. "classic", "modern", "minimal")
2. **Backend** injects `window.TENANT_THEME` from tenant settings
3. **app.js** dynamically imports the correct theme's `main.js`
4. **Vite** builds all themes; only the active one runs

## Adding a new theme

1. Copy `classic/` as a starting point
2. Rename folder to your theme slug
3. Customize layouts, pages, tokens
4. Theme is auto-discovered by the loader

## Shared code

All themes share from `resources/js/core/`:
- API clients (`core/api/`)
- Composables (`core/composables/`)
- i18n (`core/i18n/`)
- Utilities (`core/utils/`)
- Auth/Cart stores (`core/stores/`)

Themes should NOT duplicate business logic—only presentation.
