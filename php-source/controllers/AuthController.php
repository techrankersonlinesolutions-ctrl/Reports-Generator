<?php
/**
 * AuthController - Handles user authentication
 */

class AuthController {
    private $db;
    private $userModel;

    public function __construct($database) {
        $this->db = $database;
        require_once __DIR__ . '/../models/User.php';
        $this->userModel = new User($this->db);
    }

    public function login() {
        if (isLoggedIn()) {
            header('Location: index.php?route=dashboard');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF protection
            if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
                die("CSRF validation failed.");
            }

            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            if (empty($email) || empty($password)) {
                $error = 'Please enter both email and password.';
            } else {
                $user = $this->userModel->login($email, $password);
                if ($user) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];

                    if ($remember) {
                        $token = bin2hex(random_bytes(32));
                        // Save token in DB
                        $query = "UPDATE users SET remember_token = :token WHERE id = :id";
                        $stmt = $this->db->prepare($query);
                        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                        $stmt->bindParam(':id', $user['id'], PDO::PARAM_INT);
                        $stmt->execute();

                        // Set cookie for 30 days
                        setcookie('remember_token', $token, time() + (86400 * 30), "/", "", false, true);
                    }

                    header('Location: index.php?route=dashboard');
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                }
            }
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        // Destroy remember token in DB
        if (isset($_SESSION['user_id'])) {
            $query = "UPDATE users SET remember_token = NULL WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
        }

        // Unset session variables
        $_SESSION = [];

        // Clear cookies
        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, '/');
        }

        // Destroy session
        session_destroy();

        header('Location: index.php?route=login');
        exit;
    }

    public function forgotPassword() {
        // Simple mock of forgot password for demonstration (as it's an internal-only app)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $success = "If the email exists in our system, a password reset link has been sent.";
            require_once __DIR__ . '/../views/auth/login.php';
            exit;
        }
    }
}
