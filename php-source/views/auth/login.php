<?php
// Secure Login Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eagle Reports Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #141414;
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background-color: #1e1e1e;
            border: 1px solid #2d2d2d;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            max-width: 420px;
            width: 100%;
            padding: 40px;
        }
        .logo-header {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            color: #CFFE1C;
            text-align: center;
            font-size: 24px;
            letter-spacing: -0.03em;
            margin-bottom: 30px;
        }
        .form-control {
            background-color: #111111;
            border: 1px solid #333333;
            color: #ffffff;
            border-radius: 6px;
            padding: 12px 14px;
        }
        .form-control:focus {
            background-color: #111111;
            border-color: #CFFE1C;
            color: #ffffff;
            box-shadow: 0 0 0 0.25rem rgba(207, 254, 28, 0.15);
        }
        .btn-primary {
            background-color: #CFFE1C !important;
            border-color: #CFFE1C !important;
            color: #000000 !important;
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 600;
            padding: 12px;
            border-radius: 6px;
            width: 100%;
            transition: all 0.2s ease-in-out;
        }
        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        .forgot-link {
            color: #CFFE1C;
            text-decoration: none;
            font-size: 13px;
        }
        .forgot-link:hover {
            text-decoration: underline;
        }
        .form-check-label {
            color: #a0a0a0;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo-header">
        <i class="bi bi-egg-fill me-2 fs-3"></i>EAGLE REPORTS
    </div>
    
    <p class="text-center text-muted mb-4">Please log in to your staff portal</p>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger border-0 bg-danger text-white py-2" style="font-size: 13px;">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success border-0 bg-success text-white py-2" style="font-size: 13px;">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <form action="index.php?route=login" method="POST">
        <!-- CSRF Token -->
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="mb-3">
            <label for="email" class="form-label text-muted" style="font-size: 12px; font-weight: 500;">EMAIL ADDRESS</label>
            <input type="email" class="form-control" id="email" name="email" required placeholder="name@eagledigital.com">
        </div>
        
        <div class="mb-4">
            <div class="d-flex justify-content-between mb-1">
                <label for="password" class="form-label text-muted mb-0" style="font-size: 12px; font-weight: 500;">PASSWORD</label>
                <a href="#" class="forgot-link" data-bs-toggle="modal" data-bs-target="#forgotModal">Forgot?</a>
            </div>
            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password">
        </div>

        <div class="mb-4 form-check d-flex justify-content-between align-items-center">
            <div>
                <input type="checkbox" class="form-check-input" id="remember" name="remember" style="background-color: #111; border-color: #333;">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
        </button>
    </form>
</div>

<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-card border border-dark text-white">
            <div class="modal-header border-bottom border-dark">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?route=forgot-password" method="POST">
                <div class="modal-body">
                    <p class="text-muted-custom">Enter your work email address and we will send a password reset token to you.</p>
                    <div class="mb-3">
                        <label for="forgot_email" class="form-label">Work Email</label>
                        <input type="email" class="form-control" id="forgot_email" name="email" required placeholder="admin@eagle.com">
                    </div>
                </div>
                <div class="modal-footer border-top border-dark">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Request Token</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
