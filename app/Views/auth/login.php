<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - PT Wex Indo Berkat</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-light: #f3f4f6;
            --card-light: #ffffff;
            --text-light: #1f2937;
            
            --bg-dark: #111827;
            --card-dark: #1f2937;
            --text-dark: #f9fafb;
            
            --primary: #4f46e5;
            --primary-hover: #4338ca;
        }

        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.3s ease;
        }

        /* Light Mode Defaults */
        body.light-mode {
            background-color: var(--bg-light);
            color: var(--text-light);
        }
        .light-mode .btn-theme {
            background-color: #e5e7eb;
            color: #374151;
        }

        /* Dark Mode */
        body.dark-mode {
            background-color: var(--bg-dark);
            color: var(--text-dark);
        }
        .dark-mode .login-card {
            background-color: var(--card-dark);
            border-color: #374151;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.5);
        }
        .dark-mode .form-control {
            background-color: #374151;
            border-color: #4b5563;
            color: white;
        }
        .dark-mode .form-control:focus {
            background-color: #374151;
            color: white;
            border-color: var(--primary);
        }
        .dark-mode .form-label {
            color: #d1d5db;
        }
        .dark-mode .text-muted {
            color: #9ca3af !important;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            border-radius: 1rem;
            padding: 2.5rem;
            background-color: var(--card-light);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
        }

        .brand-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary) 0%, #818cf8 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 1.75rem;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.3);
        }

        .btn-primary {
            background-color: var(--primary);
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            border-radius: 0.5rem;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.4);
        }

        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            border: 1px solid #d1d5db;
        }
        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            border-color: var(--primary);
        }

        #theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            background: transparent;
            border: none;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        #theme-toggle:hover {
            background: rgba(0,0,0,0.05);
        }
        .dark-mode #theme-toggle:hover {
            background: rgba(255,255,255,0.1);
        }
    </style>
</head>
<body class="light-mode">

    <div class="login-card">
        <!-- Theme Toggle -->
        <button id="theme-toggle" title="Switch Theme">
            <i class="fas fa-moon fs-5 text-secondary" id="theme-icon"></i>
        </button>

        <div class="text-center">
            <div class="brand-logo">
                <i class="fas fa-boxes-stacked"></i>
            </div>
            <h4 class="fw-bold mb-1">Selamat Datang</h4>
            <p class="text-muted mb-4 small">Silakan login untuk melanjutkan</p>
        </div>

        <form action="<?= base_url('auth/login') ?>" method="POST">
            <?= csrf_field() ?>
            
            <div class="mb-3">
                <label class="form-label small fw-bold">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 ps-0 text-muted">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus value="Rickylidya">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-0 ps-0 text-muted">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required value="12345678">
                    <button class="btn border-0 text-muted" type="button" onclick="togglePassword()">
                        <i class="fas fa-eye" id="eye-icon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">
                Log In
            </button>

            <div class="text-center">
                <small class="text-muted" style="font-size: 0.75rem;">
                    &copy; <?= date('Y') ?> PT Bex Indo Berkat
                </small>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 1. Theme Toggle Logic
        const body = document.body;
        const themeBtn = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');

        // Check local storage
        if (localStorage.getItem('theme') === 'dark') {
            enableDarkMode();
        }

        themeBtn.addEventListener('click', () => {
            if (body.classList.contains('dark-mode')) {
                disableDarkMode();
            } else {
                enableDarkMode();
            }
        });

        function enableDarkMode() {
            body.classList.remove('light-mode');
            body.classList.add('dark-mode');
            themeIcon.classList.remove('fa-moon', 'text-secondary');
            themeIcon.classList.add('fa-sun', 'text-warning');
            localStorage.setItem('theme', 'dark');
        }

        function disableDarkMode() {
            body.classList.remove('dark-mode');
            body.classList.add('light-mode');
            themeIcon.classList.remove('fa-sun', 'text-warning');
            themeIcon.classList.add('fa-moon', 'text-secondary');
            localStorage.setItem('theme', 'light');
        }

        // 2. Password Toggle
        function togglePassword() {
            const passInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // 3. SweetAlert2 from Flashdata
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= session()->getFlashdata('success') ?>',
                timer: 1500,
                showConfirmButton: false,
                background: localStorage.getItem('theme') === 'dark' ? '#1f2937' : '#fff',
                color: localStorage.getItem('theme') === 'dark' ? '#f9fafb' : '#000'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '<?= session()->getFlashdata('error') ?>',
                confirmButtonColor: '#4f46e5',
                background: localStorage.getItem('theme') === 'dark' ? '#1f2937' : '#fff',
                color: localStorage.getItem('theme') === 'dark' ? '#f9fafb' : '#000'
            });
        <?php endif; ?>
    </script>
</body>
</html>