<?php
/**
 * Setting Model
 */

class Setting {
    private $db;
    private $table = "settings";

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    public function getSettings() {
        $query = "SELECT * FROM " . $this->table . " WHERE id = 1 LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function updateSettings($data) {
        $query = "UPDATE " . $this->table . " SET 
                  default_company_name = :default_company_name,
                  default_email = :default_email,
                  default_phone = :default_phone,
                  default_website = :default_website,
                  default_footer = :default_footer,
                  pdf_margin_top = :pdf_margin_top,
                  pdf_margin_bottom = :pdf_margin_bottom,
                  pdf_margin_left = :pdf_margin_left,
                  pdf_margin_right = :pdf_margin_right,
                  primary_color = :primary_color,
                  secondary_color = :secondary_color
                  WHERE id = 1";
                  
        $stmt = $this->db->prepare($query);
        
        $stmt->bindParam(':default_company_name', $data['default_company_name'], PDO::PARAM_STR);
        $stmt->bindParam(':default_email', $data['default_email'], PDO::PARAM_STR);
        $stmt->bindParam(':default_phone', $data['default_phone'], PDO::PARAM_STR);
        $stmt->bindParam(':default_website', $data['default_website'], PDO::PARAM_STR);
        $stmt->bindParam(':default_footer', $data['default_footer'], PDO::PARAM_STR);
        $stmt->bindParam(':pdf_margin_top', $data['pdf_margin_top'], PDO::PARAM_INT);
        $stmt->bindParam(':pdf_margin_bottom', $data['pdf_margin_bottom'], PDO::PARAM_INT);
        $stmt->bindParam(':pdf_margin_left', $data['pdf_margin_left'], PDO::PARAM_INT);
        $stmt->bindParam(':pdf_margin_right', $data['pdf_margin_right'], PDO::PARAM_INT);
        $stmt->bindParam(':primary_color', $data['primary_color'], PDO::PARAM_STR);
        $stmt->bindParam(':secondary_color', $data['secondary_color'], PDO::PARAM_STR);
        
        return $stmt->execute();
    }
}
