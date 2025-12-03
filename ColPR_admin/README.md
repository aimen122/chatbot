# ColPR Admin Dashboard

Admin dashboard for managing proposals with real-time notifications.

## Features

- **Real-time Notifications**: Get notified about new proposals with sound alerts and browser notifications
- **Proposal Management**: View and manage accepted, rejected, and escalated proposals
- **Notification System**: Smart notification system that only alerts once per proposal
- **Dashboard Overview**: Comprehensive dashboard with statistics and quick access to proposals
- **Marketing Members**: Manage marketing team members
- **Pricing Engine**: Access to pricing engine functionality

## Installation

1. Clone the repository:
```bash
git clone https://github.com/aimen122/chatbot_dashboard.git
```

2. Configure database connection in `config.php`:
```php
$servername = "localhost";
$username = "your_username";
$password = "your_password";
$dbname = "glaxit_chatbot";
```

3. Ensure your web server (Apache/Nginx) is configured to serve PHP files

4. Import the database schema (if needed)

## Requirements

- PHP 7.4 or higher
- MySQL/MariaDB
- Web server (Apache/Nginx)
- Modern web browser with JavaScript enabled

## Features

### Notification System
- Real-time proposal notifications
- Sound alerts for new proposals
- Browser notifications (requires permission)
- Smart tracking to prevent duplicate notifications
- Notification persistence across page refreshes

### Proposal Management
- View accepted proposals
- View rejected and escalated proposals
- Mark proposals as read
- Export proposals
- Assign proposals to team members

## File Structure

- `index.php` - Main dashboard
- `sidebar.php` - Sidebar navigation with notification system
- `accepted_proposals.php` - Accepted proposals page
- `rejected_escalated.php` - Rejected and escalated proposals
- `get_notifications.php` - API endpoint for notifications
- `mark_proposal_read.php` - API endpoint to mark proposals as read
- `config.php` - Database configuration

## Recent Updates

- Fixed notification system to prevent duplicate notifications for the same proposal
- Added localStorage persistence for notified proposals
- Improved notification sound and popup handling

## License

Proprietary - All rights reserved

