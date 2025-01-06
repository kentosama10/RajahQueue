<?php

require_once '../core/Model.php';

class User extends Model {
    public function getUserByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update the counter for a user
    public function updateCounter($userId, $counter) {
        $stmt = $this->db->prepare("UPDATE users SET counter = ? WHERE id = ?");
        return $stmt->execute([$counter, $userId]);
    }

    // Get the counter for a user
    public function getCounter($userId) {
        $stmt = $this->db->prepare("SELECT counter FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['counter'] : null;
    }

    // Create a new user with hashed password
    public function createUser($username, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        return $stmt->execute([$username, $hashedPassword]);
    }
}
