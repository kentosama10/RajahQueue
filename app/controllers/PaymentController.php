<?php

require_once '../core/Controller.php';

class PaymentController extends Controller {
    public function getPaymentQueue() {
        $queueModel = $this->model('Queue');
        $data = $queueModel->getPaymentQueue();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function index() {
        $this->view('payment/payment');
    }

    public function completePayment() {
        ob_clean();
        
        $response = ['success' => false, 'message' => ''];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['queue_number'])) {
                $response['message'] = 'Missing required parameters';
            } else {
                $queueNumber = $_POST['queue_number'];
                
                $queueModel = $this->model('Queue');
                $success = $queueModel->completePayment($queueNumber);
                
                $response['success'] = $success;
                $response['message'] = $success ? 'Payment completed successfully' : 'Failed to complete payment';
            }
        } else {
            $response['message'] = 'Invalid request method';
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}