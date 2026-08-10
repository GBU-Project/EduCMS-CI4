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

    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }

        /* Rich content rendering (posts/pages authored with the TinyMCE editor).
           The Tailwind Typography plugin (loaded above) handles most of this via
           the 'prose' class, but tables and embedded video need extra help to
           stay responsive since editor-inserted markup isn't scoped to prose's
           container width by default. */
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
        /* Embedded video (TinyMCE 'media' plugin output) responsive at a 16:9 ratio */
        .prose iframe[src*="youtube"],
        .prose iframe[src*="vimeo"] {
            width: 100%;
            aspect-ratio: 16 / 9;
            height: auto;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased flex flex-col min-h-screen">

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
                    <?php echo render_menu_desktop($header_menu, $current_path ?? ''); ?>

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
            <?php echo render_menu_mobile($header_menu); ?>
            <a href="<?php echo base_url('admin/login'); ?>" class="block px-4 py-2.5 text-sm font-medium text-indigo-600 hover:bg-indigo-50 rounded-lg transition">Admin Panel</a>
        </div>
    </nav>
