# RajahQueue

RajahQueue is a queue management system designed to streamline customer service operations by organizing and tracking customer queues efficiently.

## Features

- **Queue Management**: Add, update, and track customer queues.
- **Dashboard**: Real-time display of queue statuses (Waiting, Serving, Completed, etc.).
- **Reports**: Generate daily and monthly summaries, service type breakdowns, and custom date-range reports.
- **User Roles**: Support for different user roles (e.g., Admin, Staff).
- **Responsive Design**: Optimized for desktop and mobile devices.

## Setup Instructions

1. Install [XAMPP](https://www.apachefriends.org/index.html) on your system.
2. Clone this repository into the `htdocs` folder of your XAMPP installation.
3. Start the Apache and MySQL services from the XAMPP Control Panel.
4. Import the database:
   - Open `phpMyAdmin` (http://localhost/phpmyadmin).
   - Create a new database named `rajah_queue`.
   - Import the `app/database/rajah_queue.sql` file into the newly created database.
5. Access the application in your browser at `http://localhost/RajahQueue/public`.

## Usage

### Accessing the Dashboard

1. Navigate to `http://localhost/RajahQueue/public`.
2. Use the navigation menu to access different sections:
   - **Queue Dashboard**: View and manage active queues.
   - **Reports**: Generate and view reports.
   - **Customer Serving**: Display real-time serving information.

### Managing Queues

- Add new queues via the form available in the Queue Dashboard.
- Update queue statuses (e.g., Serving, Completed) as needed.

### Generating Reports

- Use the Reports section to view daily, monthly, and custom date-range reports.
- Export data to CSV for further analysis.

## Contributing

We welcome contributions to improve RajahQueue. To contribute:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Commit your changes and push them to your fork.
4. Submit a pull request with a detailed description of your changes.

## License

This project is licensed under the MIT License. See the LICENSE file for details.

## Support

For support or inquiries, please contact the project maintainer or open an issue in the repository.
