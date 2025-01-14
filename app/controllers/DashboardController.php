<?php

require_once '../core/Controller.php';

class DashboardController extends Controller {
    public function __construct() {
        $this->requireAuth(); // Ensure user is authenticated for all actions in this controller
    }

    public function index() {
        $queueModel = $this->model('Queue');
        $queueData = $queueModel->getQueueData();
        $this->view('dashboard/dashboard', ['queueData' => $queueData]);
    }

    public function getDashboardData() {
        ob_clean();
    
        try {
            $dashboardModel = $this->model('Dashboard');
            $queueModel = $this->model('Queue');
    
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
    
            $data = [
                'stats' => [
                    'waiting' => (int)$dashboardModel->getCountByStatus('Waiting'),
                    'serving' => (int)$dashboardModel->getCountByStatus('Serving'),
                    'completed' => (int)$dashboardModel->getCompletedToday()
                ],
                'recallHistory' => $dashboardModel->getRecallHistory(),
            ];
    
            if ($searchTerm) {
                $data['queue'] = $dashboardModel->searchQueue($searchTerm, $page);
                $data['totalCount'] = $dashboardModel->getSearchCount($searchTerm);
            } else {
                $data['queue'] = $dashboardModel->getActiveQueue($page);
                $data['totalCount'] = $dashboardModel->getTotalQueueCount();
            }
    
            $data['paymentQueue'] = $queueModel->getPaymentQueue();
    
            // Fetch the counter's active user
            $counterNumber = $_GET['counter_number'] ?? 1; // Default to counter 1 if not provided
            $userModel = $this->model('User');
            $data['counters'] = $userModel->getCounter($counterNumber);
    
            header('Content-Type: application/json');
            echo json_encode($data, JSON_PRETTY_PRINT);
            exit;
        } catch (Exception $e) {
            error_log("Error in getDashboardData: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['error' => 'An error occurred while fetching data.']);
            exit;
        }
    }
    

    public function updateStatus() {
        ob_clean();
        
        $response = ['success' => false, 'message' => ''];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['queue_number']) || !isset($_POST['status'])) {
                $response['message'] = 'Missing required parameters';
            } else {
                $queueNumber = $_POST['queue_number'];
                $newStatus = $_POST['status'];
                
                // Get the current user's ID from session
                $userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
                
                $queueModel = $this->model('Queue');
                $success = $queueModel->updateStatus($queueNumber, $newStatus, $userId);
                
                if ($success) {
                    if ($newStatus === 'Done') {
                        $response['promptPayment'] = true;
                        $response['message'] = 'Status updated successfully. Do you wish to proceed to payment?';
                    } else {
                        $response['success'] = true;
                        $response['message'] = 'Status updated successfully';
                    }
                } else {
                    $response['success'] = false;
                    if ($newStatus === 'Serving') {
                        $response['message'] = 'Cannot serve this queue number. It may be completed, reset, or no longer valid.';
                    } else {
                        $response['message'] = 'Failed to update status';
                    }
                }
            }
        } else {
            $response['message'] = 'Invalid request method';
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function updatePaymentStatus() {
        ob_clean();
        
        $response = ['success' => false, 'message' => ''];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['queue_number']) || !isset($_POST['payment_status'])) {
                $response['message'] = 'Missing required parameters';
            } else {
                $queueNumber = $_POST['queue_number'];
                $paymentStatus = $_POST['payment_status'];
                
                $queueModel = $this->model('Queue');
                $success = $queueModel->updatePaymentStatus($queueNumber, $paymentStatus);
                
                $response['success'] = $success;
                $response['message'] = $success ? 'Payment status updated successfully' : 'Failed to update payment status';
            }
        } else {
            $response['message'] = 'Invalid request method';
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

    public function display() {
        $this->view('dashboard/display');
    }

}