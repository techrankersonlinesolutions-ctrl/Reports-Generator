<?php
/**
 * ReportController - Handles multi-step report generation, uploads, duplicates, history, and PDFs
 */

class ReportController {
    private $db;
    private $reportModel;
    private $settingModel;

    public function __construct($database) {
        $this->db = $database;
        require_once __DIR__ . '/../models/Report.php';
        require_once __DIR__ . '/../models/Setting.php';
        $this->reportModel = new Report($this->db);
        $this->settingModel = new Setting($this->db);
    }

    public function history() {
        if (!isLoggedIn()) {
            header('Location: index.php?route=login');
            exit;
        }
        $userId = $_SESSION['user_id'];
        $reports = $this->reportModel->getAll($userId);
        require_once __DIR__ . '/../views/report/history.php';
    }

    public function generate() {
        if (!isLoggedIn()) {
            header('Location: index.php?route=login');
            exit;
        }

        // Get default settings to pre-fill the form
        $settings = $this->settingModel->getSettings();
        
        // Define clean empty report layout structure for blank creation
        $report = [
            'id' => '',
            'business_name' => '',
            'report_month' => date('F'),
            'report_year' => date('Y'),
            'generated_date' => date('Y-m-d'),
            'business_logo' => '',
            'cover_image' => '',
            'people_viewed' => 0,
            'search_direct' => 0,
            'search_discovery' => 0,
            'profile_interactions' => 0,
            'reviews_count' => 0,
            'rating_average' => 0.00,
            'views_maps' => 0,
            'views_search' => 0,
            'heatmap_image' => '',
            'avg_rank' => 0.00,
            'top_3_percentage' => 0.00,
            'points_tracked' => 49,
            'insight_text' => 'We tracked local grids for high intent search terms. Visibilities have increased overall.',
            'geofence_map_url' => '',
            'next_month_plan' => $settings['default_footer'] ?? '<h3>SEO Action Plan</h3><p>Continue building local links and reviews.</p>',
            'company_email' => $settings['default_email'] ?? '',
            'company_phone' => $settings['default_phone'] ?? '',
            'company_website' => $settings['default_website'] ?? '',
            'footer_notes' => $settings['default_footer'] ?? '',
            'keywords' => [],
            'backlinks' => []
        ];

        require_once __DIR__ . '/../views/report/generate.php';
    }

    public function edit() {
        if (!isLoggedIn()) {
            header('Location: index.php?route=login');
            exit;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            header('Location: index.php?route=history');
            exit;
        }

        $report = $this->reportModel->getById($id);
        if (!$report) {
            die("Report not found.");
        }

        require_once __DIR__ . '/../views/report/generate.php';
    }

    public function save() {
        if (!isLoggedIn()) {
            header('Location: index.php?route=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // CSRF protection
            if (!isset($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
                die("CSRF token validation failed.");
            }

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            
            // Handle image uploads with security and resizing
            $existing_logo = $_POST['existing_business_logo'] ?? '';
            $business_logo = $this->handleImageUpload('business_logo', $existing_logo);

            $existing_cover = $_POST['existing_cover_image'] ?? '';
            $cover_image = $this->handleImageUpload('cover_image', $existing_cover);

            $existing_heatmap = $_POST['existing_heatmap_image'] ?? '';
            $heatmap_image = $this->handleImageUpload('heatmap_image', $existing_heatmap);

            // Sanitize inputs
            $data = [
                'business_name' => filter_input(INPUT_POST, 'business_name', FILTER_SANITIZE_SPECIAL_CHARS),
                'report_month' => filter_input(INPUT_POST, 'report_month', FILTER_SANITIZE_SPECIAL_CHARS),
                'report_year' => filter_input(INPUT_POST, 'report_year', FILTER_SANITIZE_SPECIAL_CHARS),
                'generated_date' => filter_input(INPUT_POST, 'generated_date', FILTER_SANITIZE_SPECIAL_CHARS),
                'business_logo' => $business_logo,
                'cover_image' => $cover_image,
                'people_viewed' => filter_input(INPUT_POST, 'people_viewed', FILTER_VALIDATE_INT) ?: 0,
                'search_direct' => filter_input(INPUT_POST, 'search_direct', FILTER_VALIDATE_INT) ?: 0,
                'search_discovery' => filter_input(INPUT_POST, 'search_discovery', FILTER_VALIDATE_INT) ?: 0,
                'profile_interactions' => filter_input(INPUT_POST, 'profile_interactions', FILTER_VALIDATE_INT) ?: 0,
                'reviews_count' => filter_input(INPUT_POST, 'reviews_count', FILTER_VALIDATE_INT) ?: 0,
                'rating_average' => filter_input(INPUT_POST, 'rating_average', FILTER_VALIDATE_FLOAT) ?: 0.00,
                'views_maps' => filter_input(INPUT_POST, 'views_maps', FILTER_VALIDATE_INT) ?: 0,
                'views_search' => filter_input(INPUT_POST, 'views_search', FILTER_VALIDATE_INT) ?: 0,
                'heatmap_image' => $heatmap_image,
                'avg_rank' => filter_input(INPUT_POST, 'avg_rank', FILTER_VALIDATE_FLOAT) ?: 0.00,
                'top_3_percentage' => filter_input(INPUT_POST, 'top_3_percentage', FILTER_VALIDATE_FLOAT) ?: 0.00,
                'points_tracked' => filter_input(INPUT_POST, 'points_tracked', FILTER_VALIDATE_INT) ?: 49,
                'insight_text' => filter_input(INPUT_POST, 'insight_text', FILTER_SANITIZE_SPECIAL_CHARS),
                'geofence_map_url' => $_POST['geofence_map_url'] ?? '', // Raw URL or embed iframe
                'next_month_plan' => $_POST['next_month_plan'] ?? '', // Allow HTML from WYSIWYG
                'company_email' => filter_input(INPUT_POST, 'company_email', FILTER_SANITIZE_EMAIL),
                'company_phone' => filter_input(INPUT_POST, 'company_phone', FILTER_SANITIZE_SPECIAL_CHARS),
                'company_website' => filter_input(INPUT_POST, 'company_website', FILTER_SANITIZE_SPECIAL_CHARS),
                'footer_notes' => filter_input(INPUT_POST, 'footer_notes', FILTER_SANITIZE_SPECIAL_CHARS),
                'keywords' => [],
                'backlinks' => []
            ];

            // Parse keywords from POST array
            if (isset($_POST['keywords_kw']) && is_array($_POST['keywords_kw'])) {
                for ($i = 0; $i < count($_POST['keywords_kw']); $i++) {
                    $kw = filter_var($_POST['keywords_kw'][$i], FILTER_SANITIZE_SPECIAL_CHARS);
                    $prev = filter_var($_POST['keywords_prev'][$i], FILTER_VALIDATE_INT) ?: 0;
                    $curr = filter_var($_POST['keywords_curr'][$i], FILTER_VALIDATE_INT) ?: 0;
                    if (!empty($kw)) {
                        $data['keywords'][] = [
                            'keyword' => $kw,
                            'prev_rank' => $prev,
                            'curr_rank' => $curr
                        ];
                    }
                }
            }

            // Parse backlinks from separate dynamic lists
            $categories = ['business_listings', 'profile_creations', 'web_2', 'blogs', 'google_stacking', 'stacking_properties', 'guest_posting'];
            foreach ($categories as $cat) {
                if (isset($_POST[$cat . '_url']) && is_array($_POST[$cat . '_url'])) {
                    for ($i = 0; $i < count($_POST[$cat . '_url']); $i++) {
                        $url = filter_var($_POST[$cat . '_url'][$i], FILTER_SANITIZE_URL);
                        $status = filter_var($_POST[$cat . '_status'][$i], FILTER_SANITIZE_SPECIAL_CHARS) ?: 'Active';
                        if (!empty($url)) {
                            $data['backlinks'][] = [
                                'category' => $cat,
                                'url' => $url,
                                'status' => $status
                            ];
                        }
                    }
                }
            }

            if ($id) {
                // Update
                $this->reportModel->update($id, $data);
                $_SESSION['flash_success'] = "Report updated successfully!";
            } else {
                // Insert
                $id = $this->reportModel->create($_SESSION['user_id'], $data);
                $_SESSION['flash_success'] = "Report generated successfully!";
            }

            header('Location: index.php?route=history');
            exit;
        }
    }

    public function duplicate() {
        if (!isLoggedIn()) {
            header('Location: index.php?route=login');
            exit;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $newId = $this->reportModel->duplicate($id);
            if ($newId) {
                $_SESSION['flash_success'] = "Report duplicated successfully! You can now edit the monthly metrics.";
                header('Location: index.php?route=edit&id=' . $newId);
                exit;
            }
        }
        $_SESSION['flash_error'] = "Failed to duplicate report.";
        header('Location: index.php?route=history');
        exit;
    }

    public function delete() {
        if (!isLoggedIn()) {
            header('Location: index.php?route=login');
            exit;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $this->reportModel->delete($id);
            $_SESSION['flash_success'] = "Report deleted successfully.";
        }
        header('Location: index.php?route=history');
        exit;
    }

    // PDF generation trigger
    public function generatePdf() {
        if (!isLoggedIn()) {
            header('Location: index.php?route=login');
            exit;
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            die("Invalid report ID");
        }

        $report = $this->reportModel->getById($id);
        $settings = $this->settingModel->getSettings();
        if (!$report) {
            die("Report not found");
        }

        // We will load mPDF. If it is deployed in cPanel, the developer runs composer install.
        // We provide a beautifully-structured HTML print template file which mPDF compiles into PDF.
        require_once __DIR__ . '/../includes/functions.php';
        
        // In the views folder, we will create a layout specifically for PDF rendering
        ob_start();
        require_once __DIR__ . '/../views/report/pdf_template.php';
        $html = ob_get_clean();

        // Check if mPDF is installed, if not, fallback to a clean Web Print preview
        if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
            require_once __DIR__ . '/../vendor/autoload.php';
            
            try {
                $margin_top = $settings['pdf_margin_top'] ?? 15;
                $margin_bottom = $settings['pdf_margin_bottom'] ?? 15;
                $margin_left = $settings['pdf_margin_left'] ?? 15;
                $margin_right = $settings['pdf_margin_right'] ?? 15;

                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'margin_top' => $margin_top,
                    'margin_bottom' => $margin_bottom,
                    'margin_left' => $margin_left,
                    'margin_right' => $margin_right,
                    'tempDir' => __DIR__ . '/../pdf/tmp'
                ]);

                $mpdf->SetTitle($report['business_name'] . ' - ' . $report['report_month'] . ' ' . $report['report_year'] . ' Report');
                $mpdf->WriteHTML($html);
                
                // Filename format: Business Name - Month Year Report.pdf
                $filename = $report['business_name'] . ' - ' . $report['report_month'] . ' ' . $report['report_year'] . ' Report.pdf';
                
                // Output inline or download
                $action = $_GET['action'] ?? 'view';
                if ($action === 'download') {
                    $mpdf->Output($filename, 'D');
                } else {
                    $mpdf->Output($filename, 'I');
                }
                exit;
            } catch (Exception $e) {
                echo "mPDF Error: " . $e->getMessage() . "<br><br>";
                echo "Showing Print-Friendly HTML fallback:<br><hr>";
                echo $html;
            }
        } else {
            // No mPDF found in the workspace yet. Show raw print-friendly page
            echo "<div style='background:#333;color:#fff;padding:10px;font-family:sans-serif;display:flex;justify-content:space-between;align-items:center;'>";
            echo "<span>mPDF Library is not installed. To generate real PDFs on cPanel, please run 'composer install' in the project directory.</span>";
            echo "<button onclick='window.print()' style='background:#CFFE1C;color:#000;border:none;padding:5px 15px;cursor:pointer;border-radius:3px;font-weight:bold;'>Print / Save PDF</button>";
            echo "</div>";
            echo $html;
        }
    }

    // Secure Image uploader with validation
    private function handleImageUpload($fileField, $existingPath = '') {
        if (!isset($_FILES[$fileField]) || $_FILES[$fileField]['error'] !== UPLOAD_ERR_OK) {
            return $existingPath;
        }

        $file = $_FILES[$fileField];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        
        // Validate MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return $existingPath;
        }

        // Ensure uploads directory exists
        $uploadDir = __DIR__ . '/../uploads/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate secure file name
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('img_', true) . '.' . $ext;
        $targetPath = $uploadDir . $filename;

        // Move file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            if (extension_loaded('gd')) {
                $this->resizeImage($targetPath, 800);
            }
            return 'uploads/' . $filename;
        }

        return $existingPath;
    }

    // GD image resize algorithm to maintain aspect ratio and prevent massive storage bloat
    private function resizeImage($filePath, $maxWidth) {
        list($origWidth, $origHeight, $type) = getimagesize($filePath);
        
        if ($origWidth <= $maxWidth) {
            return; // No need to resize
        }

        $aspectRatio = $origWidth / $origHeight;
        $newWidth = $maxWidth;
        $newHeight = round($maxWidth / $aspectRatio);

        $image = null;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($filePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($filePath);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagecreatefromwebp')) {
                    $image = imagecreatefromwebp($filePath);
                }
                break;
        }

        if (!$image) return;

        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG and WebP
        if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($newImage, $filePath, 85);
                break;
            case IMAGETYPE_PNG:
                imagepng($newImage, $filePath, 6);
                break;
            case IMAGETYPE_WEBP:
                if (function_exists('imagewebp')) {
                    imagewebp($newImage, $filePath, 80);
                }
                break;
        }

        imagedestroy($image);
        imagedestroy($newImage);
    }
}
