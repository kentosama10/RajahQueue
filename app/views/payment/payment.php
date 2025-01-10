<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Queue - RajahQueue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }
        .queue-item {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            margin-bottom: 1rem;
            padding: 1rem;
            text-align: center;
        }
        .queue-number {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .customer-name, .service-type {
            font-size: 1.2rem;
        }
        .loading-spinner {
            display: none;
            text-align: center;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Payment Queue</h2>
        <div id="paymentQueue" class="row">
            <!-- Payment queue items will be loaded here -->
        </div>
        <div class="loading-spinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div id="errorMessage" class="alert alert-danger mt-3" style="display: none;"></div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function loadPaymentQueue() {
            $('.loading-spinner').show();
            $('#errorMessage').hide();
            $.ajax({
                url: '/RajahQueue/public/PaymentController/getPaymentQueue',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    updatePaymentQueue(data);
                    $('.loading-spinner').hide();
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching payment queue data:', error);
                    $('#errorMessage').text('Failed to load payment queue. Please try again later.').show();
                    $('.loading-spinner').hide();
                }
            });
        }

        function updatePaymentQueue(data) {
            const paymentQueue = $('#paymentQueue');
            paymentQueue.empty(); // Clear existing items

            if (data.length === 0) {
                paymentQueue.append(`
                    <div class="col-12 text-center text-muted">
                        No customers in the payment queue.
                    </div>
                `);
            } else {
                data.forEach(item => {
                    paymentQueue.append(`
                        <div class="col-md-4">
                            <div class="queue-item">
                                <div class="queue-number">${item.queue_number}</div>
                                <div class="customer-name">${item.customer_name}</div>
                                <div class="service-type">${item.service_type}</div>
                                <button class="btn btn-success mt-2" onclick="completePayment('${item.queue_number}')">Complete Payment</button>
                            </div>
                        </div>
                    `);
                });
            }
        }

        function completePayment(queueNumber) {
            $.ajax({
                url: '/RajahQueue/public/PaymentController/completePayment',
                method: 'POST',
                data: { queue_number: queueNumber },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        loadPaymentQueue();
                    } else {
                        alert('Failed to complete payment. Please try again.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error completing payment:', error);
                    alert('Error completing payment. Please try again.');
                }
            });
        }

        // Initial load
        $(document).ready(function () {
            loadPaymentQueue();
        });
    </script>
</body>
</html>