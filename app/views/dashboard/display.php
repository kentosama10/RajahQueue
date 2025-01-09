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
            color: #fff;
            padding: 1rem 0;
            text-align: center;
        }
        .queue-item {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
            margin-bottom: 1rem;
            padding: 1rem;
        }
        .queue-number {
            font-size: 2rem;
            font-weight: bold;
        }
        .customer-name {
            font-size: 1.5rem;
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
                        No customers are currently being served.
                    </div>
                `);
            } else {
                data.queue.forEach(item => {
                    if (item.status.toLowerCase() === 'serving') {
                        servingQueue.append(`
                            <div class="col-md-4">
                                <div class="queue-item text-center">
                                    <div class="queue-number">${item.queue_number}</div>
                                    <div class="customer-name">${item.customer_name}</div>
                                    <div class="service-type">${item.service_type}</div>
                                </div>
                            </div>
                        `);
                    }
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