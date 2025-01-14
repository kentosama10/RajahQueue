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
            // First, verify the user exists
            $userCheckStmt = $this->db->prepare("SELECT id FROM users WHERE id = ?");
            $userCheckStmt->execute([$userId]);
            if (!$userCheckStmt->fetch()) {
                error_log("User ID $userId does not exist in users table");
                return false;
            }

            // Check if the counter exists
            $checkStmt = $this->db->prepare("SELECT counter_number FROM counters WHERE counter_number = ?");
            $checkStmt->execute([$counterNumber]);
            if (!$checkStmt->fetch()) {
                error_log("Counter $counterNumber does not exist");
                return false;
            }

            // Release any existing counter assignments for this user
            $releaseStmt = $this->db->prepare("UPDATE counters SET active_user_id = NULL WHERE active_user_id = ?");
            $releaseStmt->execute([$userId]);

            // Update the new counter assignment with explicit type casting
            $stmt = $this->db->prepare("
                UPDATE counters 
                SET active_user_id = CAST(? AS SIGNED), 
                    updated_at = CURRENT_TIMESTAMP
                WHERE counter_number = ?
            ");
            $success = $stmt->execute([$userId, $counterNumber]);
        
            if (!$success) {
                error_log("Failed to update counter: " . json_encode($stmt->errorInfo()));
                return false;
            }
        
            // Verify the update
            $verifyStmt = $this->db->prepare("
                SELECT active_user_id 
                FROM counters 
                WHERE counter_number = ? AND active_user_id = ?
            ");
            $verifyStmt->execute([$counterNumber, $userId]);
            $result = $verifyStmt->fetch();
            
            if (!$result) {
                error_log("Verification failed: Counter update did not persist. Counter: $counterNumber, User: $userId");
                return false;
            }

            error_log("Successfully updated counter $counterNumber for user $userId");
            return true;
        } catch (PDOException $e) {
            error_log("Database error in updateCounter: " . $e->getMessage());
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
        return $this->getCounter($counterNumber); // Reuse getCounter
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
    public function createUser($username, $password)
    {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        return $stmt->execute([$username, $hashedPassword]);
    }


}
