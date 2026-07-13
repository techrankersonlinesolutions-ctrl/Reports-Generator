<?php
/**
 * User Model
 */

class User {
    private $db;
    private $table = "users";

    public function __construct($databaseConnection) {
        $this->db = $databaseConnection;
    }

    public function getById($id) {
        $query = "SELECT id, name, email, company_name, company_phone, company_email, company_website, company_footer FROM " . $this->table . " WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function login($email, $password) {
        $query = "SELECT id, name, email, password FROM " . $this->table . " WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    public function updateProfile($id, $data) {
        $sql = "UPDATE " . $this->table . " SET 
                name = :name, 
                email = :email, 
                company_name = :company_name, 
                company_phone = :company_phone, 
                company_email = :company_email, 
                company_website = :company_website, 
                company_footer = :company_footer";
        
        if (!empty($data['password'])) {
            $sql .= ", password = :password";
        }
        
        $sql .= " WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindParam(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindParam(':company_name', $data['company_name'], PDO::PARAM_STR);
        $stmt->bindParam(':company_phone', $data['company_phone'], PDO::PARAM_STR);
        $stmt->bindParam(':company_email', $data['company_email'], PDO::PARAM_STR);
        $stmt->bindParam(':company_website', $data['company_website'], PDO::PARAM_STR);
        $stmt->bindParam(':company_footer', $data['company_footer'], PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if (!empty($data['password'])) {
            $hashed = password_hash($data['password'], PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $hashed, PDO::PARAM_STR);
        }
        
        return $stmt->execute();
    }
}
