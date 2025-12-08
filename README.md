# 🎫 Helpdesk System

A comprehensive helpdesk ticketing system built with PHP and MySQL. This system provides a complete solution for managing customer support tickets with role-based access control, email notifications, and activity tracking.

![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-blue)
![MySQL Version](https://img.shields.io/badge/MySQL-5.7%2B-orange)
![License](https://img.shields.io/badge/License-MIT-green)

---

## 📋 Table of Contents

- [Features](#-features)
- [System Requirements](#-system-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [User Roles](#-user-roles)
- [Core Features](#-core-features)
- [Email Notifications](#-email-notifications)
- [API Documentation](#-api-documentation)
- [Troubleshooting](#-troubleshooting)
- [Contributing](#-contributing)
- [License](#-license)

---

## ✨ Features

### 🎯 Ticket Management
- ✅ Create, update, and delete support tickets
- ✅ Priority levels: Low, Medium, High, Urgent
- ✅ Status tracking: Open, In Progress, Closed
- ✅ Category organization with custom colors and icons
- ✅ Advanced filtering and search functionality
- ✅ Full-text search support
- ✅ Ticket assignment to agents
- ✅ File attachments support (images, PDFs, documents)
- ✅ Activity timeline for each ticket
- ✅ Export tickets to CSV/Excel

### 👥 User Management
- ✅ Role-based access control (Admin, Customer)
- ✅ User registration and authentication
- ✅ Profile management with avatar upload
- ✅ Password reset functionality
- ✅ User activity tracking

### 📧 Email Notifications
- ✅ Automated email notifications for:
  - New ticket created
  - Ticket updated
  - Ticket assigned
  - New comment added
  - Status changes
- ✅ Email queue system for reliable delivery
- ✅ Customizable notification preferences per user
- ✅ Email delivery tracking and retry mechanism
- ✅ SMTP support with SSL/TLS encryption

### 💬 Comments & Communication
- ✅ Public and internal comments
- ✅ Real-time comment updates
- ✅ Edit and delete comments
- ✅ @mention support (planned)
- ✅ Rich text formatting

### 📊 Analytics & Reporting
- ✅ Dashboard with statistics
- ✅ Ticket distribution by status
- ✅ Priority distribution charts
- ✅ Category analytics
- ✅ Agent performance metrics
- ✅ Overdue ticket tracking
- ✅ Activity logs export

### 🏷️ Category Management
- ✅ Create custom categories
- ✅ Color-coded categories
- ✅ Icon assignment
- ✅ Active/Inactive toggle
- ✅ Ticket count per category
- ✅ Category-based filtering

### 📝 Activity Logging
- ✅ Complete audit trail
- ✅ Track all ticket changes
- ✅ User action history
- ✅ IP address and user agent logging
- ✅ Field-level change tracking

### 🔒 Security Features
- ✅ CSRF protection
- ✅ SQL injection prevention (prepared statements)
- ✅ XSS protection
- ✅ Rate limiting
- ✅ Secure password hashing (bcrypt)
- ✅ Session management
- ✅ File upload validation

---

## 💻 System Requirements

- **PHP**: 8.3 or higher
- **MySQL**: 5.7 or higher
- **Extensions Required**:
  - PDO
  - PDO_MySQL
  - mbstring
  - fileinfo
  - gd (for image processing)
- **Web Server**: Apache/Nginx with mod_rewrite
- **Composer**: For dependency management

---

## 🚀 Installation

### 1. Clone Repository

```bash
git clone https://github.com/yourusername/helpdesk-system.git
cd helpdesk-system
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Edit configuration
nano .env
```

### 4. Configure Database

Update `.env` with your database credentials:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=helpdesk_db
DB_USER=root
DB_PASS=your_password
```

### 5. Initialize Database

```bash
# Run database initialization script
php scripts/init-db.php
```

This will create all necessary tables:
- users
- tickets
- comments
- attachments
- categories
- activity_logs
- email_notifications
- notification_preferences

### 6. Create Admin User

```bash
# Run via browser or command line
php scripts/create-admin.php
```

Or manually via MySQL:

```sql
INSERT INTO users (name, email, password, role) 
VALUES ('Admin', 'admin@example.com', '$2y$10$hashed_password', 'admin');
```

### 7. Set File Permissions

```bash
# Upload directory
chmod 755 public/uploads
chmod 755 storage/logs

# Environment file
chmod 600 .env
```

### 8. Configure Web Server

**Apache (.htaccess already included)**:
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [QSA,L]
```

**Nginx**:
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

### 9. Setup Email Notifications

Configure SMTP settings in `.env`:

```env
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
```

### 10. Setup Cron Job (Email Processing)

Add to crontab:

```bash
* * * * * php /path/to/helpdesk/scripts/process-emails.php >> /path/to/logs/email-cron.log 2>&1
```

---

## ⚙️ Configuration

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_NAME` | Application name | "Helpdesk System" |
| `APP_URL` | Base URL | http://localhost/helpdesk |
| `APP_ENV` | Environment (development/production) | development |
| `DB_HOST` | Database host | 127.0.0.1 |
| `DB_PORT` | Database port | 3306 |
| `DB_NAME` | Database name | helpdesk_db |
| `UPLOAD_MAX_SIZE` | Max upload size (bytes) | 5242880 (5MB) |
| `SESSION_LIFETIME` | Session duration (seconds) | 7200 (2 hours) |
| `RATE_LIMIT_REQUESTS` | Max requests per period | 60 |

### Email Configuration Options

**Option 1: Hosting Email (Recommended)**
```env
MAIL_HOST=mail.yourdomain.com
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
```

**Option 2: Gmail**
```env
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

**Option 3: Mailtrap (Testing)**
```env
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
```

---

## 👥 User Roles

### 1. Admin
**Permissions:**
- Full system access
- Manage all tickets
- Manage users
- Manage categories
- View all analytics
- Access activity logs
- Configure system settings


### 3. Customer
**Permissions:**
- Create tickets
- View own tickets only
- Add comments to own tickets
- Upload attachments
- Update own profile
- No admin access

---

## 🎯 Core Features

### Ticket Management

#### Creating Tickets
```php
// Customer creates ticket
POST /tickets
{
    "title": "Cannot login to account",
    "description": "I forgot my password",
    "priority": "high",
    "category_id": 1
}
```

#### Updating Tickets
```php
// Admin/Agent updates ticket
POST /tickets/{id}
{
    "status": "progress",
    "assigned_to": 2,
    "priority": "urgent"
}
```

#### Advanced Filtering
```php
GET /tickets?status=open&priority=high&category_id=1&assigned_to=me
```

### Category Management

#### Available Fields
- **Name**: Category display name
- **Slug**: URL-friendly identifier
- **Description**: Category description
- **Color**: Hex color code (#3B82F6)
- **Icon**: Font Awesome icon name
- **Status**: Active/Inactive

#### Category Operations
```php
// Create category
POST /categories/store
{
    "name": "Technical Support",
    "description": "Technical issues",
    "color": "#EF4444",
    "icon": "wrench"
}

// Update category
POST /categories/{id}/update

// Toggle status
POST /categories/{id}/toggle

// Delete category (if no tickets)
DELETE /categories/{id}
```

### Comment System

#### Comment Types
- **Public**: Visible to all (customer, agents, admins)
- **Internal**: Only visible to agents and admins

```php
// Add comment
POST /comments
{
    "ticket_id": 1,
    "comment": "Working on this issue",
    "is_internal": true
}
```

### File Attachments

#### Supported File Types
- Images: jpg, jpeg, png, gif
- Documents: pdf, doc, docx, txt
- Archives: zip

```php
// Upload attachment
POST /tickets (multipart/form-data)
{
    "attachments[]": [File1, File2]
}
```

---

## 📧 Email Notifications

### Notification Types

1. **Ticket Created**
   - Sent to: Customer
   - Trigger: New ticket submission

2. **Ticket Updated**
   - Sent to: Customer, assigned agent
   - Trigger: Status/priority change

3. **Ticket Assigned**
   - Sent to: Assigned agent
   - Trigger: Ticket assignment

4. **Comment Added**
   - Sent to: Customer, assigned agent
   - Trigger: New comment (public only)

5. **Status Changed**
   - Sent to: Customer
   - Trigger: Status change

### Email Queue System

Emails are queued and processed by cron job:

```bash
# Process pending emails
php scripts/process-emails.php

# View email logs
tail -f storage/logs/email-cron-*.log
```

### User Preferences

Users can customize which notifications they receive:

```
/notifications/preferences
```

---

## 📊 Activity Logging

All actions are logged in `activity_logs` table:

### Tracked Actions
- `created` - Ticket created
- `updated` - Ticket updated
- `commented` - Comment added
- `closed` - Ticket closed
- `reopened` - Ticket reopened
- `attachment_added` - File uploaded

### Log Fields
- Ticket ID
- User ID
- Action type
- Field name (for updates)
- Old value
- New value
- IP address
- User agent
- Timestamp

---

## 🔍 Search & Filtering

### Basic Search
```
GET /tickets/search?q=login+issue
```

### Full-Text Search (requires FULLTEXT index)
```sql
ALTER TABLE tickets ADD FULLTEXT(title, description);
```

### Advanced Filters
```
GET /tickets?
    status=open&
    priority=high&
    category_id=1&
    assigned_to=me&
    date_from=2024-01-01&
    sort_by=priority&
    sort_order=DESC
```

---

## 📈 Analytics

### Dashboard Metrics
- Total tickets
- Open tickets
- In progress tickets
- Closed tickets
- Urgent priority count
- Today's tickets

### Reports
- Priority distribution
- Category distribution
- Agent performance
- Response time averages
- Resolution time tracking

---

## 🛠️ Troubleshooting

### Email Not Sending

1. **Check SMTP credentials**
   ```bash
   php test-email.php
   ```

2. **Verify email queue**
   ```sql
   SELECT * FROM email_notifications WHERE status = 'failed';
   ```

3. **Check logs**
   ```bash
   tail -f storage/logs/email-cron-*.log
   ```

4. **Try alternative port**
   ```env
   # Try 587 instead of 465
   MAIL_PORT=587
   MAIL_ENCRYPTION=tls
   ```

### Database Connection Error

```bash
# Test connection
php -r "new PDO('mysql:host=localhost;dbname=helpdesk_db', 'user', 'pass');"
```

### File Upload Issues

```bash
# Check permissions
ls -la public/uploads/

# Fix permissions
chmod 755 public/uploads/
chown www-data:www-data public/uploads/
```

### Session Problems

```bash
# Clear sessions
rm -rf storage/sessions/*

# Check PHP session settings
php -i | grep session
```

---

## 🔐 Security Best Practices

1. **Never commit `.env` to version control**
2. **Use strong database passwords**
3. **Enable HTTPS in production**
4. **Keep PHP and dependencies updated**
5. **Regular database backups**
6. **Monitor error logs**
7. **Implement rate limiting**
8. **Validate all user inputs**

---

## 📚 Directory Structure

```
helpdesk-system/
├── app/
│   ├── Config/          # Database configuration
│   ├── Controllers/     # MVC Controllers
│   ├── Models/          # Data models
│   ├── Helpers/         # Utility functions
│   └── Services/        # Business logic (EmailService)
├── public/
│   ├── assets/          # CSS, JS, images
│   ├── uploads/         # User uploaded files
│   └── index.php        # Entry point
├── routes/
│   └── web.php          # Route definitions
├── scripts/
│   ├── init-db.php      # Database setup
│   └── process-emails.php
├── storage/
│   └── logs/            # Application logs
├── views/
│   ├── layouts/         # Page templates
│   ├── tickets/         # Ticket views
│   ├── notifications/   # Notification views
│   └── ...
├── vendor/              # Composer dependencies
├── .env.example         # Environment template
├── .gitignore
├── composer.json
└── README.md
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Developer

Developed with ❤️ by Trio Agus

**Contact:**
- Email: your-email@example.com
- GitHub: [@yourusername](https://github.com/yourusername)

---

## 🙏 Acknowledgments

- **PHPMailer** - Email sending library
- **Tailwind CSS** - UI framework
- **Font Awesome** - Icons

---

## 📅 Version History

### v1.0.0 (2025-12-08)
- ✅ Initial release
- ✅ Ticket management system
- ✅ User authentication & roles
- ✅ Email notifications
- ✅ Category management
- ✅ Activity logging
- ✅ File attachments
- ✅ Advanced search & filtering

---

## 🚧 Roadmap

### Planned Features
- [ ] Real-time chat support
- [ ] Knowledge base
- [ ] Customer satisfaction surveys
- [ ] SLA (Service Level Agreement) tracking
- [ ] Multi-language support
- [ ] REST API
- [ ] Mobile app
- [ ] Integration with Slack/Teams
- [ ] Automated ticket routing
- [ ] Custom fields

---

**⭐ If you find this project helpful, please give it a star on GitHub!**
