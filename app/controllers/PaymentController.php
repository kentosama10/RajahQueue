<?php

require_once '../core/Controller.php';

class PaymentController extends Controller {
    /**
     * Retrieves the current payment queue with validation
     * @return void
     */
    public function getPaymentQueue() {
        try {
            $queueModel = $this->model('Queue');
            // Only get active (non-reset) payment queue items
            $data = $queueModel->getPaymentQueue();
            
            header('Content-Type: application/json');
            echo json_encode($data);
            exit;
        } catch (Exception $e) {
            error_log("Error fetching payment queue: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode([
                "error" => "Failed to fetch payment queue",
                "success" => false
            ]);
            exit;
        }
    }

    public function index() {
        $this->view('payment/payment');
    }

    /**
     * Handles payment completion with validation
     * @return void
     */
    public function completePayment() {
        ob_clean();
        
        $response = [
            "success" => false,
            "message" => "",
            "error" => null
        ];
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Invalid request method");
            }

            if (!isset($_POST['queue_number'])) {
                throw new Exception("Missing queue number parameter");
            }

            $queueNumber = trim($_POST['queue_number']);
            
            // Validate queue number format (e.g., "T-1", "V-2", etc.)
            if (!preg_match('/^[A-Z]-\d+$/', $queueNumber)) {
                throw new Exception("Invalid queue number format");
            }

            $queueModel = $this->model('Queue');
            
            // First verify the queue item exists and is valid for payment
            $queueItem = $queueModel->verifyQueueForPayment($queueNumber);
            
            if (!$queueItem) {
                throw new Exception("Queue number not found or not eligible for payment");
            }
            
            if ($queueItem["reset_flag"] === 1) {
                throw new Exception("This queue number has been reset");
            }
            
            if ($queueItem["payment_status"] === "Completed") {
                throw new Exception("Payment has already been completed for this queue number");
            }

            // Process the payment
            $success = $queueModel->completePayment($queueNumber);
            
            if (!$success) {
                throw new Exception("Failed to process payment");
            }

            $response["success"] = true;
            $response["message"] = "Payment completed successfully";

        } catch (Exception $e) {
            error_log("Payment completion error: " . $e->getMessage());
            $response["error"] = $e->getMessage();
            $response["message"] = "Payment processing failed: " . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}