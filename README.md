# Elimo Real Estate Property Management System

A fully functional property management system built with PHP and MySQL, based on the Elimo Real Estate HTML template.

## Features

### Frontend
- **Property Search & Filtering**: Advanced search with multiple filters (location, price, bedrooms, bathrooms, features)
- **Property Listings**: Dynamic property display with pagination
- **Property Details**: Individual property pages with full information
- **Contact Forms**: Working contact and inquiry forms
- **Newsletter Subscription**: Email subscription functionality
- **User Authentication**: Login and registration system
- **Responsive Design**: Mobile-friendly interface

### Admin Panel
- **Dashboard**: Statistics and overview of all content
- **Property Management**: Add, edit, delete properties
- **Team Management**: Manage team members and their profiles
- **Blog Management**: Create and manage blog posts
- **FAQ Management**: Dynamic FAQ system
- **Contact Messages**: View and manage contact inquiries
- **Settings**: Site configuration management

## Database Schema

The system uses the following main tables:
- `users` - User authentication and roles
- `properties` - Property listings
- `team_members` - Team/agent information
- `blog_posts` - Blog articles
- `faqs` - Frequently asked questions
- `contact_messages` - Contact form submissions
- `newsletter_subscribers` - Newsletter subscribers
- `property_inquiries` - Property-specific inquiries
- `site_settings` - Configuration settings

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- XAMPP, MAMP, or similar local server environment

### Step 1: Database Setup

1. Create a new database named `elimo_real_estate`
2. Import the database schema from `database_schema.sql`
3. Verify all tables are created successfully

```sql
mysql -u root -p elimo_real_estate < database_schema.sql
```

### Step 2: Configuration

1. Copy the files to your web server directory
2. Update database credentials in `includes/config.php` if needed:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'elimo_real_estate');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Step 3: File Permissions

Ensure the following directories are writable by the web server:
- `images/uploads/` (for property images)

```bash
chmod 755 images/uploads/
```

### Step 4: Access the System

1. **Frontend**: `http://localhost/elimosite/`
2. **Admin Panel**: `http://localhost/elimosite/admin/`

### Default Admin Login
- **Username**: `admin`
- **Password**: `admin123`

## Directory Structure

```
elimosite/
├── index.php                 # Homepage
├── about-us.php              # About page
├── contact-us.php            # Contact page
├── search-results.php        # Search results
├── developments.php          # Developments page
├── team.php                  # Team page
├── blog.php                  # Blog page
├── faqs.php                  # FAQs page
├── logout.php                # Logout script
├── database_schema.sql       # Database structure
├── README.md                 # This file
├── includes/                 # Core PHP files
│   ├── config.php            # Configuration and settings
│   ├── database.php          # Database functions
│   ├── functions.php         # Utility functions
│   ├── footer.php            # Footer template
│   └── login-modal.php       # Login/register modal
├── admin/                    # Admin panel
│   ├── index.php             # Admin dashboard
│   ├── properties.php        # Property management
│   ├── property-save.php     # Property save handler
│   ├── team.php              # Team management
│   ├── blog.php              # Blog management
│   ├── faqs.php              # FAQ management
│   ├── contacts.php          # Contact messages
│   ├── inquiries.php         # Property inquiries
│   ├── subscribers.php       # Newsletter subscribers
│   └── settings.php          # Site settings
├── css/                      # Stylesheets
├── js/                       # JavaScript files
├── images/                   # Images
│   └── uploads/              # Uploaded property images
└── vendors/                  # Third-party libraries
```

## Usage Instructions

### Adding Properties

1. Login to admin panel
2. Navigate to "Properties" section
3. Click "Add Property" button
4. Fill in property details:
   - Title, description, category, type, status
   - Price, location, specifications
   - Upload images
   - Set as featured if desired
5. Save the property

### Managing Content

All content sections can be managed through the admin panel:
- **Properties**: Add/edit/delete property listings
- **Team Members**: Manage agent profiles
- **Blog Posts**: Create and publish articles
- **FAQs**: Add/edit frequently asked questions
- **Contact Messages**: View and respond to inquiries

### User Roles

- **Admin**: Full access to all features and content management
- **User**: Can browse properties, submit inquiries, subscribe to newsletter

## Security Features

- **SQL Injection Prevention**: All database queries use prepared statements
- **XSS Protection**: Input sanitization and output escaping
- **CSRF Protection**: Form tokens for state-changing operations
- **Password Hashing**: Secure password storage using PHP's password_hash()
- **Session Management**: Secure session handling with timeout

## Customization

### Adding New Fields

1. Update the database schema
2. Modify the corresponding PHP functions
3. Update the admin forms
4. Update frontend display templates

### Changing Design

The system maintains the original HTML template structure:
- CSS files are in the `css/` directory
- JavaScript files are in the `js/` directory
- Template files use the original HTML structure with PHP dynamic content

## WordPress Migration

The code is modular and structured for easy WordPress migration:
- Functions are organized in separate include files
- Database operations use PDO (compatible with WordPress $wpdb)
- Template structure follows WordPress theme patterns
- No WordPress-specific code is used currently

## Support

For issues or questions:
1. Check the database connection in `includes/config.php`
2. Verify file permissions for upload directories
3. Ensure PHP error reporting is enabled for debugging
4. Check browser console for JavaScript errors

## License

This project is based on the Elimo Real Estate HTML template and converted to a functional PHP/MySQL system.
