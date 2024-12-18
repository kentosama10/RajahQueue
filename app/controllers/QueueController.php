<?php
// app/controllers/QueueController.php

require_once '../core/Controller.php';

class QueueController extends Controller
{
    public function index()
    {
        $queueModel = $this->model('Queue');
        $data['queue'] = $queueModel->getAll();
        $this->view('queue/index', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $queueModel = $this->model('Queue');

            // Sanitize inputs
            $customerName = trim($_POST['customer_name']);
            $serviceType = $_POST['service_type'];
            $region = isset($_POST['region']) ? $_POST['region'] : null;
            $priority = $_POST['priority'];
            $priorityType = ($priority === 'Yes') ? $_POST['priority_type'] : null;

            // Add to queue and get formatted queue number
            $queueNumber = $queueModel->add($customerName, $serviceType, $region, $priority, $priorityType);

            // Store success message in session
            session_start();
            $_SESSION['success_message'] = [
                'queue_number' => $queueNumber
            ];

            header('Location: /RajahQueue/public/');
            exit;
        }
    }

    public function dashboard()
    {
        $this->view('queue/dashboard');
    }

    public function getDashboardData()
    {
        ob_clean();
        
        try {
            $queueModel = $this->model('Queue');

            // Get the current page from the request, default to 1
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $searchTerm = isset($_GET['search']) ? $_GET['search'] : ''; // Get search term

            if ($searchTerm) {
                $data = [
                    'stats' => [
                        'waiting' => 0,
                        'serving' => 0,
                        'completed' => 0
                    ],
                    'queue' => $queueModel->searchQueue($searchTerm, $page),
                    'totalCount' => $queueModel->getSearchCount($searchTerm), // Get total count for pagination
                ];
            } else {
                $data = [
                    'stats' => [
                        'waiting' => (int)$queueModel->getCountByStatus('Waiting'),
                        'serving' => (int)$queueModel->getCountByStatus('Serving'),
                        'completed' => (int)$queueModel->getCompletedToday()
                    ],
                    'queue' => $queueModel->getActiveQueue($page),
                    'recallHistory' => $queueModel->getRecallHistory(),
                    'totalCount' => $queueModel->getTotalQueueCount(), // Get total count for pagination
                ];
            }

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

    public function updateStatus()
    {
        // Clear any previous output
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
                
                $response['success'] = $success;
                $response['message'] = $success ? 'Status updated successfully' : 'Failed to update status';
            }
        } else {
            $response['message'] = 'Invalid request method';
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }

}
