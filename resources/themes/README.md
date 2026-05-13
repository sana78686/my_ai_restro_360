# AiRestro360 Themes

Multi-theme architecture for tenant storefronts. Each theme is a self-contained UI package.

## Available Themes

| Theme | Style | Best For | Status |
|-------|-------|----------|--------|
| `classic` | Traditional, warm red tones | General restaurants, cafes | Skeleton |
| `modern` | Bold, dark, sharp corners | Trendy eateries, bars | Skeleton |
| `minimal` | Clean, lots of whitespace | Fine dining, organic | Skeleton |
| `blaze` | Fast-food inspired, sticky cart | QSR, fried chicken, burgers | **Complete** |

## Theme Selection

Tenants can select their theme via:
- **Dashboard**: Website Settings > Layout > Choose template
- **API**: `PUT /tenant/website-settings` with `{ "theme": "blaze" }`
- **Database**: `UPDATE settings SET theme = 'blaze'`

## Dynamic Settings

All themes share the same website settings. Changes in the dashboard apply globally:
- Business name, tagline, description
- Logo, favicon
- Colors (primary, secondary, accent)
- Hero section (headline, image)
- Social links
- SEO settings
- Section visibility

## Structure

```
themes/
├── classic/          # Default theme (traditional design)
│   ├── main.js       # Theme entry point
│   ├── router.js     # Theme-specific routes
│   ├── layouts/      # Header, footer, page shells
│   ├── pages/        # Landing, Menu, Checkout, etc.
│   └── assets/
│       └── tokens.css  # Design tokens (colors, fonts, spacing)
│
├── modern/           # Modern theme (bold, full-bleed)
│   └── ...
│
├── minimal/          # Minimal theme (clean, whitespace)
│   └── ...
│
├── blaze/            # Fast-food theme with sticky cart
│   ├── PLAN.md       # Implementation plan & static/dynamic breakdown
│   ├── main.js
│   ├── router.js
│   ├── layouts/
│   │   └── TenantFrontendLayout.vue  # Header + mobile drawer + footer
│   ├── pages/
│   │   ├── Landing.vue   # Hero slider, categories, best sellers, deals
│   │   └── Menu.vue      # Category tabs + products + sticky cart sidebar
│   └── assets/
│       └── tokens.css
│
└── README.md
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
