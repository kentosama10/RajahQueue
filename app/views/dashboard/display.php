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
            padding: 10px;
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
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.1);
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
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .clock-display {
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 0;
        }

        .dashboard-layout {
            display: flex;
            gap: 20px;
        }

        .dashboard-column {
            flex: 1;
            min-width: 0;
            /* Prevents flex items from overflowing */
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
                    <h2><i class="bi bi-credit-card me-2"></i>For Payment</h2>
                </div>
                <div id="paymentQueue" class="row">
                    <!-- Payment queue items will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentServingPage = 0;
        let currentPaymentPage = 0;
        let previousServingData = [];
        let previousPaymentData = [];
        let countdown;

        function updateClock() {
            const clockDisplay = document.getElementById("clockDisplay");
            const now = new Date();
            clockDisplay.textContent = now.toLocaleTimeString("en-US", {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit"
            });
        }

        function updateCountdownDisplay(countdownValue) {
            const countdownDisplay = document.getElementById("countdownDisplay");
            if (countdownDisplay) {
                countdownDisplay.textContent = `Next update in ${countdownValue} seconds`;
            }
        }

        function refreshDisplay() {
            $.ajax({
                url: '/RajahQueue/public/DashboardController/getDashboardData',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    // Update UI with data
                    updateUI(data);

                    // Reset countdown
                    clearInterval(countdown);
                    let countdownValue = 15; // Reset countdown value
                    updateCountdownDisplay(countdownValue);
                    countdown = setInterval(() => {
                        countdownValue--;
                        updateCountdownDisplay(countdownValue);
                        if (countdownValue <= 0) {
                            clearInterval(countdown);
                        }
                    }, 1000);
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching display data:', error);
                    showError('Failed to fetch queue data. Retrying...');
                }
            });
        }

        function updateUI(data) {
            const ITEMS_PER_PAGE_SERVING = 12;
            const ITEMS_PER_PAGE_PAYMENT = 6;

            // Process Serving Queue
            const servingQueue = $('#servingQueue');
            const servingData = processServingQueue(data.queue, ITEMS_PER_PAGE_SERVING);
            renderQueue(servingQueue, servingData.items, previousServingData, 'fadeInUp', currentServingPage);
            previousServingData = servingData.allItems;
            currentServingPage = servingData.nextPage;

            // Process Payment Queue
            const paymentQueue = $('#paymentQueue');
            const paymentData = processPaymentQueue(data.paymentQueue, ITEMS_PER_PAGE_PAYMENT);
            renderPaymentQueue(paymentQueue, paymentData.items, previousPaymentData, currentPaymentPage);
            previousPaymentData = paymentData.allItems;
            currentPaymentPage = paymentData.nextPage;
        }

        function processServingQueue(queue, itemsPerPage) {
            const servingByCounter = {};
            queue.forEach(item => {
                if (item.status.toLowerCase() === 'serving' && item.counter_number) {
                    if (!servingByCounter[item.counter_number]) {
                        servingByCounter[item.counter_number] = [];
                    }
                    servingByCounter[item.counter_number].push(item);
                }
            });

            const sortedCounters = Object.keys(servingByCounter).sort((a, b) => parseInt(a) - parseInt(b));
            const allItems = sortedCounters.flatMap(counter => servingByCounter[counter]);
            const totalPages = Math.ceil(allItems.length / itemsPerPage);

            const start = currentServingPage * itemsPerPage;
            const items = allItems.slice(start, start + itemsPerPage);
            const nextPage = (currentServingPage + 1) % totalPages;

            return { allItems, items, nextPage };
        }

        function processPaymentQueue(queue, itemsPerPage) {
            const totalPages = Math.ceil(queue.length / itemsPerPage);
            const start = currentPaymentPage * itemsPerPage;
            const items = queue.slice(start, start + itemsPerPage);
            const nextPage = (currentPaymentPage + 1) % totalPages;

            return { allItems: queue, items, nextPage };
        }

        function renderQueue(queueElement, items, previousData, animationClass, currentPage) {
            if (JSON.stringify(items) !== JSON.stringify(previousData)) {
                queueElement.fadeOut(300, function () {
                    queueElement.empty();
                    items.forEach((item, index) => {
                        const isNew = !previousData.some(prev => prev.queue_number === item.queue_number);
                        queueElement.append(`
                        <div class="col-md-4 mb-4 ${isNew ? `animate__animated ${animationClass}` : ''}" 
                             style="${isNew ? `animation-delay: ${index * 0.1}s` : ''}">
                            <div class="queue-item text-center">
                                <div class="counter-header">
                                    <div class="status-indicator bg-success"></div>
                                    <h4 class="mb-0">Counter ${item.counter_number || 'N/A'}</h4>
                                </div>
                                <div class="queue-number">${item.queue_number}</div>
                            </div>
                        </div>
                    `);
                    });
                    queueElement.fadeIn(300);
                });
            }
        }

        function renderPaymentQueue(paymentQueue, items, previousData, currentPage) {
            if (JSON.stringify(items) !== JSON.stringify(previousData)) {
                paymentQueue.fadeOut(300, function () {
                    paymentQueue.empty();
                    items.forEach((item, index) => {
                        const isNew = !previousData.some(prev => prev.queue_number === item.queue_number);
                        paymentQueue.append(`
                        <div class="col-12 mb-4 ${isNew ? 'animate__animated animate__fadeInRight' : ''}" 
                             style="${isNew ? `animation-delay: ${index * 0.1}s` : ''}">
                            <div class="queue-item text-center">
                                <div class="queue-number">
                                    <i class="bi bi-credit-card me-2"></i>${item.queue_number}
                                </div>
                            </div>
                        </div>
                    `);
                    });
                    paymentQueue.fadeIn(300);
                });
            }
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

        // Initialize
        $(document).ready(function () {
            updateClock();
            setInterval(updateClock, 1000);
            setInterval(refreshDisplay, 15000);
            refreshDisplay();
        });
    </script>


</body>

</html>