<?php
/**
 * Eagle Reports Generator - Front Controller (MVC Router)
 */

// Load app configuration
require_once __DIR__ . '/config/config.php';

// Load database connection
require_once __DIR__ . '/config/database.php';

$database = new Database();
$db = $database->getConnection();

// Basic Routing Engine
$route = $_GET['route'] ?? 'dashboard';

// Auth Guard: Force login for private routes
$publicRoutes = ['login', 'forgot-password'];
if (!isLoggedIn() && !in_array($route, $publicRoutes)) {
    header('Location: index.php?route=login');
    exit;
}

switch ($route) {
    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController($db);
        $controller->login();
        break;

    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController($db);
        $controller->logout();
        break;

    case 'forgot-password':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController($db);
        $controller->forgotPassword();
        break;

    case 'dashboard':
        require_once __DIR__ . '/controllers/DashboardController.php';
        $controller = new DashboardController($db);
        $controller->index();
        break;

    case 'history':
        require_once __DIR__ . '/controllers/ReportController.php';
        $controller = new ReportController($db);
        $controller->history();
        break;

    case 'generate':
        require_once __DIR__ . '/controllers/ReportController.php';
        $controller = new ReportController($db);
        $controller->generate();
        break;

    case 'edit':
        require_once __DIR__ . '/controllers/ReportController.php';
        $controller = new ReportController($db);
        $controller->edit();
        break;

    case 'save':
        require_once __DIR__ . '/controllers/ReportController.php';
        $controller = new ReportController($db);
        $controller->save();
        break;

    case 'duplicate':
        require_once __DIR__ . '/controllers/ReportController.php';
        $controller = new ReportController($db);
        $controller->duplicate();
        break;

    case 'delete':
        require_once __DIR__ . '/controllers/ReportController.php';
        $controller = new ReportController($db);
        $controller->delete();
        break;

    case 'pdf':
        require_once __DIR__ . '/controllers/ReportController.php';
        $controller = new ReportController($db);
        $controller->generatePdf();
        break;

    case 'profile':
        require_once __DIR__ . '/controllers/ProfileController.php';
        $controller = new ProfileController($db);
        $controller->index();
        break;

    case 'settings':
        require_once __DIR__ . '/controllers/SettingsController.php';
        $controller = new SettingsController($db);
        $controller->index();
        break;

    default:
        // 404 Fallback
        http_response_code(404);
        die("404 - Page not found.");
}
