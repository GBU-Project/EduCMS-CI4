<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? esc_html($title) : esc_html(site_name() . ' - ' . site_tagline()); ?></title>
    <?php if (site_favicon()): ?>
    <link rel="icon" type="image/x-icon" href="<?php echo esc_attr(site_favicon()); ?>">
    <?php endif; ?>

    <?php
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

    <!-- Styling & Typography -->
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- Theme SDK Centralized CSS Variables & Design System -->
    <link rel="stylesheet" href="<?php echo base_url('themes/islamic/assets/css/theme-islamic.css'); ?>">


    <style>
        body { 
            font-family: var(--theme-font-sans); 
            color: var(--theme-text-main); 
            background-color: var(--theme-surface-muted); 
        }
        .font-arabic { font-family: var(--theme-font-serif); }
        a { color: var(--theme-link); }
        a:hover { color: var(--theme-link-hover); }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased flex flex-col min-h-screen">



    <!-- Theme Navbar & Top Bar -->
    <?php $this->load->view('../../themes/islamic/views/partials/navbar'); ?>

