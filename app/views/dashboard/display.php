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
        }

        .display-header {
            background-color: #343a40;
            color: white;
            padding: 1rem 0;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .queue-item {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.2s ease;
        }

        .queue-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .counter-header {
            background-color: #0d6efd;
            color: white;
            padding: 10px 0;
            border-radius: 10px 10px 0 0;
            font-size: 1.5rem;
        }

        .queue-number {
            font-size: 3rem;
            font-weight: bold;
            color: #0d6efd;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        @media (max-width: 768px) {
            .queue-number {
                font-size: 2rem;
            }

            .counter-header {
                font-size: 1.2rem;
            }
        }
    </style>
</head>

<body>


    <div class="container">
        <!-- Header: Currently Serving -->
        <div class="display-header mt-5">
            <h2>Currently Serving</h2>
        </div>

        <div id="servingQueue" class="row">
            <!-- Queue items dynamically loaded here -->
        </div>

        <!-- Header: Continuing Clients -->
        <div class="display-header mt-5">
            <h2>Continuing Clients</h2>
        </div>
        <div id="paymentQueue" class="row">
            <!-- Payment queue items dynamically loaded here -->
        </div>
    </div>

    <script>
        // Refresh and update display
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

        // Update the display with serving and payment queue data
        function updateDisplay(data) {
            const servingQueue = $('#servingQueue');
            servingQueue.empty(); // Clear existing items

            if (!data.queue || data.queue.length === 0) {
                servingQueue.append(`
                    <div class="col-12 text-center text-muted">
                        <h3>No customers are currently being served.</h3>
                    </div>
                `);
            } else {
                const groupedByCounter = {};
                data.queue.forEach(item => {
                    if (item.status.toLowerCase() === 'serving') {
                        groupedByCounter[item.counter_number] = item.queue_number;
                    }
                });

                // Display queue items by counter
                Object.keys(groupedByCounter).sort((a, b) => parseInt(a) - parseInt(b)).forEach(counterNumber => {
                    servingQueue.append(`
                        <div class="col-md-4 mb-4">
                            <div class="queue-item text-center">
                                <div class="counter-header">
                                    Counter ${counterNumber}
                                </div>
                                <div class="queue-number mt-3">${groupedByCounter[counterNumber]}</div>
                            </div>
                        </div>
                    `);
                });
            }

            const paymentQueue = $('#paymentQueue');
            paymentQueue.empty(); // Clear existing items

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
                                <div class="counter-header">
                                    Queue
                                </div>
                                <div class="queue-number mt-3">${item.queue_number}</div>
                            </div>
                        </div>
                    `);
                });
            }
        }

        // Initial load and refresh every 15 seconds
        $(document).ready(function () {
            refreshDisplay();
            setInterval(refreshDisplay, 15000);
        });
    </script>
</body>

</html>