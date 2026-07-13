<?php
/**
 * SettingsController - Handles global defaults, margins, colors
 */

class SettingsController {
    private $db;
    private $settingModel;

    public function __construct($database) {
        $this->db = $database;
        require_once __DIR__ . '/../models/Setting.php';
        $this->settingModel = new Setting($this->db);
    }

    public function index() {
        if (!isLoggedIn()) {
            header('Location: index.php?route=login');
            exit;
        }

        $settings = $this->settingModel->getSettings();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF check
            if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
                die("CSRF validation failed.");
            }

            $data = [
                'default_company_name' => filter_input(INPUT_POST, 'default_company_name', FILTER_SANITIZE_SPECIAL_CHARS),
                'default_email' => filter_input(INPUT_POST, 'default_email', FILTER_SANITIZE_EMAIL),
                'default_phone' => filter_input(INPUT_POST, 'default_phone', FILTER_SANITIZE_SPECIAL_CHARS),
                'default_website' => filter_input(INPUT_POST, 'default_website', FILTER_SANITIZE_SPECIAL_CHARS),
                'default_footer' => filter_input(INPUT_POST, 'default_footer', FILTER_SANITIZE_SPECIAL_CHARS),
                'pdf_margin_top' => filter_input(INPUT_POST, 'pdf_margin_top', FILTER_VALIDATE_INT) ?: 15,
                'pdf_margin_bottom' => filter_input(INPUT_POST, 'pdf_margin_bottom', FILTER_VALIDATE_INT) ?: 15,
                'pdf_margin_left' => filter_input(INPUT_POST, 'pdf_margin_left', FILTER_VALIDATE_INT) ?: 15,
                'pdf_margin_right' => filter_input(INPUT_POST, 'pdf_margin_right', FILTER_VALIDATE_INT) ?: 15,
                'primary_color' => filter_input(INPUT_POST, 'primary_color', FILTER_SANITIZE_SPECIAL_CHARS) ?: '#CFFE1C',
                'secondary_color' => filter_input(INPUT_POST, 'secondary_color', FILTER_SANITIZE_SPECIAL_CHARS) ?: '#141414'
            ];

            if ($this->settingModel->updateSettings($data)) {
                $_SESSION['flash_success'] = "Global settings updated successfully!";
                header('Location: index.php?route=settings');
                exit;
            } else {
                $_SESSION['flash_error'] = "Failed to update settings.";
            }
        }

        require_once __DIR__ . '/../views/settings.php';
    }
}
