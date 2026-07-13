<?php
/**
 * Report Model
 */

class Report {
    private $db;
    private $table = "reports";

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    public function getAll($userId) {
        $query = "SELECT id, business_name, report_month, report_year, generated_date, created_at 
                  FROM " . $this->table . " 
                  WHERE user_id = :user_id 
                  ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $report = $stmt->fetch();

        if ($report) {
            // Fetch Keywords
            $query_kw = "SELECT keyword, prev_rank, curr_rank FROM keyword_rankings WHERE report_id = :id";
            $stmt_kw = $this->db->prepare($query_kw);
            $stmt_kw->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_kw->execute();
            $report['keywords'] = $stmt_kw->fetchAll();

            // Fetch Backlinks
            $query_bl = "SELECT category, url, status FROM backlinks WHERE report_id = :id";
            $stmt_bl = $this->db->prepare($query_bl);
            $stmt_bl->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_bl->execute();
            $report['backlinks'] = $stmt_bl->fetchAll();
        }

        return $report;
    }

    public function create($userId, $data) {
        try {
            $this->db->beginTransaction();

            $query = "INSERT INTO " . $this->table . " SET 
                      user_id = :user_id,
                      business_name = :business_name,
                      report_month = :report_month,
                      report_year = :report_year,
                      generated_date = :generated_date,
                      business_logo = :business_logo,
                      cover_image = :cover_image,
                      people_viewed = :people_viewed,
                      search_direct = :search_direct,
                      search_discovery = :search_discovery,
                      profile_interactions = :profile_interactions,
                      reviews_count = :reviews_count,
                      rating_average = :rating_average,
                      views_maps = :views_maps,
                      views_search = :views_search,
                      heatmap_image = :heatmap_image,
                      avg_rank = :avg_rank,
                      top_3_percentage = :top_3_percentage,
                      points_tracked = :points_tracked,
                      insight_text = :insight_text,
                      geofence_map_url = :geofence_map_url,
                      next_month_plan = :next_month_plan,
                      company_email = :company_email,
                      company_phone = :company_phone,
                      company_website = :company_website,
                      footer_notes = :footer_notes";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':business_name', $data['business_name'], PDO::PARAM_STR);
            $stmt->bindParam(':report_month', $data['report_month'], PDO::PARAM_STR);
            $stmt->bindParam(':report_year', $data['report_year'], PDO::PARAM_STR);
            $stmt->bindParam(':generated_date', $data['generated_date'], PDO::PARAM_STR);
            $stmt->bindParam(':business_logo', $data['business_logo'], PDO::PARAM_STR);
            $stmt->bindParam(':cover_image', $data['cover_image'], PDO::PARAM_STR);
            
            $stmt->bindParam(':people_viewed', $data['people_viewed'], PDO::PARAM_INT);
            $stmt->bindParam(':search_direct', $data['search_direct'], PDO::PARAM_INT);
            $stmt->bindParam(':search_discovery', $data['search_discovery'], PDO::PARAM_INT);
            $stmt->bindParam(':profile_interactions', $data['profile_interactions'], PDO::PARAM_INT);
            $stmt->bindParam(':reviews_count', $data['reviews_count'], PDO::PARAM_INT);
            $stmt->bindParam(':rating_average', $data['rating_average'], PDO::PARAM_STR);
            $stmt->bindParam(':views_maps', $data['views_maps'], PDO::PARAM_INT);
            $stmt->bindParam(':views_search', $data['views_search'], PDO::PARAM_INT);
            
            $stmt->bindParam(':heatmap_image', $data['heatmap_image'], PDO::PARAM_STR);
            $stmt->bindParam(':avg_rank', $data['avg_rank'], PDO::PARAM_STR);
            $stmt->bindParam(':top_3_percentage', $data['top_3_percentage'], PDO::PARAM_STR);
            $stmt->bindParam(':points_tracked', $data['points_tracked'], PDO::PARAM_INT);
            $stmt->bindParam(':insight_text', $data['insight_text'], PDO::PARAM_STR);
            
            $stmt->bindParam(':geofence_map_url', $data['geofence_map_url'], PDO::PARAM_STR);
            $stmt->bindParam(':next_month_plan', $data['next_month_plan'], PDO::PARAM_STR);
            
            $stmt->bindParam(':company_email', $data['company_email'], PDO::PARAM_STR);
            $stmt->bindParam(':company_phone', $data['company_phone'], PDO::PARAM_STR);
            $stmt->bindParam(':company_website', $data['company_website'], PDO::PARAM_STR);
            $stmt->bindParam(':footer_notes', $data['footer_notes'], PDO::PARAM_STR);

            $stmt->execute();
            $reportId = $this->db->lastInsertId();

            // Insert Keywords (Step 3)
            if (!empty($data['keywords']) && is_array($data['keywords'])) {
                $query_kw = "INSERT INTO keyword_rankings (report_id, keyword, prev_rank, curr_rank) VALUES (:report_id, :keyword, :prev_rank, :curr_rank)";
                $stmt_kw = $this->db->prepare($query_kw);
                foreach ($data['keywords'] as $kw) {
                    if (empty($kw['keyword'])) continue;
                    $stmt_kw->bindParam(':report_id', $reportId, PDO::PARAM_INT);
                    $stmt_kw->bindParam(':keyword', $kw['keyword'], PDO::PARAM_STR);
                    $stmt_kw->bindParam(':prev_rank', $kw['prev_rank'], PDO::PARAM_INT);
                    $stmt_kw->bindParam(':curr_rank', $kw['curr_rank'], PDO::PARAM_INT);
                    $stmt_kw->execute();
                }
            }

            // Insert Backlinks (Step 5)
            if (!empty($data['backlinks']) && is_array($data['backlinks'])) {
                $query_bl = "INSERT INTO backlinks (report_id, category, url, status) VALUES (:report_id, :category, :url, :status)";
                $stmt_bl = $this->db->prepare($query_bl);
                foreach ($data['backlinks'] as $bl) {
                    if (empty($bl['url'])) continue;
                    $stmt_bl->bindParam(':report_id', $reportId, PDO::PARAM_INT);
                    $stmt_bl->bindParam(':category', $bl['category'], PDO::PARAM_STR);
                    $stmt_bl->bindParam(':url', $bl['url'], PDO::PARAM_STR);
                    $stmt_bl->bindParam(':status', $bl['status'], PDO::PARAM_STR);
                    $stmt_bl->execute();
                }
            }

            $this->db->commit();
            return $reportId;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Report creation error: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $this->db->beginTransaction();

            $query = "UPDATE " . $this->table . " SET 
                      business_name = :business_name,
                      report_month = :report_month,
                      report_year = :report_year,
                      generated_date = :generated_date,
                      business_logo = :business_logo,
                      cover_image = :cover_image,
                      people_viewed = :people_viewed,
                      search_direct = :search_direct,
                      search_discovery = :search_discovery,
                      profile_interactions = :profile_interactions,
                      reviews_count = :reviews_count,
                      rating_average = :rating_average,
                      views_maps = :views_maps,
                      views_search = :views_search,
                      heatmap_image = :heatmap_image,
                      avg_rank = :avg_rank,
                      top_3_percentage = :top_3_percentage,
                      points_tracked = :points_tracked,
                      insight_text = :insight_text,
                      geofence_map_url = :geofence_map_url,
                      next_month_plan = :next_month_plan,
                      company_email = :company_email,
                      company_phone = :company_phone,
                      company_website = :company_website,
                      footer_notes = :footer_notes
                      WHERE id = :id";

            $stmt = $this->db->prepare($query);

            $stmt->bindParam(':business_name', $data['business_name'], PDO::PARAM_STR);
            $stmt->bindParam(':report_month', $data['report_month'], PDO::PARAM_STR);
            $stmt->bindParam(':report_year', $data['report_year'], PDO::PARAM_STR);
            $stmt->bindParam(':generated_date', $data['generated_date'], PDO::PARAM_STR);
            $stmt->bindParam(':business_logo', $data['business_logo'], PDO::PARAM_STR);
            $stmt->bindParam(':cover_image', $data['cover_image'], PDO::PARAM_STR);
            
            $stmt->bindParam(':people_viewed', $data['people_viewed'], PDO::PARAM_INT);
            $stmt->bindParam(':search_direct', $data['search_direct'], PDO::PARAM_INT);
            $stmt->bindParam(':search_discovery', $data['search_discovery'], PDO::PARAM_INT);
            $stmt->bindParam(':profile_interactions', $data['profile_interactions'], PDO::PARAM_INT);
            $stmt->bindParam(':reviews_count', $data['reviews_count'], PDO::PARAM_INT);
            $stmt->bindParam(':rating_average', $data['rating_average'], PDO::PARAM_STR);
            $stmt->bindParam(':views_maps', $data['views_maps'], PDO::PARAM_INT);
            $stmt->bindParam(':views_search', $data['views_search'], PDO::PARAM_INT);
            
            $stmt->bindParam(':heatmap_image', $data['heatmap_image'], PDO::PARAM_STR);
            $stmt->bindParam(':avg_rank', $data['avg_rank'], PDO::PARAM_STR);
            $stmt->bindParam(':top_3_percentage', $data['top_3_percentage'], PDO::PARAM_STR);
            $stmt->bindParam(':points_tracked', $data['points_tracked'], PDO::PARAM_INT);
            $stmt->bindParam(':insight_text', $data['insight_text'], PDO::PARAM_STR);
            
            $stmt->bindParam(':geofence_map_url', $data['geofence_map_url'], PDO::PARAM_STR);
            $stmt->bindParam(':next_month_plan', $data['next_month_plan'], PDO::PARAM_STR);
            
            $stmt->bindParam(':company_email', $data['company_email'], PDO::PARAM_STR);
            $stmt->bindParam(':company_phone', $data['company_phone'], PDO::PARAM_STR);
            $stmt->bindParam(':company_website', $data['company_website'], PDO::PARAM_STR);
            $stmt->bindParam(':footer_notes', $data['footer_notes'], PDO::PARAM_STR);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            // Clear old Keywords
            $query_del_kw = "DELETE FROM keyword_rankings WHERE report_id = :id";
            $stmt_del_kw = $this->db->prepare($query_del_kw);
            $stmt_del_kw->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_del_kw->execute();

            // Re-insert Keywords
            if (!empty($data['keywords']) && is_array($data['keywords'])) {
                $query_kw = "INSERT INTO keyword_rankings (report_id, keyword, prev_rank, curr_rank) VALUES (:report_id, :keyword, :prev_rank, :curr_rank)";
                $stmt_kw = $this->db->prepare($query_kw);
                foreach ($data['keywords'] as $kw) {
                    if (empty($kw['keyword'])) continue;
                    $stmt_kw->bindParam(':report_id', $id, PDO::PARAM_INT);
                    $stmt_kw->bindParam(':keyword', $kw['keyword'], PDO::PARAM_STR);
                    $stmt_kw->bindParam(':prev_rank', $kw['prev_rank'], PDO::PARAM_INT);
                    $stmt_kw->bindParam(':curr_rank', $kw['curr_rank'], PDO::PARAM_INT);
                    $stmt_kw->execute();
                }
            }

            // Clear old Backlinks
            $query_del_bl = "DELETE FROM backlinks WHERE report_id = :id";
            $stmt_del_bl = $this->db->prepare($query_del_bl);
            $stmt_del_bl->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_del_bl->execute();

            // Re-insert Backlinks
            if (!empty($data['backlinks']) && is_array($data['backlinks'])) {
                $query_bl = "INSERT INTO backlinks (report_id, category, url, status) VALUES (:report_id, :category, :url, :status)";
                $stmt_bl = $this->db->prepare($query_bl);
                foreach ($data['backlinks'] as $bl) {
                    if (empty($bl['url'])) continue;
                    $stmt_bl->bindParam(':report_id', $id, PDO::PARAM_INT);
                    $stmt_bl->bindParam(':category', $bl['category'], PDO::PARAM_STR);
                    $stmt_bl->bindParam(':url', $bl['url'], PDO::PARAM_STR);
                    $stmt_bl->bindParam(':status', $bl['status'], PDO::PARAM_STR);
                    $stmt_bl->execute();
                }
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Report update error: " . $e->getMessage());
            return false;
        }
    }

    public function duplicate($id) {
        $source = $this->getById($id);
        if (!$source) return false;

        $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        $nextMonth = $source['report_month'];
        $nextYear = $source['report_year'];
        
        $currIndex = array_search($source['report_month'], $months);
        if ($currIndex !== false) {
            if ($currIndex === 11) {
                $nextMonth = $months[0];
                $nextYear = strval(intval($source['report_year']) + 1);
            } else {
                $nextMonth = $months[$currIndex + 1];
            }
        }

        $duplicatedData = [
            'business_name' => $source['business_name'],
            'report_month' => $nextMonth,
            'report_year' => $nextYear,
            'generated_date' => date('Y-m-d'),
            'business_logo' => $source['business_logo'],
            'cover_image' => $source['cover_image'],
            'people_viewed' => $source['people_viewed'],
            'search_direct' => $source['search_direct'],
            'search_discovery' => $source['search_discovery'],
            'profile_interactions' => $source['profile_interactions'],
            'reviews_count' => $source['reviews_count'],
            'rating_average' => $source['rating_average'],
            'views_maps' => $source['views_maps'],
            'views_search' => $source['views_search'],
            'heatmap_image' => $source['heatmap_image'],
            'avg_rank' => $source['avg_rank'],
            'top_3_percentage' => $source['top_3_percentage'],
            'points_tracked' => $source['points_tracked'],
            'insight_text' => $source['insight_text'],
            'geofence_map_url' => $source['geofence_map_url'],
            'next_month_plan' => $source['next_month_plan'],
            'company_email' => $source['company_email'],
            'company_phone' => $source['company_phone'],
            'company_website' => $source['company_website'],
            'footer_notes' => $source['footer_notes'],
            'keywords' => [],
            'backlinks' => []
        ];

        // Format Keywords
        foreach ($source['keywords'] as $kw) {
            $duplicatedData['keywords'][] = [
                'keyword' => $kw['keyword'],
                'prev_rank' => $kw['curr_rank'], // current rank becomes previous rank!
                'curr_rank' => $kw['curr_rank']  // placeholder to update
            ];
        }

        // Format Backlinks
        foreach ($source['backlinks'] as $bl) {
            $duplicatedData['backlinks'][] = [
                'category' => $bl['category'],
                'url' => $bl['url'],
                'status' => $bl['status']
            ];
        }

        return $this->create($source['user_id'], $duplicatedData);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
