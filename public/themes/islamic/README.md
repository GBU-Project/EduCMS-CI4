# EduCMS Theme Islamic v1.0

Official flagship theme for **EduCMS Core v1.0.0** designed specifically for Islamic educational institutions, Pondok Pesantren, Tahfidz Centers, Madrasah (MI/MTs/MA), and Islamic Foundations (SDIT/SMPIT/SMAIT).

---

## 🌟 Visual Identity & Key Features

- **Primary Color Palette**: Emerald Green (`#047857`), Forest Green (`#064e3b`), Gold Accent (`#d97706`), Soft Slate (`#f8fafc`).
- **Islamic Visual Motifs**: Geometric pattern overlays, card elevation, modern typography (`Plus Jakarta Sans` & `Amiri` for Arabic text).
- **Responsive Layouts**: Desktop, Tablet, and Mobile viewport support.
- **Reference Architecture**: Built using a modular component structure (`views/components/`) and universal CMS logic with zero core modifications or database dependencies.

---

## 📂 Folder & Asset Structure

```
themes/islamic/
├── theme.json                          # Theme metadata & specification
├── README.md                           # Documentation overview
├── INSTALL.md                          # Activation & setup guide
├── CUSTOMIZATION.md                    # Theme customization manual
├── CHANGELOG.md                        # Version history
├── preview.png                         # Admin theme switcher thumbnail
├── assets/
│   ├── css/theme-islamic.css           # Core styling & HSL variables
│   ├── js/theme-islamic.js            # Micro-interactions & slider JS
│   ├── images/                         # Default fallback images
│   └── patterns/                       # SVG geometric pattern backgrounds
└── views/
    ├── partials/
    │   ├── header.php                  # SEO & Meta partial
    │   ├── navbar.php                  # Dynamic Header Menu & Top contact bar
    │   └── footer.php                  # Theme Footer & Social icons
    ├── components/
    │   ├── hero.php                    # Hero Slider component
    │   ├── welcome.php                 # Principal Welcome section
    │   ├── vision.php                  # Vision & Mission component
    │   ├── programs.php                # Ekstrakurikuler / Programs component
    │   ├── stats.php                   # Statistics Counter bar
    │   ├── news-card.php               # Reusable news card
    │   ├── agenda-card.php             # Reusable agenda card
    │   ├── gallery-card.php            # Reusable photo album card
    │   ├── video-card.php              # Reusable video card
    │   └── cta.php                     # Call to action card (PPDB)
    └── home.php                        # Modular homepage view assembly
```

---

## 🔒 Compliance & Compatibility

- **EduCMS Core Version**: `v1.0.0`
- **Database Migrations**: None (0 database changes)
- **External Dependencies**: Standard Tailwind typography, Lucide Icons, Google Fonts
- **Core Controller Overrides**: None (100% compliant with locked core policy)
