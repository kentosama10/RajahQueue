<?php

require_once '../core/Controller.php';

class PaymentController extends Controller {
    public function __construct() {
        $this->requireAuth(); // Ensure user is authenticated
    }

    public function index() {
        $this->view('payment/payment');
    }

    /**
     * Retrieves the current payment queue with pagination and search
     * @return void
     */
    public function getPaymentQueue() {
        ob_clean();
        
        try {
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
            
            $queueModel = $this->model('Queue');
            
            // Get payment statistics
            $stats = [
                'pending' => $queueModel->getPaymentCountByStatus('Pending'),
                'serving' => $queueModel->getPaymentCountByStatus('Serving'),
                'completed' => $queueModel->getPaymentsCompletedToday(),
                'cancelled' => $queueModel->getCancelledPayments()
            ];
            
            // Get paginated payment queue
            if ($searchTerm) {
                $payments = $queueModel->searchPaymentQueue($searchTerm, $page);
                $totalCount = $queueModel->getPaymentSearchCount($searchTerm);
            } else {
                $payments = $queueModel->getPaymentQueue($page);
                $totalCount = $queueModel->getTotalPendingPayments() + $queueModel->getPaymentCountByStatus('Serving');
            }
            
            $response = [
                "success" => true,
                "stats" => $stats,
                "payments" => $payments,
                "totalCount" => $totalCount,
                "currentPage" => $page
            ];
            
        } catch (Exception $e) {
            error_log("Error fetching payment queue: " . $e->getMessage());
            $response = [
                "success" => false,
                "error" => "Failed to fetch payment queue",
                "message" => "An error occurred while fetching the payment queue"
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    /**
     * Handles payment completion with validation
     * @return void
     */
    public function completePayment() {
        ob_clean();
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $response = [
            "success" => false,
            "message" => "",
            "error" => null
        ];
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Invalid request method");
            }

            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                throw new Exception("User not authenticated");
            }

            if (!isset($_POST['queue_number']) || !isset($_POST['receipt_number'])) {
                throw new Exception("Missing required parameters");
            }

            $queueNumber = trim($_POST['queue_number']);
            $receiptNumber = trim($_POST['receipt_number']);
            $userId = (int)$_SESSION['user_id'];

            $queueModel = $this->model('Queue');

            // Log the input parameters for debugging
            error_log("completePayment called with queueNumber: $queueNumber, receiptNumber: $receiptNumber, userId: $userId");

            // Process the payment with user tracking and receipt number
            $success = $queueModel->completePayment($queueNumber, $userId, $receiptNumber);
            
            if (!$success) {
                throw new Exception("Failed to process payment. $queueNumber may be already complete or cancelled.");
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

    /**
     * Retrieves payment history for the current day
     * @return void
     */
    public function getPaymentHistory() {
        ob_clean();
        
        try {
            $queueModel = $this->model('Queue');
            $history = $queueModel->getPaymentHistory();
            
            $response = [
                "success" => true,
                "history" => $history
            ];
            
        } catch (Exception $e) {
            error_log("Error fetching payment history: " . $e->getMessage());
            $response = [
                "success" => false,
                "error" => "Failed to fetch payment history"
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    /**
     * Handles payment cancellation
     * @return void
     */
    public function cancelPayment() {
        ob_clean();
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $response = [
            "success" => false,
            "message" => "",
            "error" => null
        ];
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Invalid request method");
            }

            if (!isset($_SESSION['user_id'])) {
                throw new Exception("User not authenticated");
            }

            if (!isset($_POST['queue_number'])) {
                throw new Exception("Missing queue number parameter");
            }

            $queueNumber = trim($_POST['queue_number']);
            $userId = (int)$_SESSION['user_id'];
            

            $queueModel = $this->model('Queue');
            $success = $queueModel->cancelPayment($queueNumber, $userId);
            
            if (!$success) {
                throw new Exception("Failed to cancel payment");
            }

            $response["success"] = true;
            $response["message"] = "Payment cancelled successfully";

        } catch (Exception $e) {
            error_log("Payment cancellation error: " . $e->getMessage());
            $response["error"] = $e->getMessage();
            $response["message"] = "Payment cancellation failed: " . $e->getMessage();
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function updatePaymentStatus() {
        ob_clean();
        
        $response = ['success' => false, 'message' => ''];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['queue_number']) || !isset($_POST['payment_status']) || !isset($_POST['counter_number'])) {
                $response['message'] = 'Missing required parameters';
            } else {
                $queueNumber = $_POST['queue_number'];
                $paymentStatus = $_POST['payment_status'];
                $counterNumber = $_POST['counter_number'];
                $userId = $_SESSION['user_id'] ?? null;

                if (!$userId) {
                    $response['message'] = 'User not authenticated';
                } else {
                    $queueModel = $this->model('Queue');
                    
                    // Update both payment status and cashier_server
                    $success = $queueModel->updatePaymentStatusAndServer(
                        $queueNumber, 
                        $paymentStatus, 
                        $counterNumber,
                        $userId
                    );
                    
                    $response['success'] = $success;
                    $response['message'] = $success ? 'Payment status updated successfully' : 'Failed to update payment status';
                }
            }
        } else {
            $response['message'] = 'Invalid request method';
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}