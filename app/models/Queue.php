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

    public function add($customerName, $serviceType, $region = null, $priority = 'No', $priorityType = null) {
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
    
    // Determine the first letter for the queue based on service type
    private function getServiceInitial($serviceType) {
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
    
    // Get the next queue number based on the service initial
    private function getNextQueueNumberByService($serviceInitial) {
        $stmt = $this->db->prepare("SELECT COUNT(*) AS count FROM queue WHERE queue_number LIKE ?");
        $stmt->execute([$serviceInitial . '-%']);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] + 1; // Increment count for the next queue number
    }
    

}
