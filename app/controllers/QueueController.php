<?php
// app/controllers/QueueController.php

require_once '../core/Controller.php';

class QueueController extends Controller {
    public function __construct() {
        $this->requireAuth(); // Ensure user is authenticated for all actions in this controller
    }

    public function index() {
        $queueModel = $this->model('Queue');
        $data['queue'] = $queueModel->getAll();
        $this->view('queue/queue', $data);
    }

    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $queueModel = $this->model('Queue');

            // Sanitize inputs
            $customerName = trim($_POST['customer_name']);
            $serviceType = $_POST['service_type'];

            // Check if the service type is "Payment Only"
            if ($serviceType === 'Payment') {
                // Set the payment status to 'Pending' and status to 'Done'
                $queueNumber = $queueModel->addQueueItem($customerName, $serviceType, 'Pending');
            } else {
                // Existing logic for adding to the queue
                $queueNumber = $queueModel->addQueueItem($customerName, $serviceType);
            }

            // Store success message in session
            session_start();
            $_SESSION['success_message'] = [
                'queue_number' => $queueNumber
            ];

            header('Location: /RajahQueue/public/queue/index');
            exit;
        }
    }
}