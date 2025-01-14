<?php
// app/models/Dashboard.php

require_once '../core/Model.php';

class Dashboard extends Model {
    public function getCountByStatus($status) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM queue WHERE status = ?");
        $stmt->execute([$status]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function getCompletedToday() {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM queue 
            WHERE status = 'Done' 
            AND DATE(created_at) = CURDATE()
        ");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    public function getRecallHistory() {
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

    public function searchQueue($searchTerm, $page = 1, $limit = 10) {
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

    public function getSearchCount($searchTerm) {
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

    public function getActiveQueue($page = 1, $limit = 10) {
        $offset = ($page - 1) * $limit;

        $stmt = $this->db->prepare("
            SELECT 
                q.*, 
                u.first_name, 
                u.last_name,
                c.counter_number
            FROM queue q
            LEFT JOIN users u ON q.serving_user_id = u.id
            LEFT JOIN counters c ON c.active_user_id = u.id
            WHERE q.status IN ('Waiting', 'Serving', 'No Show')
            ORDER BY 
                CASE q.status
                    WHEN 'Serving' THEN 1
                    WHEN 'Waiting' THEN 2
                    ELSE 3
                END,
                CASE q.priority
                    WHEN 'Yes' THEN 1
                    ELSE 2
                END,
                q.created_at ASC
            LIMIT :limit OFFSET :offset
        ");
        
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalQueueCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM queue WHERE status IN ('Waiting', 'Serving')");
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
}