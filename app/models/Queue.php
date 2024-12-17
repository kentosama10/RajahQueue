<?php
// app/models/Queue.php

require_once '../core/Model.php';

class Queue extends Model {
    public function getAll() {
        $stmt = $this->db->query("SELECT * FROM queue ORDER BY id ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function add($customerName, $serviceType, $region = null, $priority = 'No', $priorityType = null) {
    $stmt = $this->db->prepare("INSERT INTO queue 
        (customer_name, service_type, region, priority, priority_type, queue_number) 
        VALUES (?, ?, ?, ?, ?, ?)");
    $queueNumber = $this->getNextQueueNumber();
    $stmt->execute([$customerName, $serviceType, $region, $priority, $priorityType, $queueNumber]);
}

    

    private function getNextQueueNumber() {
        $stmt = $this->db->query("SELECT IFNULL(MAX(queue_number), 0) + 1 AS next_number FROM queue");
        return $stmt->fetch(PDO::FETCH_ASSOC)['next_number'];
    }
}
