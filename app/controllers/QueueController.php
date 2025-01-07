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
        $this->view('queue/index', $data);
    }

    public function add() {
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

            header('Location: /RajahQueue/public/QueueController/index');
            exit;
        }
    }
}