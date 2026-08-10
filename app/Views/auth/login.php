<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Panel | <?php echo esc_html(site_name()); ?></title>
    <?php if (site_favicon()): ?>
    <link rel="icon" type="image/x-icon" href="<?php echo esc_attr(site_favicon()); ?>">
    <?php endif; ?>
    <!-- Google Fonts: Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="<?php echo base_url('assets/shared/fontawesome/css/all.min.css'); ?>">
    <!-- Bootstrap 5.3 CSS -->
    <link href="<?php echo base_url('assets/portal/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #311042 100%);
            --glass-bg: rgba(30, 41, 59, 0.45);
            --glass-border: rgba(255, 255, 255, 0.08);
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent-color: #6366f1;
            --accent-glow: rgba(99, 102, 241, 0.4);
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--bg-gradient);
            background-size: cover;
            background-position: center;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
            overflow-x: hidden;
        }

        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 40px 35px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .glass-card:hover {
            box-shadow: 0 25px 50px rgba(99, 102, 241, 0.15);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-section i {
            font-size: 3rem;
            background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .logo-section h2 {
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: -0.5px;
            margin: 0;
            background: linear-gradient(to right, #ffffff, #cbd5e1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .logo-section p {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #cbd5e1;
            margin-bottom: 8px;
        }

        .input-group {
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px var(--accent-glow);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            padding-left: 15px;
        }

        .form-control {
            background: transparent;
            border: none;
            color: var(--text-primary);
            padding: 12px 15px 12px 5px;
            font-size: 0.95rem;
        }

        .form-control:focus {
            background: transparent;
            border: none;
            box-shadow: none;
            color: var(--text-primary);
        }

        .form-check-input {
            background-color: rgba(15, 23, 42, 0.5);
            border-color: var(--glass-border);
        }

        .form-check-input:checked {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-login {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            border-radius: 12px;
            padding: 12px;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px var(--accent-glow);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6);
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        }

        .btn-login:active {
            transform: translateY(1px);
        }

        .forgot-link {
            color: var(--accent-color);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .forgot-link:hover {
            color: #818cf8;
            text-decoration: underline;
        }

        /* Alerts Styling */
        .alert {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            font-size: 0.88rem;
            border-radius: 12px;
            padding: 12px 15px;
            margin-bottom: 25px;
        }

        .alert-success {
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #86efac;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="glass-card">
            
            <div class="logo-section">
                <?php if (site_logo()): ?>
                    <img src="<?php echo esc_attr(site_logo()); ?>" alt="<?php echo esc_attr(site_name()); ?>" style="height:64px;width:auto;max-width:220px;object-fit:contain;margin-bottom:0.5rem;">
                <?php else: ?>
                    <i class="fa-solid fa-graduation-cap"></i>
                <?php endif; ?>
                <h2><?php echo esc_html(site_name()); ?></h2>
                <p><?php echo esc_html(site_tagline()); ?></p>
            </div>

            <!-- Display Flash Message Alerts -->
            <?php if ($this->session->flashdata('error')): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo esc_html($this->session->flashdata('error')); ?>
                </div>
            <?php endif; ?>

            <?php if ($this->session->flashdata('success')): ?>
                <div class="alert alert-success" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> <?php echo esc_html($this->session->flashdata('success')); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <?php echo form_open('admin/login'); ?>
                
                <div class="mb-3">
                    <label for="identity" class="form-label">Username atau Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="identity" id="identity" class="form-control" placeholder="Masukkan username atau email" value="<?php echo set_value('identity'); ?>" autocomplete="username" required>
                    </div>
                    <?php echo form_error('identity', '<div class="text-danger small mt-1">', '</div>'); ?>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="password" class="form-label mb-0">Kata Sandi</label>
                        <a href="<?php echo base_url('admin/forgot'); ?>" class="forgot-link">Lupa Sandi?</a>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                    </div>
                    <?php echo form_error('password', '<div class="text-danger small mt-1">', '</div>'); ?>
                </div>

                <div class="mb-4 form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember" value="1">
                    <label class="form-check-label text-secondary small" for="remember">Ingat saya di perangkat ini</label>
                </div>

                <button type="submit" class="btn btn-login w-100">
                    <i class="fa-solid fa-right-to-bracket me-2"></i> Masuk Dashboard
                </button>

            <?php echo form_close(); ?>

        </div>
    </div>

    <!-- Bootstrap 5.3 Bundle JS -->
    <script src="<?php echo base_url('assets/portal/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
</body>
</html>
