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
            padding: 20px;
            background-color: #f8f9fa;
            overflow-y: auto;
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
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .now-serving h2 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .queue-number {
            font-size: 4rem;
            font-weight: bold;
            text-align: center;
        }

        .counter-info {
            font-size: 1.2rem;
            text-align: center;
            margin-top: 10px;
        }

        .upcoming-numbers {
            background: #fff;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .upcoming-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #eee;
            animation: fadeIn 0.5s ease-in-out;
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
            object-fit: cover;
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

        .payment-section {
            margin-top: 20px;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .payment-header {
            background: var(--primary-color);
            color: #fff;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="split-container">
        <!-- Left Side - Queue Information -->
        <div class="queue-section">
            <!-- Regular Queue Section -->
            <div class="now-serving">
                <h2><i class="bi bi-people-fill me-2"></i>Now Serving</h2>
                <div id="currentServing" class="queue-number">---</div>
                <div id="counterInfo" class="counter-info">Counter: ---</div>
            </div>

            <div class="upcoming-numbers">
                <h3>Upcoming Numbers</h3>
                <div id="upcomingList">
                    <!-- Upcoming numbers will be populated here -->
                </div>
            </div>

            <!-- Payment Queue Section -->
            <div class="payment-section">
                <div class="payment-header">
                    <h3><i class="bi bi-credit-card me-2"></i>Cashier</h3>
                </div>
                <div class="now-serving">
                    <div id="currentPayment" class="queue-number">---</div>
                    <div id="counterInfo" class="counter-info">Counter: ---</div>
                </div>
                <div id="upcomingPayments" class="upcoming-numbers">
                    <!-- Upcoming payment numbers will be populated here -->
                </div>
            </div>
        </div>

        <!-- Right Side - Media and Announcements -->
        <div class="media-section">
            <div class="clock-display" id="clockDisplay"></div>
            <div class="video-container">
                <video id="promoVideo" autoplay loop muted>
                    <source src="/RajahQueue/app/assets/videos/promo.mp4" type="video/mp4">
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

    <script>
        let previousServingData = [];
        let previousPaymentData = [];

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
                url: "/RajahQueue/public/DashboardController/getDashboardData",
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
            ).slice(0, 5);

            // Update current serving
            if (servingItems.length > 0) {
                const current = servingItems[0];
                $("#currentServing").text(current.queue_number);
                $("#counterInfo").text(`Counter: ${current.counter_number || "---"}`);
            }

            // Update upcoming list
            const upcomingHtml = waitingItems.map(item => `
                <div class="upcoming-item">
                    <span>Queue: ${item.queue_number}</span>
                    <span>Status: Waiting</span>
                </div>
            `).join("");
            
            $("#upcomingList").html(upcomingHtml || "<p>No upcoming numbers</p>");
        }

        function updatePaymentDisplay(paymentData) {
            // Sort payments to ensure 'serving' status is at the top, followed by 'pending'
            const sortedPayments = paymentData.sort((a, b) => {
                const statusOrder = ['serving', 'pending'];
                const aStatus = a.payment_status?.toLowerCase() || '';
                const bStatus = b.payment_status?.toLowerCase() || '';
                return statusOrder.indexOf(aStatus) - statusOrder.indexOf(bStatus);
            });

            // Filter serving payments (only those with assigned counters)
            const servingPayments = sortedPayments.filter(item => 
                item.payment_status && 
                item.payment_status.toLowerCase() === "serving"
            );

            // Filter pending payments
            const pendingPayments = sortedPayments.filter(item => 
                item.payment_status && 
                item.payment_status.toLowerCase() === "pending"
            ).slice(0, 3);

            // Update current payment display with animations
            if (servingPayments.length > 0) {
                servingPayments.forEach(current => {
                    // Only display if there's a counter number
                    if (current.counter_number) {
                        const paymentDisplayId = `payment-counter-${current.counter_number}`;
                        
                        // Create or update the payment display for this counter
                        let paymentDisplay = $(`#${paymentDisplayId}`);
                        if (paymentDisplay.length === 0) {
                            // Create new display if it doesn't exist
                            const newDisplay = `
                                <div id="${paymentDisplayId}" class="now-serving mb-3 animate__animated animate__fadeIn">
                                    <div class="counter-info">Counter ${current.counter_number}</div>
                                    <div class="queue-number">${current.queue_number}</div>
                                    ${current.customer_name ? `
                                        <div class="customer-info text-center mt-2">
                                            ${current.customer_name}
                                        </div>
                                    ` : ''}
                                </div>
                            `;
                            $("#currentPaymentDisplays").append(newDisplay);
                        } else {
                            // Update existing display with animation if number changed
                            const currentNumber = paymentDisplay.find('.queue-number').text();
                            if (currentNumber !== current.queue_number) {
                                paymentDisplay.find('.queue-number').fadeOut(300, function() {
                                    $(this).text(current.queue_number).fadeIn(300);
                                    // Play notification sound
                                    const audio = new Audio("/RajahQueue/app/assets/sounds/notification.mp3");
                                    audio.play();
                                });
                            }
                        }
                    }
                });
            } else {
                // No serving payments
                $("#currentPaymentDisplays").html(`
                    <div class="now-serving">
                        <div class="counter-info">No Active Counters</div>
                        <div class="queue-number">---</div>
                    </div>
                `);
            }

            // Update upcoming payments list with animations
            const upcomingContainer = $("#upcomingPayments");
            if (pendingPayments.length > 0) {
                const upcomingHtml = pendingPayments.map((item, index) => `
                    <div class="upcoming-item animate__animated animate__fadeInUp" 
                         style="animation-delay: ${index * 0.2}s">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="queue-number">Queue: ${item.queue_number}</strong>
                                ${item.customer_name ? `
                                    <div class="customer-name small text-muted">
                                        ${item.customer_name}
                                    </div>
                                ` : ''}
                            </div>
                            <span class="badge bg-warning text-dark">Pending</span>
                        </div>
                    </div>
                `).join("");
                
                upcomingContainer.html(upcomingHtml);
            } else {
                upcomingContainer.html(`
                    <div class="text-center text-muted animate__animated animate__fadeIn">
                        <p>No pending payments</p>
                    </div>
                `);
            }
        }

        // Initialize
        $(document).ready(function() {
            updateClock();
            setInterval(updateClock, 1000);
            refreshDisplay();
            setInterval(refreshDisplay, 10000);
        });
    </script>
</body>
</html>