<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Display - RajahQueue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .display-header {
            background-color: #343a40;
            color: #fff;
            padding: 1rem 0;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .queue-item {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, .1);
            padding: 0;
            transition: transform 0.2s ease;
            overflow: hidden;
        }

        .queue-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, .15);
        }

        .counter-header {
            background-color: #0d6efd;
            color: white;
            border-radius: 8px 8px 0 0;
        }

        .queue-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #0d6efd;
        }

        .container {
            padding: 0 15px;
        }
    </style>
</head>

<body>
    <div class="display-header">
        <h2>Currently Serving</h2>
    </div>
    <div class="container mt-5">

        <div id="servingQueue" class="row">
            <!-- Serving queue items will be loaded here -->
        </div>
        <div class="display-header mt-5">
            <h2>Continuing Clients</h2>
        </div>
        <div id="paymentQueue" class="row">
            <!-- Payment queue items will be loaded here -->
        </div>
    </div>

    <script>
        function refreshDisplay() {
            $.ajax({
                url: '/RajahQueue/public/DashboardController/getDashboardData',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    updateDisplay(data);
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching display data:', error);
                }
            });
        }

        function updateDisplay(data) {
            const servingQueue = $('#servingQueue');
            servingQueue.empty(); // Clear existing items

            if (data.queue.length === 0) {
                servingQueue.append(`
                    <div class="col-12 text-center text-muted">
                        <h3>No customers are currently being served.</h3>
                    </div>
                `);
            } else {
                // Group serving customers by counter
                const servingByCounter = {};
                
                data.queue.forEach(item => {
                    if (item.status.toLowerCase() === 'serving') {
                        if (item.counter_number) {
                            if (!servingByCounter[item.counter_number]) {
                                servingByCounter[item.counter_number] = [];
                            }
                            servingByCounter[item.counter_number].push(item);
                        }
                    }
                });

                // Sort counters numerically
                const sortedCounters = Object.keys(servingByCounter).sort((a, b) => parseInt(a) - parseInt(b));

                sortedCounters.forEach(counterNumber => {
                    const items = servingByCounter[counterNumber];
                    items.forEach(item => {
                        servingQueue.append(`
                            <div class="col-md-4 mb-4">
                                <div class="queue-item text-center">
                                    <div class="counter-header bg-primary text-white py-2 mb-3">
                                        <h4 class="mb-0">Counter ${item.counter_number}</h4>
                                    </div>
                                    <div class="queue-number mb-2">${item.queue_number}</div>
                                </div>
                            </div>
                        `);
                    });
                });
            }

            // Update the payment queue section
            const paymentQueue = $('#paymentQueue');
            paymentQueue.empty();

            if (!data.paymentQueue || data.paymentQueue.length === 0) {
                paymentQueue.append(`
                    <div class="col-12 text-center text-muted">
                        <h3>No pending payments.</h3>
                    </div>
                `);
            } else {
                data.paymentQueue.forEach(item => {
                    paymentQueue.append(`
                        <div class="col-md-4 mb-4">
                            <div class="queue-item text-center">
                                <div class="queue-number mb-2">${item.queue_number}</div>
                                <div class="customer-name">${item.customer_name}</div>
                            </div>
                        </div>
                    `);
                });
            }
        }

        // Initial load
        $(document).ready(function () {
            refreshDisplay();
            setInterval(refreshDisplay, 15000); // Refresh every 15 seconds
        });
    </script>
</body>

</html>