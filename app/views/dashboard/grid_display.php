<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Now Serving - RajahQueue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/RajahQueue/app/assets/images/RTC LOGO 2017 - Vector-02-ORB.png" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        :root {
            --primary-color: #F08221;
            --secondary-color: #CE007C;
            --border-color: #dee2e6;
            --transition-speed: 0.3s;
        }

        body {
            font-family: "Roboto", sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
            min-height: 100vh;
        }

        .display-header {
            background: var(--secondary-color);
            color: #fff;
            padding: 0.75rem;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .clock-display {
            font-size: 1.75rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 0 auto;
            max-width: 1600px;
        }

        .counter-card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            transition: transform var(--transition-speed);
        }

        .counter-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .counter-header {
            background: var(--primary-color);
            color: white;
            padding: 0.5rem;
            text-align: center;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .counter-body {
            padding: 0.75rem;
            text-align: center;
        }

        .queue-numbers {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin: 0.5rem 0;
        }

        .queue-number {
            font-size: 1.75rem;
            font-weight: bold;
            color: var(--secondary-color);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            background: #f8f9fa;
            min-width: 80px;
        }

        .arrow {
            color: #6c757d;
            font-size: 1.2rem;
        }

        .new-entry {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .grid-container {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 0.75rem;
            }

            .queue-number {
                font-size: 1.5rem;
                min-width: 60px;
            }

            .clock-display {
                font-size: 1.5rem;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            body {
                background-color: #1a1a1a;
            }

            .counter-card {
                background: #2d2d2d;
                border: 1px solid #404040;
            }

            .queue-number {
                background: #333;
                color: #fff;
            }

            .arrow {
                color: #888;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="display-header">
            <div class="clock-display" id="clockDisplay"></div>
            <h2><i class="bi bi-display me-2"></i>Now Serving</h2>
        </div>

        <div class="grid-container" id="counterGrid">
            <!-- Counter cards will be loaded here -->
        </div>
    </div>

    <script>
        let previousData = {};
        let isRefreshing = false;

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
            if (isRefreshing) return;
            isRefreshing = true;

            $.ajax({
                url: "/RajahQueue/public/DashboardController/getDashboardData",
                method: "GET",
                dataType: "json",
                success: function(data) {
                    updateGridView(data.queue || []);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching data:", error);
                    setTimeout(refreshDisplay, 5000);
                },
                complete: function() {
                    isRefreshing = false;
                }
            });
        }

        function updateGridView(queueData) {
            const counterGrid = document.getElementById("counterGrid");
            const counterHistory = processQueueData(queueData);
            
            counterGrid.innerHTML = "";

            Object.keys(counterHistory)
                .sort((a, b) => parseInt(a) - parseInt(b))
                .forEach(counter => {
                    const lastThreeEntries = counterHistory[counter].slice(-3);
                    if (lastThreeEntries.length === 0) return;

                    const card = document.createElement("div");
                    card.className = "counter-card";
                    
                    card.innerHTML = `
                        <div class="counter-header">Counter ${counter}</div>
                        <div class="counter-body">
                            <div class="queue-numbers">
                                ${lastThreeEntries.map((entry, index) => `
                                    ${index > 0 ? '<span class="arrow">→</span>' : ''}
                                    <span class="queue-number ${!previousData[counter]?.includes(entry.queue_number) ? 'new-entry' : ''}">${entry.queue_number}</span>
                                `).join('')}
                            </div>
                        </div>
                    `;

                    counterGrid.appendChild(card);
                });

            // Update previous data for animation purposes
            previousData = Object.fromEntries(
                Object.entries(counterHistory).map(([counter, entries]) => 
                    [counter, entries.map(e => e.queue_number)]
                )
            );
        }

        function processQueueData(queueData) {
            const counterHistory = {};
            
            queueData.forEach(item => {
                if (item.counter_number && item.status === "Serving") {
                    if (!counterHistory[item.counter_number]) {
                        counterHistory[item.counter_number] = [];
                    }
                    counterHistory[item.counter_number].push({
                        queue_number: item.queue_number
                    });
                }
            });

            // Sort entries for each counter by queue number
            Object.keys(counterHistory).forEach(counter => {
                counterHistory[counter].sort((a, b) => a.queue_number.localeCompare(b.queue_number));
            });

            return counterHistory;
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