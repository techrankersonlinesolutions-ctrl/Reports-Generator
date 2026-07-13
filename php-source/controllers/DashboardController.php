<?php
/**
 * DashboardController - Handles metrics and recent activities
 */

class DashboardController {
    private $db;
    private $reportModel;

    public function __construct($database) {
        $this->db = $database;
        require_once __DIR__ . '/../models/Report.php';
        $this->reportModel = new Report($this->db);
    }

    public function index() {
        if (!isLoggedIn()) {
            header('Location: index.php?route=login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // Fetch stats
        // 1. Total reports
        $query_total = "SELECT COUNT(*) as total FROM reports WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query_total);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $totalReports = $stmt->fetch()['total'];

        // 2. Reports generated this month
        $currentMonth = date('F');
        $currentYear = date('Y');
        $query_month = "SELECT COUNT(*) as total FROM reports WHERE user_id = :user_id AND report_month = :month AND report_year = :year";
        $stmt_m = $this->db->prepare($query_month);
        $stmt_m->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt_m->bindParam(':month', $currentMonth, PDO::PARAM_STR);
        $stmt_m->bindParam(':year', $currentYear, PDO::PARAM_STR);
        $stmt_m->execute();
        $reportsThisMonth = $stmt_m->fetch()['total'];

        // 3. Recent reports (limit 5)
        $query_recent = "SELECT id, business_name, report_month, report_year, generated_date, created_at 
                         FROM reports 
                         WHERE user_id = :user_id 
                         ORDER BY id DESC LIMIT 5";
        $stmt_r = $this->db->prepare($query_recent);
        $stmt_r->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt_r->execute();
        $recentReports = $stmt_r->fetchAll();

        require_once __DIR__ . '/../views/dashboard.php';
    }
}
