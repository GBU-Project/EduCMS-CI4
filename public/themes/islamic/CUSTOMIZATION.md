# EduCMS Theme Islamic - Customization Guide

Learn how to customize the content, branding, colors, and layout of **EduCMS Theme Islamic** using standard CMS modules.

---

## 🎨 Branding & Identity

All branding details are managed dynamically via **Admin > Settings**:

1. **Logo & Favicon**:
   - Navigate to **Admin > Settings > Setelan Umum**.
   - Upload school logo and favicon.

2. **School Name, Address, & Tagline**:
   - Navigate to **Admin > Settings > Data Sekolah**.
   - Update **Nama Sekolah**, **Tagline**, **Kepala Sekolah**, **NPSN**, and **Akreditasi**.

3. **Principal Welcome Speech (Sambutan)**:
   - Edit **Sambutan Kepala Sekolah** inside **Data Sekolah**.

---

## 📌 Navigation Menus

Header and Footer menus are powered by the built-in **Menu Builder**:

1. Navigate to **Admin > Menu Builder**.
2. Select **Header Navigation** or **Footer Navigation**.
3. Reorder or add links (Berita, Agenda, Pengumuman, PPDB, Page links).

---

## 🖼️ Hero Slider & Banners

1. Go to **Admin > Sliders**.
2. Add high-resolution photos along with Title, Subtitle, and Link.
3. If no slider is added, the theme automatically displays an elegant fallback banner with school info.

---

## ⚙️ Custom Colors & Styling

Theme colors are declared as standard CSS custom properties in `themes/islamic/assets/css/theme-islamic.css`:

```css
:root {
    --islamic-emerald-700: #047857; /* Primary Emerald */
    --islamic-forest-900: #064e3b;  /* Dark Forest */
    --islamic-gold-500: #f59e0b;    /* Gold Accent */
}
```

You can modify these HSL/HEX values directly in the CSS file to match your institution's specific branding colors.
