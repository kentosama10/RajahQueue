<?php

require_once '../core/Model.php';

class User extends Model
{
    public function getUserByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update the counter for a user
    public function updateCounter($counterNumber, $userId)
    {
        try {
            // First check if the counter is already assigned to someone else
            $checkStmt = $this->db->prepare("
                SELECT active_user_id 
                FROM counters 
                WHERE counter_number = ? AND active_user_id IS NOT NULL
            ");
            $checkStmt->execute([$counterNumber]);
            $existingAssignment = $checkStmt->fetch(PDO::FETCH_ASSOC);

            if ($existingAssignment && $existingAssignment['active_user_id'] != $userId) {
                error_log("Counter $counterNumber is already assigned to user {$existingAssignment['active_user_id']}");
                return false;
            }

            // Begin transaction
            $this->db->beginTransaction();

            // Release any other counters assigned to this user
            $releaseStmt = $this->db->prepare("
                UPDATE counters 
                SET active_user_id = NULL 
                WHERE active_user_id = ?
            ");
            $releaseStmt->execute([$userId]);

            // Assign the new counter
            $updateStmt = $this->db->prepare("
                UPDATE counters 
                SET active_user_id = ? 
                WHERE counter_number = ?
            ");
            $success = $updateStmt->execute([$userId, $counterNumber]);

            if ($success) {
                $this->db->commit();
                return true;
            } else {
                $this->db->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error updating counter: " . $e->getMessage());
            return false;
        }
    }


    // Get the counter for a user
    public function getCounter($counterNumber)
    {
        $stmt = $this->db->prepare("SELECT active_user_id FROM counters WHERE counter_number = ?");
        $stmt->execute([$counterNumber]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$result) {
            error_log("Counter not found: $counterNumber");
            return null;
        }
    
        return $result['active_user_id'];
    }
    

    public function getActiveUserForCounter($counterNumber)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT u.id, u.username, u.first_name, u.last_name
                FROM users u
                INNER JOIN counters c ON c.active_user_id = u.id
                WHERE c.counter_number = ?
                LIMIT 1
            ");
            
            $stmt->execute([$counterNumber]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting active user for counter: " . $e->getMessage());
            return null;
        }
    }

    
    public function releaseCounter($userId) {
        try {
            $sql = "UPDATE counters SET active_user_id = NULL WHERE active_user_id = :userId";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['userId' => $userId]);
        } catch (PDOException $e) {
            error_log("Failed to release counter: " . $e->getMessage());
            return false;
        }
    }
    
    
    // Create a new user with hashed password
    public function createUser($firstName, $lastName, $username, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (first_name, last_name, username, password) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$firstName, $lastName, $username, $hashedPassword]);
    }

    public function getUserCounter($userId) {
        try {
            $stmt = $this->db->prepare("
                SELECT counter_number 
                FROM counters 
                WHERE active_user_id = ?
                LIMIT 1
            ");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting user counter: " . $e->getMessage());
            return null;
        }
    }

    public function getActiveCounters() {
        $stmt = $this->db->query("
            SELECT counter_number, u.first_name 
            FROM counters c
            INNER JOIN users u ON c.active_user_id = u.id
            WHERE c.active_user_id IS NOT NULL
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
