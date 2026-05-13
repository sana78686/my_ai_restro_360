# Blaze Theme - Implementation Plan

## Theme Overview
**Name:** Blaze  
**Style:** Bold fast-food inspired, energetic, red/black/white palette  
**Best For:** Fast food, fried chicken, burgers, pizza, quick-service restaurants

---

## Implementation Status

### COMPLETED
- [x] Theme structure (main.js, router.js, tokens.css)
- [x] Layout with header, mobile drawer, footer
- [x] Landing page with all sections
- [x] Menu page with category tabs and sticky cart
- [x] Responsive design for all devices
- [x] Dynamic banners/sliders from database (S3 storage)
- [x] Dynamic categories from API
- [x] Dynamic best sellers from API
- [x] Website settings API integration
- [x] Color customization via website settings
- [x] Banner CRUD in tenant dashboard

### STILL STATIC (Hardcoded)
- [ ] Top deals section (needs `deals` table)
- [ ] Promotional banners in landing (needs additional banner positions)
- [ ] Day/Night theme toggle
- [ ] Time-based menu (midnight specials)

---

## Color Palette
| Token | Value | Usage |
|-------|-------|-------|
| Primary | `#E31837` | CTAs, buttons, badges (customizable) |
| Primary Dark | `#B81430` | Hover states |
| Secondary | `#1A1A1A` | Headers, text (customizable) |
| Accent | `#FFD700` | Highlights, sale badges (customizable) |
| Background | `#F5F5F5` | Page background |

---

## Data Flow

### Website Settings (Dynamic)
Settings are fetched from `/tenant/website-settings` and apply to all themes:

```
settings table
├── theme (classic, modern, minimal, blaze)
├── business_name, tagline, description
├── logo, favicon
├── hero_headline, hero_subheadline, hero_image
├── primary_color, secondary_color, accent_color
├── meta_title, meta_description, meta_keywords
├── facebook_link, instagram_link, twitter_link, etc.
├── show_cookie_banner, maintenance_mode
└── visible_sections (JSON)
```

### Banners (Dynamic - S3)
Banners are managed via `/tenant/banners` and stored in S3:

```
banners table
├── title, subtitle, description
├── image (S3 path)
├── cta_text, cta_link
├── price, badge
├── position (hero, promo, deal)
├── sort_order
├── is_active
└── starts_at, ends_at
```

---

## Page Sections Breakdown

### 1. Header (Layout Component)
| Element | Status | Notes |
|---------|--------|-------|
| Logo | **DYNAMIC** | From tenant settings |
| Delivery/Pickup/Dine-in tabs | Static UI | Functionality available |
| Search icon | Static UI | |
| Cart icon + count | **DYNAMIC** | From localStorage cart |
| Login button | **DYNAMIC** | Auth state |

### 2. Landing Page Sections

#### 2.1 Hero Slider
| Element | Status | Data Source |
|---------|--------|-------------|
| Slides container | Static UI | |
| Slide images | **DYNAMIC** | `banners` table (S3) |
| Slide text/CTA | **DYNAMIC** | `banners` table |
| Navigation dots | Static UI | |

#### 2.2 Explore Menu (Category Icons)
| Element | Status | Data Source |
|---------|--------|-------------|
| Section title | Static | "Explore Menu" |
| Category icons | **DYNAMIC** | `categories` table |
| Category names | **DYNAMIC** | `categories` table |

#### 2.3 Best Sellers Section
| Element | Status | Data Source |
|---------|--------|-------------|
| Section title | Static | "Best Sellers" |
| Product cards | **DYNAMIC** | `products` where `is_featured=1` |

#### 2.4 Top Deals Section
| Element | Status | Data Source |
|---------|--------|-------------|
| Section title | Static | "Top Deals" |
| Deal cards | **STATIC** | Hardcoded (needs `deals` table) |

#### 2.5 Promotional Banners
| Element | Status | Data Source |
|---------|--------|-------------|
| Banner cards | **STATIC** | Hardcoded (can use `banners` position='promo') |

### 3. Menu Page

#### 3.1 Category Tabs (Horizontal)
| Element | Status | Data Source |
|---------|--------|-------------|
| Tab items | **DYNAMIC** | `categories` table |
| Active state | Dynamic | Route/selection |

#### 3.2 Products Grid
| Element | Status | Data Source |
|---------|--------|-------------|
| Product cards | **DYNAMIC** | `products` by category |

#### 3.3 Sticky Cart Sidebar
| Element | Status | Data Source |
|---------|--------|-------------|
| Cart items | **DYNAMIC** | localStorage cart |
| Total price | **DYNAMIC** | Calculated |

### 4. Mobile Navigation Drawer
| Element | Status | Data Source |
|---------|--------|-------------|
| Login button | **DYNAMIC** | Auth state |
| Day/Night toggle | Static UI | Not functional yet |
| Navigation links | Static | Hardcoded routes |

---

## API Endpoints Used

### Public (No Auth)
- `GET /tenant/website-settings` - Website configuration
- `GET /tenant/banners?position=hero&active_only=true` - Active hero banners
- `GET /tenant/public/categories` - Menu categories
- `GET /tenant/public/products` - Products (with filters)

### Dashboard (Auth Required)
- `GET /tenant/website-settings/themes` - Available themes
- `PUT /tenant/website-settings` - Update settings
- `POST /tenant/website-settings/hero-image` - Upload hero image
- `POST /tenant/website-settings/favicon` - Upload favicon
- `GET/POST/PUT/DELETE /tenant/banners` - Banner CRUD
- `POST /tenant/banners/reorder` - Reorder banners

---

## File Structure
```
themes/blaze/
├── main.js              # Theme entry point
├── router.js            # Theme routes
├── PLAN.md              # This file
├── assets/
│   └── tokens.css       # Design tokens (colors, spacing)
├── layouts/
│   └── TenantFrontendLayout.vue  # Header + drawer + footer
└── pages/
    ├── Landing.vue      # Homepage with all sections
    └── Menu.vue         # Menu with sticky cart
```

---

## To Enable This Theme

1. Run migrations:
   ```bash
   php artisan migrate
   ```

2. Set tenant's theme in database:
   ```sql
   UPDATE settings SET theme = 'blaze' WHERE id = 1;
   ```
   
   Or via Dashboard: Website Settings > Layout > Select "Fast food style"

3. Add banners via Dashboard: Website Settings > Home hero > Add Banner

---

## Future Enhancements

1. **Deals Table**: Create `deals` table for Top Deals section
2. **Promo Banners**: Use `banners` with position='promo' for promotional section
3. **Day/Night Mode**: Implement theme toggle with CSS variables
4. **Time-Based Menu**: Filter categories/products by time (midnight deals)
5. **Testimonials**: Add customer reviews section
6. **Gallery**: Photo gallery from S3
