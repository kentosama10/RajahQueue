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
                $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
                
                $queueModel = $this->model('Queue');
                $queueItem = $queueModel->getQueueItem($queueNumber);
                
                if ($queueItem) {
                    $servingUserId = (int)$queueItem['serving_user_id'];

                    // Check if the user is already serving another customer
                    if ($newStatus === 'Serving' && $queueModel->isUserServingAnotherCustomer($userId)) {
                        $response['message'] = 'You are already serving another customer.';
                    } else {
                        // Check the current status of the queue number before updating
                        if ($queueItem['status'] === 'Serving' && $servingUserId !== null) {
                            if ($newStatus === 'Done' && $servingUserId !== $userId) {
                                $response['message'] = 'Only the user currently serving this customer can complete the status.';
                            } else if ($servingUserId !== $userId) {
                                $response['message'] = 'This customer is already being served by another user.';
                            } else {
                                // Handle "Done + Payment" status
                                if ($newStatus === 'Done + Payment') {
                                    $newStatus = 'Done';
                                    $paymentStatus = 'Pending';
                                } else {
                                    $paymentStatus = 'Not Required';
                                }

                                $success = $queueModel->updateStatus($queueNumber, $newStatus, $userId, $paymentStatus);
                                
                                if ($success) {
                                    $response['success'] = true;
                                    $response['message'] = 'Status updated successfully';
                                } else {
                                    $response['message'] = 'Failed to update status';
                                }
                            }
                        } else {
                            $success = $queueModel->updateStatus($queueNumber, $newStatus, $userId);
                            
                            if ($success) {
                                $response['success'] = true;
                                $response['message'] = 'Status updated successfully';
                            } else {
                                $response['message'] = 'Failed to update status';
                            }
                        }
                    }
                } else {
                    $response['message'] = 'Queue number not found';
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

    public function reports() {
        $queueModel = $this->model('Queue');
        $data = [
            'totalQueueCount' => $queueModel->getTotalQueueCount(),
            'completedToday' => $queueModel->getCompletedToday(),
            'waitingCount' => $queueModel->getCountByStatus('Waiting'),
            'servingCount' => $queueModel->getCountByStatus('Serving'),
            'skippedCount' => $queueModel->getCountByStatus('Skipped'),
            'noShowCount' => $queueModel->getCountByStatus('No Show'),
            'averageQueueTimeSpent' => $queueModel->getAverageQueueTimeSpent(),
            'averageTimeSpentByService' => $queueModel->getAverageTimeSpentByService(),
            'dailySummary' => $queueModel->getDailySummary(),
            'monthlySummary' => $queueModel->getMonthlySummary(),
            'serviceTypeBreakdown' => $queueModel->getServiceTypeBreakdown(),
            'completedPaymentsCount' =>$queueModel->getCompletedPaymentsCount(),
            'cancelledPaymentsCount' =>$queueModel->getCancelledPaymentsCount(),
        ];
        $this->view('report/reports', $data);
    }

    public function filterQueueData() {
        if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
            $startDate = $_GET['start_date'];
            $endDate = $_GET['end_date'];
    
            $queueModel = $this->model('Queue');
            $filteredData = $queueModel->getQueueDataByDateRange($startDate, $endDate);
    
            header('Content-Type: application/json');
            echo json_encode($filteredData);
            exit;
        }
    }
    
    public function exportQueueData() {
        if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
            $startDate = $_GET['start_date'];
            $endDate = $_GET['end_date'];
    
            $queueModel = $this->model('Queue');
            $filteredData = $queueModel->getQueueDataByDateRange($startDate, $endDate);
    
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="queue_data.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
    
            $output = fopen('php://output', 'w');
            fputcsv($output, ['ID', 'Customer Name', 'Service Type', 'Queue Number', 'Status', 'Created At', 'Updated At', 'Payment Status', 'Serving User Name', 'Completed By User Name', 'Payment Completed At']);
    
            foreach ($filteredData as $row) {
                fputcsv($output, [
                    $row['id'], $row['customer_name'], $row['service_type'], $row['region'], $row['queue_number'], $row['status'], $row['created_at'], $row['updated_at'], $row['payment_status'], $row['serving_user_name'], $row['completed_by_user_name'], $row['payment_completed_at']
                ]);
            }
    
            fclose($output);
            exit;
        }
    }
    
    public function checkQueueStatus() {
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception("Invalid request method");
            }

            if (!isset($_POST['queue_number'])) {
                throw new Exception("Missing queue number parameter");
            }

            $queueNumber = trim($_POST['queue_number']);
            $queueModel = $this->model('Queue');
            $queueItem = $queueModel->getQueueItem($queueNumber);

            if (!$queueItem) {
                throw new Exception("Queue number not found");
            }

            $response = [
                'success' => true,
                'current_status' => $queueItem['status'],
                'serving_user_id' => $queueItem['serving_user_id']
            ];
        } catch (Exception $e) {
            error_log("Error checking queue status: " . $e->getMessage());
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }

        echo json_encode($response);
        exit;
    }

    public function getActiveCounters() {
        $userModel = $this->model('User');
        $activeCounters = $userModel->getActiveCounters();

        header('Content-Type: application/json');
        echo json_encode(['activeCounters' => $activeCounters]);
        exit;
    }

    public function v2() {
        $this->view('dashboard/displayv2');
    }
}