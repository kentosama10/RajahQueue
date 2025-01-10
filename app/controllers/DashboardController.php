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

            // Get the current page from the request, default to 1
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $searchTerm = isset($_GET['search']) ? $_GET['search'] : ''; // Get search term

            // Always fetch the overall stats and recent activity
            $data = [
                'stats' => [
                    'waiting' => (int)$dashboardModel->getCountByStatus('Waiting'),
                    'serving' => (int)$dashboardModel->getCountByStatus('Serving'),
                    'completed' => (int)$dashboardModel->getCompletedToday()
                ],
                'recallHistory' => $dashboardModel->getRecallHistory(), // Always fetch recent activity
            ];

            // If a search term is provided, fetch the filtered queue
            if ($searchTerm) {
                $data['queue'] = $dashboardModel->searchQueue($searchTerm, $page);
                $data['totalCount'] = $dashboardModel->getSearchCount($searchTerm); // Get total count for pagination
            } else {
                $data['queue'] = $dashboardModel->getActiveQueue($page);
                $data['totalCount'] = $dashboardModel->getTotalQueueCount(); // Get total count for pagination
            }

            // Fetch the payment queue for completed items
            $data['paymentQueue'] = $queueModel->getPaymentQueue(); // Fetch payment queue items

            header('Content-Type: application/json');
            echo json_encode($data, JSON_PRETTY_PRINT);
            exit;
        } catch (Exception $e) {
            // Log the error message
            error_log("Error in getDashboardData: " . $e->getMessage());
            // Return a JSON error response
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
                
                $queueModel = $this->model('Queue');
                $success = $queueModel->updateStatus($queueNumber, $newStatus);
                
                if ($success && $newStatus === 'Done') {
                    // Prompt the user if they wish to proceed to payment
                    $response['promptPayment'] = true;
                    $response['message'] = 'Status updated successfully. Do you wish to proceed to payment?';
                } else {
                    $response['success'] = $success;
                    $response['message'] = $success ? 'Status updated successfully' : 'Failed to update status';
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