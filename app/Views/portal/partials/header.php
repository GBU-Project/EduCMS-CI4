<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? esc_html($title) : esc_html(site_name() . ' - ' . site_tagline()); ?></title>
    <?php if (site_favicon()): ?>
    <link rel="icon" type="image/x-icon" href="<?php echo esc_attr(site_favicon()); ?>">
    <?php endif; ?>

    <?php
        // SEO: meta description / keywords / canonical / Open Graph.
        // Sourced from Seo_model via Portal_Controller::load_seo() when a
        // controller passes $seo_meta; falls back to a sane site-wide
        // default so every page always has a description, per
        // CONTENT_MODULE_STANDARD.md §4. (Logic unchanged from RC3 —
        // only $__site_name now goes through the site_name() helper
        // instead of reading $site_settings directly, per TASK 7.)
        $__seo = isset($seo_meta) && is_array($seo_meta) ? $seo_meta : array();
        $__site_name = site_name();
        $__og_title = !empty($__seo['og_title']) ? $__seo['og_title'] : (isset($title) ? $title : $__site_name);
        $__meta_description = !empty($__seo['meta_description'])
            ? $__seo['meta_description']
            : (isset($site_settings['seo']['default_meta_description']) ? $site_settings['seo']['default_meta_description'] : ('Portal resmi ' . $__site_name . '.'));
        $__og_description = !empty($__seo['og_description']) ? $__seo['og_description'] : $__meta_description;
        $__keywords = !empty($__seo['keywords']) ? $__seo['keywords'] : '';
        $__og_image = !empty($__seo['og_image']) ? base_url($__seo['og_image']) : '';
        $__canonical = !empty($__seo['canonical_url']) ? $__seo['canonical_url'] : current_url();
    ?>
    <meta name="description" content="<?php echo esc_attr($__meta_description); ?>">
    <?php if (!empty($__keywords)): ?>
    <meta name="keywords" content="<?php echo esc_attr($__keywords); ?>">
    <?php endif; ?>
    <link rel="canonical" href="<?php echo esc_attr($__canonical); ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo esc_attr($__og_title); ?>">
    <meta property="og:description" content="<?php echo esc_attr($__og_description); ?>">
    <meta property="og:url" content="<?php echo esc_attr(current_url()); ?>">
    <meta property="og:site_name" content="<?php echo esc_attr($__site_name); ?>">
    <?php if (!empty($__og_image)): ?>
    <meta property="og:image" content="<?php echo esc_attr($__og_image); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <?php else: ?>
    <meta name="twitter:card" content="summary">
    <?php endif; ?>

    <?php
        $activeTheme = get_setting('theme', 'active_theme', 'default');
        $isIslamic   = ($activeTheme === 'islamic');
    ?>

    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <?php if ($isIslamic): ?>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <?php endif; ?>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }

        /* Rich content rendering */
        .prose table {
            display: block;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            white-space: nowrap;
        }
        .prose img {
            border-radius: 0.75rem;
        }
        .prose iframe {
            max-width: 100%;
        }
        .prose iframe[src*="youtube"],
        .prose iframe[src*="vimeo"] {
            width: 100%;
            aspect-ratio: 16 / 9;
            height: auto;
        }

        <?php if ($isIslamic): ?>
        .font-arabic { font-family: 'Amiri', serif; }
        body.theme-islamic {
            background-color: #f0fdf4 !important;
        }
        body.theme-islamic .bg-indigo-600,
        body.theme-islamic .bg-indigo-700,
        body.theme-islamic .bg-indigo-500 { background-color: #059669 !important; }
        body.theme-islamic .hover\:bg-indigo-700:hover { background-color: #047857 !important; }
        body.theme-islamic .hover\:bg-indigo-50:hover { background-color: #ecfdf5 !important; }
        body.theme-islamic .bg-indigo-700 { background-color: #047857 !important; }
        body.theme-islamic .text-indigo-600,
        body.theme-islamic .text-indigo-700,
        body.theme-islamic .text-indigo-800 { color: #059669 !important; }
        body.theme-islamic .text-indigo-500 { color: #10b981 !important; }
        body.theme-islamic .text-indigo-200 { color: #a7f3d0 !important; }
        body.theme-islamic .text-indigo-100 { color: #d1fae5 !important; }
        body.theme-islamic .bg-slate-900 { background-color: #064e3b !important; }
        body.theme-islamic .border-indigo-600 { border-color: #059669 !important; }
        body.theme-islamic .focus\:border-indigo-500:focus { border-color: #059669 !important; }
        body.theme-islamic .focus\:ring-indigo-100:focus { --tw-ring-color: #d1fae5 !important; }
        body.theme-islamic .shadow-indigo-100 { box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.2) !important; }
        body.theme-islamic .shadow-indigo-900\/40 { box-shadow: 0 10px 25px -5px rgba(4, 120, 87, 0.4) !important; }
        body.theme-islamic .from-indigo-600 { --tw-gradient-from: #059669 var(--tw-gradient-from-position) !important; --tw-gradient-to: rgb(5 150 105 / 0) var(--tw-gradient-to-position) !important; --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-from), var(--tw-gradient-to)) !important; }
        body.theme-islamic .to-indigo-700 { --tw-gradient-to: #047857 var(--tw-gradient-to-position) !important; }
        body.theme-islamic .from-slate-900\/90 { --tw-gradient-from: rgb(6 78 59 / 0.95) var(--tw-gradient-from-position) !important; }
        <?php endif; ?>
    </style>
</head>
<body class="<?php echo $isIslamic ? 'theme-islamic ' : ''; ?>bg-slate-50 text-slate-900 antialiased flex flex-col min-h-screen">

    <?php if ($isIslamic): ?>
    <!-- Islamic Theme Banner -->
    <div class="bg-gradient-to-r from-emerald-900 via-emerald-800 to-emerald-900 text-amber-300 py-2 px-4 text-xs font-semibold tracking-wide border-b border-amber-500/30">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <div class="hidden sm:flex items-center space-x-2 text-emerald-100">
                <i data-lucide="moon" class="w-3.5 h-3.5 text-amber-400"></i>
                <span>Selamat Datang di Portal Resmi <?php echo esc_html(site_name()); ?></span>
            </div>
            <div class="mx-auto sm:mx-0 font-arabic text-base text-amber-300 font-bold tracking-wide">
                بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ — Assalamu'alaikum Warahmatullahi Wabarakatuh
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Header Navigation -->
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <!-- Brand / Logo -->
                <a href="<?php echo base_url(); ?>" class="flex items-center space-x-3">
                    <?php if (site_logo()): ?>
                        <img src="<?php echo esc_attr(site_logo()); ?>" alt="<?php echo esc_attr(site_name()); ?>" class="w-10 h-10 rounded-xl object-contain">
                    <?php else: ?>
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-md shadow-indigo-100">
                        <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                    </div>
                    <?php endif; ?>
                    <div>
                        <span class="text-xl font-bold tracking-tight text-slate-900">
                            <?php echo esc_html(site_name()); ?>
                        </span>
                        <div class="text-[10px] text-slate-500 font-medium tracking-wider uppercase">
                            NPSN: <?php echo isset($site_settings['school']['npsn']) ? esc_html($site_settings['school']['npsn']) : '10293847'; ?> • Akreditasi <?php echo isset($site_settings['school']['accreditation']) ? esc_html($site_settings['school']['accreditation']) : 'A'; ?>
                        </div>
                    </div>
                </a>

                <!-- Desktop Navigation Menu (100% dynamic — sourced from Menu Builder / `menus` table) -->
                <div class="hidden md:flex items-center space-x-6">
                    <?php echo render_menu_desktop($header_menu ?? [], $current_path ?? ''); ?>

                    <a href="<?php echo base_url('admin/login'); ?>" class="inline-flex items-center space-x-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-sm font-medium shadow-lg shadow-indigo-100 transition">
                        <i data-lucide="log-in" class="w-4 h-4"></i>
                        <span>Admin Panel</span>
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center space-x-2">
                    <button type="button" id="mobile-menu-toggle" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Panel (100% dynamic) -->
        <div id="mobile-menu-panel" class="hidden md:hidden border-t border-slate-100 bg-white px-4 py-3 space-y-1">
            <?php echo render_menu_mobile($header_menu ?? []); ?>
            <a href="<?php echo base_url('admin/login'); ?>" class="block px-4 py-2.5 text-sm font-medium text-indigo-600 hover:bg-indigo-50 rounded-lg transition">Admin Panel</a>
        </div>
    </nav>
