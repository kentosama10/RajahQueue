<?php
// app/models/Queue.php

require_once '../core/Model.php';

class Queue extends Model
{
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM queue ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($customerName, $serviceType, $region = null, $priority = 'No', $priorityType = null)
    {
        // Ensure reset logic runs
        $this->resetQueueNumbersIfNeeded();

        // Determine the service initial letter
        $serviceInitial = $this->getServiceInitial($serviceType);

        // Generate queue number
        $nextNumber = $this->getNextQueueNumberByService($serviceInitial);
        $queueNumber = $serviceInitial . '-' . $nextNumber;

        // Insert into database
        $stmt = $this->db->prepare("INSERT INTO queue 
            (customer_name, service_type, region, priority, priority_type, queue_number) 
            VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$customerName, $serviceType, $region, $priority, $priorityType, $queueNumber]);

        // Return the queue number for confirmation
        return $queueNumber;
    }

    private function resetQueueNumbersIfNeeded()
    {
        // Check the last reset date in the queue_reset table
        $stmt = $this->db->query("SELECT reset_date FROM queue_reset ORDER BY reset_date DESC LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $lastResetDate = $result ? $result['reset_date'] : null;

        // If the reset hasn't occurred today, perform the reset
        if ($lastResetDate !== date('Y-m-d')) {
            $this->resetQueueNumbers();
        }
    }

    private function resetQueueNumbers()
    {
        // Update the status of the queue for a fresh start
        $stmt = $this->db->prepare("UPDATE queue SET status = 'Archived' WHERE DATE(created_at) < CURDATE()");
        $stmt->execute();

        // Insert the new reset date into the queue_reset table
        $stmt = $this->db->prepare("INSERT INTO queue_reset (reset_date) VALUES (CURDATE())");
        $stmt->execute();
    }

    private function getServiceInitial($serviceType)
    {
        switch ($serviceType) {
            case 'Tour Packages':
                return 'T';
            case 'Travel Insurance':
                return 'I'; // Use I for Travel Insurance to avoid conflict
            case 'Visa':
                return 'V';
            case 'Flights':
                return 'F';
            default:
                return 'U'; // Default to 'U' for Unknown
        }
    }

    private function getNextQueueNumberByService($serviceInitial)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS count FROM queue WHERE queue_number LIKE ?");
        $stmt->execute([$serviceInitial . '-%']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] + 1; // Increment count for the next queue number
    }

    public function getCountByStatus($status)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM queue WHERE status = ?");
        $stmt->execute([$status]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function getCompletedToday()
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM queue 
            WHERE status = 'Done' 
            AND DATE(created_at) = CURDATE()
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function getActiveQueue($page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit; // Calculate the offset for pagination

        $stmt = $this->db->prepare("
            SELECT * FROM queue 
            WHERE status IN ('Waiting', 'Serving', 'No Show')
            ORDER BY 
                CASE status
                    WHEN 'Serving' THEN 1
                    WHEN 'Waiting' THEN 2
                    ELSE 3
                END,
                CASE priority
                    WHEN 'Yes' THEN 1
                    ELSE 2
                END,
                created_at ASC
            LIMIT :limit OFFSET :offset
        ");
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalQueueCount()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM queue WHERE status IN ('Waiting', 'Serving')");
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function updateStatus($queueNumber, $status)
    {
        $allowedStatuses = ['Waiting', 'Serving', 'Done', 'Skipped', 'No Show', 'Recalled'];
        
        if (!in_array($status, $allowedStatuses)) {
            return false;
        }

        try {
            // If the status is 'Recalled', set it to 'Waiting'
            if ($status === 'Recalled') {
                $status = 'Serving';
            }

            $stmt = $this->db->prepare("
                UPDATE queue 
                SET 
                    status = ?,
                    payment_status = CASE WHEN ? = 'Done' THEN 'Pending' ELSE payment_status END,
                    updated_at = CURRENT_TIMESTAMP
                WHERE queue_number = ?
            ");
            
            $result = $stmt->execute([$status, $status, $queueNumber]);
            return $result && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating queue status: " . $e->getMessage());
            return false;
        }
    }

    public function updatePaymentStatus($queueNumber, $paymentStatus) {
        try {
            $stmt = $this->db->prepare("
                UPDATE queue 
                SET 
                    payment_status = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE queue_number = ?
            ");
            
            $result = $stmt->execute([$paymentStatus, $queueNumber]);
            return $result && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating payment status: " . $e->getMessage());
            return false;
        }
    }

    public function getRecallHistory()
    {
        $stmt = $this->db->query("
            SELECT 
                queue_number,
                status as action,
                updated_at
            FROM queue 
            WHERE status IN ('No Show', 'Recalled', 'Serving', 'Done', 'Skipped')
            AND DATE(updated_at) = CURDATE()
            ORDER BY updated_at DESC
            LIMIT 10
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchQueue($searchTerm, $page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit; // Calculate the offset for pagination

        $stmt = $this->db->prepare("
            SELECT * FROM queue 
            WHERE (customer_name LIKE :searchTerm OR queue_number LIKE :searchTerm)
            AND status IN ('Waiting', 'Serving', 'No Show', 'Recalled')
            ORDER BY 
                CASE status
                    WHEN 'Serving' THEN 1
                    WHEN 'No Show' THEN 2
                    WHEN 'Waiting' THEN 3
                    ELSE 4
                END,
                CASE priority
                    WHEN 'Yes' THEN 1
                    ELSE 2
                END,
                created_at ASC
            LIMIT :limit OFFSET :offset
        ");
        
        $searchTerm = "%$searchTerm%"; // Prepare the search term for LIKE
        $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSearchCount($searchTerm)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM queue 
            WHERE (customer_name LIKE :searchTerm OR queue_number LIKE :searchTerm)
            AND status IN ('Waiting', 'Serving', 'No Show')
        ");
        
        $searchTerm = "%$searchTerm%"; // Prepare the search term for LIKE
        $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function getPaymentQueue() {
        $stmt = $this->db->query("SELECT * FROM queue WHERE payment_status = 'Pending' ORDER BY updated_at ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function completePayment($queueNumber) {
        try {
            $stmt = $this->db->prepare("
                UPDATE queue 
                SET 
                    payment_status = 'Completed',
                    updated_at = CURRENT_TIMESTAMP
                WHERE queue_number = ?
            ");
            
            $result = $stmt->execute([$queueNumber]);
            return $result && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error completing payment: " . $e->getMessage());
            return false;
        }
    }

    public function getQueueData() {
        $stmt = $this->db->query("SELECT * FROM queue WHERE status = 'Done' AND payment_status = 'Pending' ORDER BY updated_at ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
