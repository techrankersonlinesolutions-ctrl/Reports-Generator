<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts (Space Grotesk & Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        :root {
            --bg-color: #141414;
            --card-bg: #1e1e1e;
            --card-border: #2d2d2d;
            --primary-color: #CFFE1C;
            --primary-rgb: 207, 254, 28;
            --text-color: #f5f5f5;
            --text-muted: #a0a0a0;
            --font-sans: 'Inter', sans-serif;
            --font-display: 'Space Grotesk', sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: var(--font-sans);
            font-size: 14px;
            letter-spacing: -0.01em;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6, .display-font {
            font-family: var(--font-display);
            font-weight: 600;
            color: #ffffff;
            letter-spacing: -0.02em;
        }

        .navbar-brand {
            font-family: var(--font-display);
            font-weight: 700;
            color: var(--primary-color) !important;
            letter-spacing: -0.03em;
        }

        .bg-card {
            background-color: var(--card-bg) !important;
            border: 1px solid var(--card-border);
        }

        .btn-primary {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
            color: #000000 !important;
            font-family: var(--font-display);
            font-weight: 600;
            border-radius: 6px;
            padding: 8px 18px;
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            border-color: var(--primary-color) !important;
            color: var(--primary-color) !important;
            font-family: var(--font-display);
            font-weight: 600;
            border-radius: 6px;
            padding: 8px 18px;
            transition: all 0.2s ease-in-out;
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color) !important;
            color: #000000 !important;
        }

        .text-muted-custom {
            color: var(--text-muted);
        }

        .nav-link {
            font-family: var(--font-display);
            color: var(--text-muted) !important;
            font-weight: 500;
            padding: 8px 16px !important;
            border-radius: 6px;
            transition: all 0.2s;
        }

        .nav-link:hover, .nav-link.active {
            color: var(--primary-color) !important;
            background-color: rgba(var(--primary-rgb), 0.1);
        }

        /* Card elements styling */
        .card {
            border-radius: 12px;
            border: 1px solid var(--card-border);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background-color: transparent !important;
            border-bottom: 1px solid var(--card-border) !important;
            padding: 16px 24px;
        }

        .card-body {
            padding: 24px;
        }

        /* Form elements styling */
        .form-control, .form-select {
            background-color: #1a1a1a !important;
            border: 1px solid #333333 !important;
            color: #ffffff !important;
            border-radius: 6px;
            padding: 10px 14px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 0.25rem rgba(var(--primary-rgb), 0.15) !important;
        }

        .form-label {
            color: var(--text-muted);
            font-weight: 500;
            margin-bottom: 6px;
        }

        /* Table custom design */
        .table {
            color: var(--text-color) !important;
        }
        
        .table-dark {
            --bs-table-bg: var(--card-bg) !important;
            --bs-table-border-color: var(--card-border) !important;
        }

        /* Custom progress bar */
        .progress {
            background-color: #2b2b2b !important;
            border-radius: 50px;
        }

        .progress-bar {
            background-color: var(--primary-color) !important;
        }

        /* Step wizard navigation styles */
        .step-indicator {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--card-border);
            padding-bottom: 20px;
            margin-bottom: 30px;
            overflow-x: auto;
        }

        .step-node {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            flex: 1;
            min-width: 90px;
            cursor: pointer;
        }

        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #2b2b2b;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 8px;
            border: 2px solid transparent;
            transition: all 0.3s;
        }

        .step-node.active .step-circle {
            background-color: rgba(var(--primary-rgb), 0.15);
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        .step-node.completed .step-circle {
            background-color: var(--primary-color);
            color: #000;
            border-color: var(--primary-color);
        }

        .step-label {
            font-size: 11px;
            font-family: var(--font-display);
            font-weight: 500;
            color: var(--text-muted);
        }

        .step-node.active .step-label {
            color: var(--primary-color);
            font-weight: 600;
        }

        .step-node.completed .step-label {
            color: #ffffff;
        }

        /* Notification Badges */
        .badge-primary {
            background-color: var(--primary-color) !important;
            color: #000000 !important;
            font-weight: 600;
        }
    </style>
</head>
<body>

<?php if (isLoggedIn()): ?>
<nav class="navbar navbar-expand-lg border-bottom border-dark bg-card py-3">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="index.php?route=dashboard">
            <i class="bi bi-egg-fill me-2 fs-4"></i>
            <span>EAGLE REPORTS</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-4">
                <li class="nav-item">
                    <a class="nav-link <?php echo ($route === 'dashboard') ? 'active' : ''; ?>" href="index.php?route=dashboard">
                        <i class="bi bi-grid-1x2-fill me-2"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($route === 'history') ? 'active' : ''; ?>" href="index.php?route=history">
                        <i class="bi bi-file-earmark-bar-graph-fill me-2"></i>Report History
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($route === 'generate') ? 'active' : ''; ?>" href="index.php?route=generate">
                        <i class="bi bi-plus-circle-fill me-2"></i>Generate Report
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-3">
                    <span class="text-muted-custom"><i class="bi bi-person-fill me-1"></i><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($route === 'profile') ? 'active' : ''; ?>" href="index.php?route=profile">
                        <i class="bi bi-shield-lock-fill me-1"></i>Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo ($route === 'settings') ? 'active' : ''; ?>" href="index.php?route=settings">
                        <i class="bi bi-gear-fill me-1"></i>Settings
                    </a>
                </li>
                <li class="nav-item ms-3">
                    <a class="btn btn-outline-danger btn-sm border-0" href="index.php?route=logout">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="container py-4 px-4">
    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show bg-success text-white border-0" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?php echo $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show bg-danger text-white border-0" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
