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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
            font-size: 2.3rem;
            font-weight: bold;
            color: var(--secondary-color);
            padding: 0.5rem;
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

        .animate__animated {
            animation-duration: 0.5s;
            animation-fill-mode: both;
        }

        .animate__fadeInUp {
            animation-name: fadeInUp;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate__fadeInRight {
            animation-name: fadeInRight;
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
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

        .no-data-translate {
            opacity: 0;
            transform: translateY(20px);
            /* Start from below */
            animation: translateIn 0.5s forwards;
        }

        @keyframes translateIn {
            from {
                opacity: 0;
                transform: translateY(20px);
                /* Start from below */
            }

            to {
                opacity: 1;
                transform: translateY(0);
                /* End at original position */
            }
        }

        footer {
            background-color: var(--primary-color);
            color: white;
            padding: 0;
            text-align: center;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
            margin: 0;
            overflow: hidden;
            /* Prevent overflow issues */
        }

        .marquee {
            white-space: nowrap;
            box-sizing: border-box;
            display: inline-block;
            animation: marquee 40s linear infinite;
            margin: 0;
            padding: 0;
            position: relative;
        }

        .marquee p {
            padding: 0;
            margin: 0;
            color: black;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .marquee:hover {
            animation-play-state: paused;
            /* Pause animation on hover */
        }

        .social-icons a {
            margin-left: 0;
        }

        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        /* Gradient fade effect on both sides */
        footer::before,
        footer::after {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            width: 50px;
            background: linear-gradient(to right, var(--primary-color), transparent);
            z-index: 1001;
        }

        footer::before {
            left: 0;
        }

        footer::after {
            right: 0;
            transform: rotateY(180deg);
        }
    </style>
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
                    <h2><i class="bi bi-credit-card me-2"></i>Cashier</h2>
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
        let isRefreshing = false; // Prevent overlapping refreshes

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
            if (isRefreshing) return; // Prevent overlapping refresh calls
            isRefreshing = true;

            $.ajax({
                url: '/RajahQueue/public/DashboardController/getDashboardData',
                method: 'GET',
                dataType: 'json',
                success: function (data) {

                    // Ensure valid data structure
                    const queueData = data.queue || [];
                    const paymentQueueData = data.paymentQueue || [];

                    updateUI({ queue: queueData, paymentQueue: paymentQueueData });

                    // Reset countdown
                    clearInterval(countdown);
                    let countdownValue = 15;
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
                    setTimeout(refreshDisplay, 5000);
                },
                complete: function () {
                    isRefreshing = false; // Allow next refresh
                }
            });
        }

        function updateUI(data) {
            const ITEMS_PER_PAGE_SERVING = 12;
            const ITEMS_PER_PAGE_PAYMENT = 6;

            // Process Serving Queue
            const servingQueue = $("#servingQueue");
            const servingData = processServingQueue(data.queue || [], ITEMS_PER_PAGE_SERVING);
            renderQueue(servingQueue, servingData.items, previousServingData, "fadeInUp");
            previousServingData = servingData.allItems;
            currentServingPage = servingData.nextPage;

            // Process Payment Queue
            const paymentQueue = $("#paymentQueue");
            const paymentData = processPaymentQueue(data.paymentQueue || [], ITEMS_PER_PAGE_PAYMENT);
            renderPaymentQueue(paymentQueue, paymentData.items, previousPaymentData);
            previousPaymentData = paymentData.allItems;
            currentPaymentPage = paymentData.nextPage;

            // Check if both queues are empty and force refresh if so
            if (servingData.items.length === 0 && paymentData.items.length === 0) {
                setTimeout(refreshDisplay, 5000); // Wait 5 seconds before refreshing
            }
        }

        function processServingQueue(queue, itemsPerPage) {
            const servingByCounter = {};
            queue.forEach(item => {
                if (item.status && item.status.toLowerCase() === 'serving' && item.counter_number && item.service_type !== 'Booth') {
                    if (!servingByCounter[item.counter_number]) {
                        servingByCounter[item.counter_number] = [];
                    }
                    servingByCounter[item.counter_number].push(item);
                }
            });

            const sortedCounters = Object.keys(servingByCounter).sort((a, b) => parseInt(a) - parseInt(b));
            const allItems = sortedCounters.flatMap(counter => servingByCounter[counter]);
            const totalPages = Math.ceil(allItems.length / itemsPerPage);

            if (allItems.length === 0) return { allItems, items: [], nextPage: 0 };

            const start = currentServingPage * itemsPerPage;
            const items = allItems.slice(start, start + itemsPerPage);
            const nextPage = (currentServingPage + 1) % totalPages;

            return { allItems, items, nextPage };
        }

        function processPaymentQueue(queue, itemsPerPage) {
            const totalPages = Math.ceil(queue.length / itemsPerPage);
            if (queue.length === 0) return { allItems: [], items: [], nextPage: 0 };

            const start = currentPaymentPage * itemsPerPage;
            const items = queue.slice(start, start + itemsPerPage);
            const nextPage = (currentPaymentPage + 1) % totalPages;

            return { allItems: queue, items, nextPage };
        }

        function renderQueue(queueElement, items, previousData, animationClass) {
            const fragment = document.createDocumentFragment();
            const existingItems = previousData.map(prev => prev.queue_number);

            // Sound effect for new items
            const newItemSound = new Audio("/RajahQueue/app/assets/sounds/notification.mp3");

            if (items.length === 0) {
                // Show fallback animation when no data is available
                queueElement.empty();
                queueElement.append(`
            <div class="col-12 text-center mt-5 no-data-fade-in">
                <h3 class="mt-3 text-muted">No customers are currently being served.</h3>
            </div>
        `);
            } else {
                // Render items with transitions for new data
                items.forEach((item, index) => {
                    const isNew = !existingItems.includes(item.queue_number);

                    if (isNew) {
                        // Play sound effect for new items
                        newItemSound.play();
                    }

                    const div = document.createElement('div');
                    div.className = `col-md-4 mb-4 ${isNew ? `animate__animated ${animationClass}` : ''}`;
                    div.style.animationDelay = isNew ? `${index * 0.1}s` : '';
                    div.innerHTML = `
                <div class="queue-item text-center">
                    <div class="counter-header">
                        <div class="status-indicator bg-success"></div>
                        <h4 class="mb-0">Counter ${item.counter_number || 'N/A'}</h4>
                    </div>
                    <div class="queue-number">${item.queue_number}</div>
                </div>
            `;

                    fragment.appendChild(div);
                });

                queueElement.empty();
                queueElement.append(fragment);
            }
        }



        function renderPaymentQueue(paymentQueue, items, previousData) {
            const fragment = document.createDocumentFragment();
            const existingItems = previousData.map(prev => prev.queue_number);

            // Sound effect for new items
            const newItemSound = new Audio("/RajahQueue/app/assets/sounds/notification.mp3");

            if (items.length === 0) {
                // Show fallback animation when no data is available
                paymentQueue.empty();
                paymentQueue.append(`
            <div class="col-12 text-center mt-5 no-data-fade-in">
                <h3 class="mt-3 text-muted">No pending for payments.</h3>
            </div>
        `);
            } else {
                // Render items with transitions for new data
                items.forEach((item, index) => {
                    const isNew = !existingItems.includes(item.queue_number);

                    if (isNew) {
                        // Play sound effect for new items
                        newItemSound.play();
                    }

                    const div = document.createElement('div');
                    div.className = `col-12 mb-4 ${isNew ? 'animate__animated animate__fadeInRight' : ''}`;
                    div.style.animationDelay = isNew ? `${index * 0.1}s` : '';
                    div.innerHTML = `
                <div class="queue-item text-center">
                    <div class="queue-number">
                        <i class="bi bi-credit-card me-2"></i>${item.queue_number}
                    </div>
                </div>
            `;

                    fragment.appendChild(div);
                });

                paymentQueue.empty();
                paymentQueue.append(fragment);
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
            refreshDisplay();
            setInterval(refreshDisplay, 15000); // Ensure no overlap
        });
    </script>

    <!-- Footer Marquee -->
    <footer>
        <div class="marquee">
            <p>Contact Us: Telephone: +63 (02) 8894-0886 &nbsp; | &nbsp; E-mail: webinquiry@rajahtravel.com &nbsp; |
                &nbsp;
                Address: 3rd Floor 331 Building Sen. Gil Puyat Ave. Makati &nbsp; | &nbsp; Room 202 GLC Building
                A. Mabini cor. T.M. Kalaw Sts. Manila &nbsp; | &nbsp; Follow us on:
                <span class="social-icons">
                    <span class="text-black ms-3"><i class="bi bi-facebook"></i> rajahtravel.com</span>
                    <span class="text-black ms-3"><i class="bi bi-twitter-x"></i> rajahtravel.com</span>
                    <span class="text-black ms-3"><i class="bi bi-instagram"></i> rajahtravel_com</span>
                </span>
            </p>

        </div>
    </footer>

</body>

</html>