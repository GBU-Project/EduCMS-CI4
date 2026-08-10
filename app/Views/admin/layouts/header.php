<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token-name" content="<?php echo csrf_token(); ?>">
    <meta name="csrf-token-value" content="<?php echo csrf_hash(); ?>">
    <title><?php echo isset($title) ? esc_html($title) : 'Admin Panel'; ?> | <?php echo esc_html(site_name()); ?></title>
    <?php if (site_favicon()): ?>
    <link rel="icon" type="image/x-icon" href="<?php echo esc_attr(site_favicon()); ?>">
    <?php endif; ?>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="<?php echo base_url('assets/shared/fontawesome/css/all.min.css'); ?>">
    <!-- AdminLTE 3 CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/admin/css/adminlte.min.css'); ?>">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/admin/datatables/css/dataTables.bootstrap4.min.css'); ?>">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/admin/sweetalert2/sweetalert2.min.css'); ?>">
    <!-- EduCMS Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/admin/css/educms-admin.css'); ?>">
    <!-- Theme Manager FOUC Blocker -->
    <script>
        (function() {
            const dbTheme = "<?php echo isset($site_settings['default_theme']) ? esc_attr($site_settings['default_theme']) : 'auto'; ?>";
            const theme = localStorage.getItem('educms_admin_theme') || dbTheme || 'auto';
            let isDark = theme === 'dark';
            if (theme === 'auto') {
                isDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            }
            if (isDark) {
                document.documentElement.classList.add('dark-mode');
            }
        })();
    </script>
    <script>
        window.EduCMS_Config = {
            baseUrl: "<?php echo base_url(); ?>",
            defaultTheme: "<?php echo isset($site_settings['default_theme']) ? esc_attr($site_settings['default_theme']) : 'auto'; ?>"
        };
    </script>
    <!-- TinyMCE 6 Rich Text Editor (self-hosted — see assets/admin/plugins/tinymce, GPL-2.0-or-later) -->
    <script src="<?php echo base_url('assets/admin/plugins/tinymce/tinymce.min.js'); ?>" referrerpolicy="origin"></script>
    <style>
        body {
            font-family: 'Outfit', sans-serif !important;
            font-size: 0.92rem;
            background-color: #f8fafc;
        }
        .main-sidebar {
            background-color: #0f172a !important; /* Premium Dark Slate Sidebar */
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        }
        .brand-link {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
            background-color: #0f172a !important;
        }
        .brand-text {
            font-weight: 600 !important;
            letter-spacing: -0.5px;
            color: #ffffff !important;
        }
        .nav-sidebar .nav-link {
            color: #94a3b8 !important;
            border-radius: 8px !important;
            margin: 2px 8px !important;
            padding: 8px 12px !important;
            transition: all 0.2s ease;
        }
        .nav-sidebar .nav-link:hover, .nav-sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.06) !important;
            color: #ffffff !important;
        }
        .nav-sidebar .nav-link.active {
            background-color: #6366f1 !important; /* Accent Purple/Indigo active indicator */
            color: #ffffff !important;
        }
        .nav-sidebar .nav-icon {
            margin-right: 10px !important;
            font-size: 1.05rem !important;
        }
        .main-header {
            border-bottom: 1px solid #e2e8f0 !important;
            background-color: #ffffff !important;
        }
        .breadcrumb-container {
            background-color: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 12px 24px;
        }
        .content-wrapper {
            background-color: #f8fafc !important;
        }
        .dropdown-menu-indigo {
            background-color: #6366f1;
            color: #ffffff;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Theme Switcher Toggle -->
      <li class="nav-item">
        <a class="nav-link theme-toggle-btn" onclick="cycleTheme()" role="button" title="Ganti Tema">
          <i class="fa-solid fa-sun theme-icon"></i>
        </a>
      </li>
      <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
          <i class="fa-solid fa-circle-user mr-1 text-secondary"></i>
          <span class="d-none d-md-inline text-dark"><?php echo esc_html($current_user->full_name); ?></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <!-- User image -->
          <li class="user-header bg-indigo p-4 text-center text-white" style="background-color: #6366f1 !important;">
            <i class="fa-solid fa-circle-user fa-3x mb-2 text-white"></i>
            <p>
              <?php echo esc_html($current_user->full_name); ?>
              <small class="d-block text-white-50 mt-1"><?php echo esc_html((string) session()->get('role_name')); ?></small>
            </p>
          </li>
          <!-- Menu Footer-->
          <li class="user-footer d-flex justify-content-between p-3">
            <a href="#" class="btn btn-default btn-flat btn-sm">Profil</a>
            <a href="<?php echo base_url('admin/logout'); ?>" class="btn btn-default btn-flat btn-sm text-danger font-weight-bold">Keluar</a>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
