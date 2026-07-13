<?php
/**
 * ProfileController - Handles user profile edits and password updates
 */

class ProfileController {
    private $db;
    private $userModel;

    public function __construct($database) {
        $this->db = $database;
        require_once __DIR__ . '/../models/User.php';
        $this->userModel = new User($this->db);
    }

    public function index() {
        if (!isLoggedIn()) {
            header('Location: index.php?route=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getById($userId);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF check
            if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
                die("CSRF token verification failed.");
            }

            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            $company_name = filter_input(INPUT_POST, 'company_name', FILTER_SANITIZE_SPECIAL_CHARS);
            $company_phone = filter_input(INPUT_POST, 'company_phone', FILTER_SANITIZE_SPECIAL_CHARS);
            $company_email = filter_input(INPUT_POST, 'company_email', FILTER_SANITIZE_EMAIL);
            $company_website = filter_input(INPUT_POST, 'company_website', FILTER_SANITIZE_SPECIAL_CHARS);
            $company_footer = filter_input(INPUT_POST, 'company_footer', FILTER_SANITIZE_SPECIAL_CHARS);

            $error = '';
            
            if (empty($name) || empty($email)) {
                $error = 'Name and Email are required.';
            } elseif (!empty($password) && $password !== $confirm_password) {
                $error = 'Passwords do not match.';
            } else {
                $data = [
                    'name' => $name,
                    'email' => $email,
                    'password' => $password,
                    'company_name' => $company_name,
                    'company_phone' => $company_phone,
                    'company_email' => $company_email,
                    'company_website' => $company_website,
                    'company_footer' => $company_footer
                ];

                if ($this->userModel->updateProfile($userId, $data)) {
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['flash_success'] = "Profile and company information updated successfully!";
                    header('Location: index.php?route=profile');
                    exit;
                } else {
                    $error = 'Failed to update profile. Email might already be in use.';
                }
            }
        }

        require_once __DIR__ . '/../views/profile.php';
    }
}
