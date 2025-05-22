<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Queue Display V2 - RajahQueue</title>
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
            font-family: "Roboto", sans-serif;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #f8f9fa;
        }

        /* Split Screen Layout */
        .split-container {
            display: flex;
            height: 100vh;
        }

        .queue-section {
            flex: 1;
            padding: 10px;
            background-color: #f8f9fa;
            overflow-y: hidden;
        }

        .media-section {
            flex: 1;
            background-color: #000;
            position: relative;
        }

        /* Queue Display Styling */
        .now-serving {
            background: var(--secondary-color);
            color: #fff;
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }

        .now-serving h2 {
            font-size: 1.4rem;
            margin-bottom: 10px;
            text-align: center;
            padding-bottom: 5px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .queue-number {
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
        }

        .counter-info {
            font-size: 1.2rem;
            text-align: center;
        }

        .upcoming-numbers {
            background: #fff;
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .upcoming-numbers p {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
            font-weight: 500;
        }

        /* Add grid layout for upcoming items */
        .upcoming-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
        }

        .upcoming-item {
            padding: 8px;
            border-radius: 6px;
            background: #f8f9fa;
            transition: transform 0.3s ease, background 0.3s ease;
            text-align: center;
        }

        .upcoming-item:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }

        /* Video Section */
        .video-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: calc(100% - 50px); /* Account for news ticker */
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* News Ticker */
        .news-ticker {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            background: var(--primary-color);
            color: #fff;
            overflow: hidden;
        }

        .ticker-content {
            white-space: nowrap;
            animation: ticker 30s linear infinite;
            padding: 15px 0;
        }

        @keyframes ticker {
            0% { transform: translateX(100%); }
            100% { transform: translateX(-100%); }
        }

        /* Clock Display */
        .clock-display {
            position: absolute;
            top: 20px;
            right: 20px;
            color: #fff;
            font-size: 1.5rem;
            z-index: 100;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .payment-header {
            background: var(--primary-color);
            color: #fff;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        #currentPaymentDisplays {
            display: flex;
            flex-direction: column;
        }

        #currentPaymentDisplays .now-serving {
            background: var(--primary-color);
        }

        #currentPaymentDisplays .now-serving:last-child {
            margin-bottom: 0;
        }

        .payment-section .upcoming-item {
            padding: 5px;
            border-radius: 0;
            transition: all 0.3s ease;
        }

        .payment-section .upcoming-item:hover {
            background-color: #f8f9fa;
        }

        /* Add these to your existing styles */
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

        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
        }

        .no-data-fade-in {
            opacity: 0;
            transform: translateY(20px);
            animation: translateIn 0.5s forwards;
        }

        @keyframes translateIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Add these styles */
        .serving-item {
            margin-bottom: 15px;
            padding: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .serving-item:last-child {
            margin-bottom: 0;
            border-bottom: none;
        }

        /* Add these new styles */
        .serving-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .service-section {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 8px;
        }

        .service-section h3 {
            font-size: 1.5rem;
            margin-bottom: 5px;
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
        }

        .serving-item {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 6px;
            margin-bottom: 5px;
            padding: 0;
            transition: transform 0.2s ease;
        }

        .serving-item:hover {
            transform: translateY(-2px);
        }

        .serving-item .queue-number {
            font-size: 2rem;
            font-weight: bold;
        }

        .serving-item .counter-info {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* Zoom in and out animation for new items */
        @keyframes zoomInOut {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.2); /* Zoom in */
            }
            100% {
                transform: scale(1); /* Zoom out */
            }
        }

        .new-item {
            animation: zoomInOut 1s ease-in-out infinite; /* Animation loops every 1 second */
        }
    </style>
</head>
<body>
    <div class="split-container">
        <!-- Left Side - Queue Information -->
        <div class="queue-section">
            <!-- Combined Now Serving Section -->
            <div class="now-serving">
                <h2><i class="bi bi-display me-2"></i>Now Serving</h2>
                <div class="serving-container">
                    <!-- Regular Queue Section -->
                    <div class="service-section">
                        <h3><i class="bi bi-people-fill me-1"></i>Queue</h3>
                        <div id="currentServing">
                            <!-- Queue numbers will be populated here -->
                        </div>
                    </div>
                    
                    <!-- Payment Section -->
                    <div class="service-section">
                        <h3><i class="bi bi-cash me-1"></i>Cashier</h3>
                        <div id="currentPaymentDisplays">
                            <!-- Payment numbers will be populated here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Sections -->
            <div class="upcoming-numbers">
                <p><i class="bi bi-clock me-1"></i>Upcoming Queues</p>
                <div id="upcomingList" class="upcoming-list">
                    <!-- Upcoming numbers will be populated here -->
                </div>
            </div>

            <div class="upcoming-numbers">
                <p><i class="bi bi-cash me-1"></i>Upcoming Payments</p>
                <div id="upcomingPayments" class="upcoming-list">
                    <!-- Upcoming payments will be populated here -->
                </div>
            </div>
        </div>

        <!-- Right Side - Media and Announcements -->
        <div class="media-section">
            <div class="clock-display" id="clockDisplay" hidden></div>
            <div class="video-container">
                <video id="promoVideo" autoplay loop muted>
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="news-ticker">
                <div class="ticker-content" id="tickerContent">
                    Welcome to Rajah Travel Corporation - Your trusted partner in travel since 1972
                </div>
            </div>
        </div>
    </div>

    <!-- Add this audio element for the notification sound -->
    <audio id="notificationSound" src="/RajahQueue/app/assets/sounds/notification.mp3" preload="auto"></audio>

    <script>
        let previousServingData = [];
        let previousPaymentData = [];
        const itemsPerPage = 5; // Number of items to show per page

        // Text-to-speech announcement
        function announceServing(queueNumber, counterNumber) {
            if (!queueNumber || !counterNumber) return;
            const message = `${queueNumber} please proceed to counter ${counterNumber}`;
            if ('speechSynthesis' in window) {
                setTimeout(() => {
                    const utter = new SpeechSynthesisUtterance(message);
                    utter.rate = 1;
                    utter.pitch = 1;
                    utter.volume = 1;
                    window.speechSynthesis.speak(utter);
                }, 3000); // 2 seconds delay before speech
            }
        }

        // Listen for manual announcement trigger from dashboard
        window.addEventListener('storage', function(event) {
            if (event.key === 'manualAnnouncement' && event.newValue) {
                try {
                    const data = JSON.parse(event.newValue);
                    if (data.queueNumber && data.counterNumber) {
                        announceServing(data.queueNumber, data.counterNumber);
                    }
                } catch (e) {
                    // Ignore parse errors
                }
            }
        });

        let lastManualAnnouncementId = null;

        function pollManualAnnouncement() {
            $.ajax({
                url: '/RajahQueue/public/dashboard/getManualAnnouncement',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data && data.id && data.id !== lastManualAnnouncementId) {
                        lastManualAnnouncementId = data.id;
                        announceServing(data.queue_number, data.counter_number);
                    }
                }
            });
        }

        function updateClock() {
            const clockDisplay = document.getElementById("clockDisplay");
            const now = new Date();
            clockDisplay.textContent = now.toLocaleTimeString("en-US", {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit"
            });
        }

        function refreshDisplay() {
            $.ajax({
                url: "/RajahQueue/public/dashboard/getDashboardData",
                method: "GET",
                dataType: "json",
                success: function(data) {
                    updateQueueDisplay(data.queue || []);
                    updatePaymentDisplay(data.paymentQueue || []);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching queue data:", error);
                    setTimeout(refreshDisplay, 5000);
                }
            });
        }

        function updateQueueDisplay(queueData) {
            const servingItems = queueData.filter(item => 
                item.status && item.status.toLowerCase() === "serving"
            );

            const waitingItems = queueData.filter(item => 
                item.status && item.status.toLowerCase() === "waiting"
            );

            // Check for new serving items
            const newServingItems = servingItems.filter(item => 
                !previousServingData.some(prevItem => prevItem.queue_number === item.queue_number)
            );

            if (newServingItems.length > 0) {
                // Play notification sound if there are new items
                const notificationSound = document.getElementById("notificationSound");
                notificationSound.play();
                // Announce each new serving item
                newServingItems.forEach(item => {
                    announceServing(item.queue_number, item.counter_number || "unknown");
                });
            }

            // Store serving data for rendering
            previousServingData = servingItems;

            // Update current serving
            const servingHtml = servingItems.map(item => {
                const isNew = newServingItems.some(newItem => newItem.queue_number === item.queue_number);
                return `
                    <div class="serving-item animate__animated animate__fadeIn ${isNew ? 'new-item' : ''}">
                        <div class="queue-number">${item.queue_number}</div>
                        <div class="counter-info"><i class="bi bi-display me-1"></i>Counter ${item.counter_number || "---"}</div>
                    </div>
                `;
            }).join("");
            
            $("#currentServing").html(servingHtml);

            // Update upcoming list independently
            const upcomingHtml = waitingItems.map(item => `
                <div class="upcoming-item animate__animated animate__fadeIn">
                    <span>Queue: ${item.queue_number}</span>
                    <span>Status: Waiting</span>
                </div>
            `).join("");
            
            $("#upcomingList").html(upcomingHtml);
        }

        function updatePaymentDisplay(paymentData) {
            const servingPayments = paymentData.filter(item => 
                item.payment_status?.toLowerCase() === "serving" &&
                item.counter_number
            );

            const pendingPayments = paymentData.filter(item => 
                item.payment_status?.toLowerCase() === "pending"
            );

            // Check for new serving payment items
            const newPaymentItems = servingPayments.filter(item => 
                !previousPaymentData.some(prevItem => prevItem.queue_number === item.queue_number)
            );

            if (newPaymentItems.length > 0) {
                // Announce each new serving payment item
                newPaymentItems.forEach(item => {
                    announceServing(item.queue_number, item.counter_number || "unknown");
                });
            }

            // Update current payment display
            const displayHtml = servingPayments.map(payment => {
                const isNew = newPaymentItems.some(newItem => newItem.queue_number === payment.queue_number);
                return `
                    <div class="serving-item animate__animated animate__fadeIn ${isNew ? 'new-item' : ''}">
                        <div class="queue-number">${payment.queue_number}</div>
                        <div class="counter-info"><i class="bi bi-display me-1"></i>Counter ${payment.counter_number}</div>
                    </div>
                `;
            }).join("");

            $("#currentPaymentDisplays").html(displayHtml);

            // Update upcoming payments list independently
            const upcomingContainer = $("#upcomingPayments");
            const upcomingHtml = pendingPayments.map((item, index) => `
                <div class="upcoming-item animate__animated animate__fadeInUp" 
                     style="animation-delay: ${index * 0.2}s">
                    <span>Queue: ${item.queue_number}</span>
                    <span>Status: Pending</span>
                </div>
            `).join("");

            upcomingContainer.html(upcomingHtml || `
                <div class="text-center text-muted animate__animated animate__fadeIn">
                </div>
            `);

            // Update the previous payment data to the current serving payments
            previousPaymentData = servingPayments;
        }

        // Initialize
        $(document).ready(function() {
            updateClock();
            setInterval(updateClock, 1000);
            refreshDisplay();
            setInterval(refreshDisplay, 15000); // Fetch new data every 15 seconds
            setInterval(pollManualAnnouncement, 3000); // Poll every 3 seconds
        });
    </script>
</body>
</html>