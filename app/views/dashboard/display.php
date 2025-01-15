<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Display - RajahQueue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/RajahQueue/app/assets/images/RTC LOGO 2017 - Vector-02-ORB.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --primary-color: #F08221;
            --secondary-color: #CE007C;
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
            min-height: 100vh;
            overflow: hidden;
        }

        .display-header {
            background: var(--secondary-color);
            color: #fff;
            padding: 0.5rem 0;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .queue-item {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, .1);
            padding: 0;
            transition: all var(--transition-speed) ease;
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.1);
        }

        .queue-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, .15);
        }

        .counter-header {
            background: var(--primary-color);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 0.5rem;
        }

        .queue-number {
            font-size: 3rem;
            font-weight: bold;
            color: var(--secondary-color);
            padding: 0.5rem 0;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.2); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

        .clock-display {
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 1rem;
        }

        .refresh-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 20px;
            border-radius: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .dashboard-layout {
            display: flex;
            gap: 20px;
        }

        .dashboard-column {
            flex: 1;
            min-width: 0; /* Prevents flex items from overflowing */
        }

        .queue-section {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        /* Enhance the payment queue items */
        #paymentQueue .queue-item {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            transition: all 0.3s ease;
        }

        #paymentQueue .queue-item:hover {
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="dashboard-layout">
            <!-- Currently Serving Column -->
            <div class="dashboard-column">
                <div class="display-header">
                    <div class="clock-display" id="clockDisplay"></div>
                    <h2><i class="bi bi-people-fill me-2"></i>Currently Serving</h2>
                </div>
                <div id="servingQueue" class="row">
                    <!-- Serving queue items will be loaded here -->
                </div>
            </div>

            <!-- Continuing Clients Column -->
            <div class="dashboard-column">
                <div class="display-header">
                    <h2><i class="bi bi-arrow-repeat me-2"></i>Continuing Clients</h2>
                </div>
                <div id="paymentQueue" class="row">
                    <!-- Payment queue items will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <div class="refresh-indicator" id="refreshIndicator">
        Next refresh in: <span id="countdown">15</span>s
    </div>

    <script>
        function updateClock() {
            const clockDisplay = document.getElementById("clockDisplay");
            const now = new Date();
            clockDisplay.textContent = now.toLocaleTimeString("en-US", {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit"
            });
        }

        function updateCountdown(seconds) {
            document.getElementById("countdown").textContent = seconds;
        }

        function refreshDisplay() {
            $.ajax({
                url: '/RajahQueue/public/DashboardController/getDashboardData',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    updateDisplay(data);
                    // Start countdown
                    let countdown = 15;
                    const countdownInterval = setInterval(() => {
                        countdown--;
                        updateCountdown(countdown);
                        if (countdown <= 0) clearInterval(countdownInterval);
                    }, 1000);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching display data:', error);
                    showError('Failed to fetch queue data. Retrying...');
                }
            });
        }

        function showError(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger alert-dismissible fade show';
            errorDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container').prepend(errorDiv);
            setTimeout(() => errorDiv.remove(), 5000);
        }

        function updateDisplay(data) {
            const servingQueue = $('#servingQueue');
            servingQueue.empty();

            if (data.queue.length === 0) {
                servingQueue.append(`
                    <div class="col-12 text-center text-muted animate__animated animate__fadeIn">
                        <h3><i class="bi bi-info-circle me-2"></i>No customers are currently being served.</h3>
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
                            <div class="col-md-4 mb-4 animate__animated animate__fadeInUp">
                                <div class="queue-item text-center">
                                    <div class="counter-header">
                                        <div class="status-indicator bg-success"></div>
                                        <h4 class="mb-0">Counter ${item.counter_number}</h4>
                                    </div>
                                    <div class="queue-number">${item.queue_number}</div>
                                </div>
                            </div>
                        `);
                    });
                });
            }

            // Update the payment queue section with animations
            const paymentQueue = $('#paymentQueue');
            paymentQueue.empty();

            if (!data.paymentQueue || data.paymentQueue.length === 0) {
                paymentQueue.append(`
                    <div class="col-12 text-center text-muted animate__animated animate__fadeIn">
                        <h3><i class="bi bi-info-circle me-2"></i>No pending payments.</h3>
                    </div>
                `);
            } else {
                data.paymentQueue.forEach((item, index) => {
                    paymentQueue.append(`
                        <div class="col-12 mb-4 animate__animated animate__fadeInRight" 
                             style="animation-delay: ${index * 0.1}s">
                            <div class="queue-item text-center">
                                <div class="queue-number">
                                    <i class="bi bi-arrow-repeat me-2"></i>${item.queue_number}
                                </div>
                            </div>
                        </div>
                    `);
                });
            }
        }

        // Initialize
        $(document).ready(function() {
            updateClock();
            setInterval(updateClock, 1000);
            refreshDisplay();
            setInterval(refreshDisplay, 15000);
        });
    </script>
</body>

</html>