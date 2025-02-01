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

    public function add($customerName, $serviceType)
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
            (customer_name, service_type, queue_number, reset_flag) 
            VALUES (?, ?, ?, 0)");
        $stmt->execute([$customerName, $serviceType, $queueNumber]);

        // Return the queue number for confirmation
        return $queueNumber;
    }
    public function addQueueItem($customerName, $serviceType, $paymentStatus = null)
    {
        try {
            // Ensure reset logic runs
            $this->resetQueueNumbersIfNeeded();

            // Determine the service initial letter
            $serviceInitial = $this->getServiceInitial($serviceType);

            // Generate queue number
            $nextNumber = $this->getNextQueueNumberByService($serviceInitial);
            $queueNumber = $serviceInitial . '-' . $nextNumber;

            // Determine the status
            $status = ($serviceType === 'Payment') ? 'Done' : 'Waiting';

            // Insert into database
            $stmt = $this->db->prepare("
                INSERT INTO queue (customer_name, service_type, payment_status, queue_number, status, created_at, updated_at)
                VALUES (:customer_name, :service_type, :payment_status, :queue_number, :status, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
            ");
            $stmt->bindParam(':customer_name', $customerName);
            $stmt->bindParam(':service_type', $serviceType);
            $stmt->bindParam(':payment_status', $paymentStatus);
            $stmt->bindParam(':queue_number', $queueNumber);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

            // Return the queue number for confirmation
            return $queueNumber;
        } catch (PDOException $e) {
            error_log("Error adding queue item: " . $e->getMessage());
            return null;
        }
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
        // Update the status of currently serving and waiting queue numbers to "Skipped"
        $stmt = $this->db->prepare("UPDATE queue SET status = 'Skipped' WHERE status IN ('Serving', 'Waiting')");
        $stmt->execute();

        // Archive old queue numbers by updating the reset_flag column
        $stmt = $this->db->prepare("UPDATE queue SET reset_flag = 1 WHERE reset_flag = 0");
        $stmt->execute();

        // Insert the new reset date into the queue_reset table
        $stmt = $this->db->prepare("INSERT INTO queue_reset (reset_date) VALUES (CURDATE())");
        $stmt->execute();
    }



    private function getServiceInitial($serviceType)
    {
        switch ($serviceType) {
            case 'Tours / Cruise':
                return 'Tours';
            case 'Travel Insurance':
                return 'I'; // Use I for Travel Insurance to avoid conflict
            case 'Visa':
                return 'V';
            case 'Flights':
                return 'Flights';
            case 'Multiple Services':
                return 'M';
            case 'Payment':
                return 'P';
            case 'Booth':
                return 'B';
            default:
                return 'U'; // Default to 'U' for Unknown
        }
    }

    private function getNextQueueNumberByService($serviceInitial)
    {
        // Count rows where reset_flag = 0 for the given service
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM queue WHERE reset_flag = 0 AND LEFT(queue_number, 1) = ?");
        $stmt->execute([$serviceInitial]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] + 1; // Next queue number
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

    public function getActiveQueue($page = 1, $limit = 20)
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

    public function updateStatus($queueNumber, $status, $userId = null, $paymentStatus = null)
    {
        $allowedStatuses = ['Waiting', 'Serving', 'Done', 'Skipped', 'No Show', 'Recalled'];

        if (!in_array($status, $allowedStatuses)) {
            return ['success' => false, 'message' => 'Invalid status provided'];
        }

        try {
            // First, check if this queue number is valid for serving
            if ($status === 'Serving') {
                // Check if the queue number exists with reset_flag = 0 and current status
                $checkStmt = $this->db->prepare("
                    SELECT status, reset_flag, serving_user_id 
                    FROM queue 
                    WHERE queue_number = ?
                    AND reset_flag = 0
                    ORDER BY created_at DESC
                    LIMIT 1
                ");
                $checkStmt->execute([$queueNumber]);
                $queueItem = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if (!$queueItem) {
                    return ['success' => false, 'message' => 'Queue number not found or has been reset'];
                }

                if ($queueItem['status'] === 'Done') {
                    return ['success' => false, 'message' => 'This queue number has already been completed'];
                }

                if ($queueItem['status'] === 'Serving' && $queueItem['serving_user_id'] !== null) {
                    return ['success' => false, 'message' => 'This customer is already being served by another user'];
                }
            }

            // If the status is 'Recalled', set it to 'Serving'
            if ($status === 'Recalled') {
                $status = 'Serving';
            }

            // Prepare the SQL query based on the status
            if ($status === 'Serving') {
                $sql = "
                    UPDATE queue 
                    SET 
                        status = ?,
                        serving_user_id = ?,
                        payment_status = CASE WHEN ? = 'Done' THEN 'Pending' ELSE payment_status END,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE queue_number = ? 
                    AND reset_flag = 0 
                    AND (status != 'Done' AND (status != 'Serving' OR serving_user_id IS NULL))
                ";
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute([$status, $userId, $status, $queueNumber]);
            } else {
                $sql = "
                    UPDATE queue 
                    SET 
                        status = ?,
                        payment_status = CASE WHEN ? = 'Done' THEN ? ELSE payment_status END,
                        updated_at = CURRENT_TIMESTAMP
                    WHERE queue_number = ? 
                    AND reset_flag = 0
                ";
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute([$status, $status, $paymentStatus, $queueNumber]);
            }

            if (!$result || $stmt->rowCount() === 0) {
                return ['success' => false, 'message' => 'Failed to update status'];
            }

            return [
                'success' => true,
                'message' => 'Status updated successfully',
                'showPaymentModal' => ($status === 'Done')
            ];

        } catch (PDOException $e) {
            error_log("Error updating queue status: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    public function updatePaymentStatus($queueNumber, $paymentStatus)
    {
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

    public function searchQueue($searchTerm, $page = 1, $limit = 20)
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

    /**
     * Complete a payment and track the user who processed it
     * @param string $queueNumber Queue number to complete
     * @param int|null $userId ID of user completing the payment
     * @return bool Success status
     */
    public function completePayment($queueNumber, $userId, $receiptNumber)
    {
        try {
            $this->db->beginTransaction();

            // Log the input parameters for debugging
            error_log("completePayment in Queue model called with queueNumber: $queueNumber, userId: $userId, receiptNumber: $receiptNumber");

            // Check if the queue item exists and is in the correct state
            $stmt = $this->db->prepare("
                SELECT * FROM queue 
                WHERE queue_number = :queueNumber 
                AND payment_status = 'Pending' 
                AND status = 'Done' 
                AND reset_flag = 0
            ");
            $stmt->bindParam(':queueNumber', $queueNumber, PDO::PARAM_STR);
            $stmt->execute();
            $queueItem = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$queueItem) {
                error_log("Queue item not found or not in the correct state for queueNumber: $queueNumber");
                $this->db->rollBack();
                return false;
            }

            // Proceed with updating the payment status
            $stmt = $this->db->prepare("
                UPDATE queue 
                SET 
                    payment_status = 'Completed',
                    completed_by_user_id = :userId,
                    receipt_number = :receiptNumber,
                    payment_completed_at = CURRENT_TIMESTAMP,
                    updated_at = CURRENT_TIMESTAMP,
                    status = 'Done'
                WHERE queue_number = :queueNumber
                AND payment_status = 'Pending'
                AND status = 'Done'
                AND reset_flag = 0
            ");

            $stmt->bindParam(':queueNumber', $queueNumber, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':receiptNumber', $receiptNumber, PDO::PARAM_STR);

            $success = $stmt->execute();

            if ($success && $stmt->rowCount() > 0) {
                $this->db->commit();
                return true;
            }

            error_log("Failed to update queue item for queueNumber: $queueNumber");
            $this->db->rollBack();
            return false;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error completing payment: " . $e->getMessage());
            return false;
        }
    }

    public function getQueueData()
    {
        $stmt = $this->db->query("SELECT * FROM queue WHERE status = 'Payment' AND payment_status = 'Pending' ORDER BY updated_at ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get count of payments by status
     * @param string $status Payment status to count
     * @return int Count of payments with given status
     */
    public function getPaymentCountByStatus($status)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM queue 
                WHERE payment_status = ?
                AND reset_flag = 0
            ");
            $stmt->execute([$status]);
            return (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (PDOException $e) {
            error_log("Error getting payment count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get count of payments completed today
     * @return int Count of payments completed today
     */
    public function getPaymentsCompletedToday()
    {
        try {
            $stmt = $this->db->query("
                SELECT COUNT(*) as count 
                FROM queue 
                WHERE payment_status = 'Completed'
                AND DATE(updated_at) = CURDATE()
                AND reset_flag = 0
            ");
            return (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (PDOException $e) {
            error_log("Error getting completed payments: " . $e->getMessage());
            return 0;
        }
    }


    public function getCancelledPayments()
    {
        try {
            $stmt = $this->db->query("
                SELECT COUNT(*) as count 
                FROM queue 
                WHERE payment_status = 'Cancelled'
                AND DATE(updated_at) = CURDATE()
                AND reset_flag = 0
            ");
            return (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (PDOException $e) {
            error_log("Error getting cancelled payments: " . $e->getMessage());
            return 0;
        }
    }

    public function getCompletedPaymentsCount()
    {
        try {
            $stmt = $this->db->query("
                SELECT COUNT(*) as count
                FROM queue 
                WHERE payment_status = 'Completed'
                AND reset_flag = 0
            ");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Error fetching completed payments count: " . $e->getMessage());
            return 0;
        }
    }

    public function getCancelledPaymentsCount()
    {
        try {
            $stmt = $this->db->query("
                SELECT COUNT(*) as count
                FROM queue 
                WHERE payment_status = 'Cancelled'
                AND reset_flag = 0
            ");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            error_log("Error fetching cancelled payments count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get paginated payment queue
     * @param int $page Page number
     * @param int $limit Items per page
     * @return array Array of payment queue items
     */
    public function getPaymentQueue($page = 1, $limit = 20)
    {
        try {
            $offset = ($page - 1) * $limit;

            $stmt = $this->db->prepare("
                SELECT 
                    q.*,
                    u.first_name,
                    u.last_name
                FROM queue q
                LEFT JOIN users u ON q.serving_user_id = u.id
                WHERE q.payment_status = 'Pending' 
                AND q.status = 'Done'
                AND q.reset_flag = 0
                ORDER BY q.updated_at ASC
                LIMIT :limit OFFSET :offset
            ");

            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting payment queue: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search payment queue
     * @param string $searchTerm Search term
     * @param int $page Page number
     * @param int $limit Items per page
     * @return array Array of matching payment queue items
     */
    public function searchPaymentQueue($searchTerm, $page = 1, $limit = 20)
    {
        try {
            $offset = ($page - 1) * $limit;

            $stmt = $this->db->prepare("
                SELECT 
                    q.*,
                    u.first_name,
                    u.last_name
                FROM queue q
                LEFT JOIN users u ON q.serving_user_id = u.id
                WHERE (q.customer_name LIKE :searchTerm 
                    OR q.queue_number LIKE :searchTerm)
                AND q.payment_status = 'Pending'
                AND q.status = 'Done'
                AND q.reset_flag = 0
                ORDER BY q.updated_at ASC
                LIMIT :limit OFFSET :offset
            ");

            $searchTerm = "%$searchTerm%";
            $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error searching payment queue: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get total count of pending payments
     * @return int Total count of pending payments
     */
    public function getTotalPendingPayments()
    {
        try {
            $stmt = $this->db->query("
                SELECT COUNT(*) as count 
                FROM queue 
                WHERE payment_status = 'Pending'
                AND status = 'Done'
                AND reset_flag = 0
            ");
            return (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (PDOException $e) {
            error_log("Error getting total pending payments: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get count of payment search results
     * @param string $searchTerm Search term
     * @return int Count of matching payment items
     */
    public function getPaymentSearchCount($searchTerm)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM queue 
                WHERE (customer_name LIKE :searchTerm 
                    OR queue_number LIKE :searchTerm)
                AND payment_status = 'Pending'
                AND status = 'Done'
                AND reset_flag = 0
            ");

            $searchTerm = "%$searchTerm%";
            $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
            $stmt->execute();

            return (int) $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        } catch (PDOException $e) {
            error_log("Error getting payment search count: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get payment history for the current day
     * @return array Array of payment history items
     */
    public function getPaymentHistory()
    {
        try {
            $stmt = $this->db->query("
                SELECT 
                    q.*,
                    u.first_name,
                    u.last_name
                FROM queue q
                LEFT JOIN users u ON q.completed_by_user_id = u.id
                WHERE q.payment_status = 'Completed'
                AND DATE(q.updated_at) = CURDATE()
                AND q.reset_flag = 0
                ORDER BY q.updated_at DESC
                LIMIT 50
            ");

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting payment history: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Cancel a payment and track the user who cancelled it
     * @param string $queueNumber Queue number to cancel
     * @param int|null $userId ID of user cancelling the payment
     * @return bool Success status
     */
    public function cancelPayment($queueNumber, $userId)
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                UPDATE queue 
                SET 
                    payment_status = 'Cancelled',
                    completed_by_user_id = :userId,
                    payment_completed_at = CURRENT_TIMESTAMP,
                    updated_at = CURRENT_TIMESTAMP
                WHERE queue_number = :queueNumber
                AND payment_status = 'Pending'
                AND status = 'Done'
                AND reset_flag = 0
            ");

            $stmt->bindParam(':queueNumber', $queueNumber, PDO::PARAM_STR);
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

            $success = $stmt->execute();

            if ($success && $stmt->rowCount() > 0) {
                $this->db->commit();
                return true;
            }

            $this->db->rollBack();
            return false;

        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error cancelling payment: " . $e->getMessage());
            return false;
        }
    }

    public function getDailySummary()
    {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as totalQueues,
                SUM(CASE WHEN status = 'Done' THEN 1 ELSE 0 END) as completedQueues,
                SUM(CASE WHEN status = 'Skipped' THEN 1 ELSE 0 END) as skippedQueues
            FROM queue
            WHERE DATE(created_at) = CURDATE()
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getMonthlySummary()
    {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as totalQueues,
                SUM(CASE WHEN status = 'Done' THEN 1 ELSE 0 END) as completedQueues,
                SUM(CASE WHEN status = 'Skipped' THEN 1 ELSE 0 END) as skippedQueues
            FROM queue
            WHERE MONTH(created_at) = MONTH(CURDATE())
            AND YEAR(created_at) = YEAR(CURDATE())
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getServiceTypeBreakdown()
    {
        $stmt = $this->db->query("
            SELECT 
                SUM(CASE WHEN service_type = 'Visa' THEN 1 ELSE 0 END) as visa,
                SUM(CASE WHEN service_type = 'Tours / Cruise' THEN 1 ELSE 0 END) as tourPackages,
                SUM(CASE WHEN service_type = 'Travel Insurance' THEN 1 ELSE 0 END) as travelInsurance,
                SUM(CASE WHEN service_type = 'Flights' THEN 1 ELSE 0 END) as flights
            FROM queue
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // public function getPriorityQueueReport()
    // {
    //     $stmt = $this->db->query("
    //         SELECT 
    //             SUM(CASE WHEN priority = 'Yes' THEN 1 ELSE 0 END) as priorityQueues,
    //             SUM(CASE WHEN priority = 'No' THEN 1 ELSE 0 END) as nonPriorityQueues
    //         FROM queue
    //         WHERE reset_flag = 1
    //     ");
    //     return $stmt->fetch(PDO::FETCH_ASSOC);
    // }

    public function getQueueDataByDateRange($startDate, $endDate)
    {
        $stmt = $this->db->prepare("
            SELECT 
                q.id, q.customer_name, q.service_type, q.region, q.priority, q.priority_type, q.queue_number, q.status, q.created_at, q.updated_at, q.payment_status, q.serving_user_id, q.completed_by_user_id, q.payment_completed_at,
                CONCAT(su.first_name, ' ', su.last_name) as serving_user_name, CONCAT(cu.first_name, ' ', cu.last_name) as completed_by_user_name,
                TIMESTAMPDIFF(MINUTE, q.created_at, q.updated_at) AS time_spent
            FROM queue q
            LEFT JOIN users su ON q.serving_user_id = su.id
            LEFT JOIN users cu ON q.completed_by_user_id = cu.id
            WHERE DATE(q.created_at) BETWEEN :startDate AND :endDate
        ");
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQueueItem($queueNumber)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT status, serving_user_id 
                FROM queue 
                WHERE queue_number = ?
                AND reset_flag = 0
                LIMIT 1
            ");
            $stmt->execute([$queueNumber]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting queue item: " . $e->getMessage());
            return false;
        }
    }

    public function getAverageQueueTimeSpent()
    {
        $sql = "SELECT AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) AS average_time_spent FROM queue WHERE status = 'Done'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return round($stmt->fetchColumn());
    }

    public function getAverageTimeSpentByService()
    {
        $sql = "SELECT service_type, AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) AS average_time_spent FROM queue WHERE status = 'Done' GROUP BY service_type";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $averageTimeSpentByService = [];
        foreach ($results as $row) {
            $averageTimeSpentByService[$row['service_type']] = round($row['average_time_spent']);
        }
        return $averageTimeSpentByService;
    }
}
