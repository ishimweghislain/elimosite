<?php
/**
 * General utility functions for Elimo Real Estate
 */

/**
 * Get page title based on current file
 */
function get_page_title() {
    $current_file = basename($_SERVER['PHP_SELF'], '.php');
    $titles = [
        'index' => 'Home',
        'about-us' => 'About Us',
        'developments' => 'Developments',
        'team' => 'Our Team',
        'blog' => 'Blog',
        'faqs' => 'FAQs',
        'contact-us' => 'Contact Us',
        'search-results' => 'Search Results',
        'property-detail' => 'Property Details',
        'login' => 'Login',
        'register' => 'Register'
    ];
    
    return isset($titles[$current_file]) ? $titles[$current_file] : 'Elimo Real Estate';
}

/**
 * Get navigation menu items
 */
function get_nav_items() {
    return [
        ['url' => 'index.php', 'label' => 'Home', 'active' => basename($_SERVER['PHP_SELF']) === 'index.php'],
        ['url' => 'about-us.php', 'label' => 'About', 'active' => basename($_SERVER['PHP_SELF']) === 'about-us.php'],
        ['url' => 'properties.php', 'label' => 'Properties', 'active' => basename($_SERVER['PHP_SELF']) === 'properties.php'],
        ['url' => 'developments.php', 'label' => 'Developments', 'active' => basename($_SERVER['PHP_SELF']) === 'developments.php'],
        ['url' => 'team.php', 'label' => 'Team', 'active' => basename($_SERVER['PHP_SELF']) === 'team.php'],
        ['url' => 'blog.php', 'label' => 'Blog', 'active' => basename($_SERVER['PHP_SELF']) === 'blog.php'],
        ['url' => 'faqs.php', 'label' => 'FAQ\'s', 'active' => basename($_SERVER['PHP_SELF']) === 'faqs.php'],
        ['url' => 'contact-us.php', 'label' => 'Contact', 'active' => basename($_SERVER['PHP_SELF']) === 'contact-us.php']
    ];
}

/**
 * Render navigation menu
 */
function render_nav_menu() {
    $items = get_nav_items();
    $html = '<ul class="navbar-nav hover-menu main-menu px-0 mx-xl-n4">';
    
    foreach ($items as $item) {
        $active_class = $item['active'] ? 'active' : '';
        $html .= sprintf(
            '<li class="nav-item py-2 py-xl-5 px-0 px-xl-3">
                <a class="nav-link p-0 %s" href="%s">%s</a>
            </li>',
            $active_class,
            $item['url'],
            $item['label']
        );
    }
    
    $html .= '</ul>';
    return $html;
}

/**
 * Get featured properties
 */
function get_featured_properties($limit = 6) {
    $sql = "SELECT * FROM properties WHERE is_featured = 1 AND status != 'sold' AND status != 'draft' ORDER BY created_at DESC LIMIT ?";
    $stmt = execute_query($sql, [$limit]);
    return $stmt ? $stmt->fetchAll() : [];
}

/**
 * Get recent properties
 */
function get_recent_properties($limit = 6) {
    $sql = "SELECT * FROM properties WHERE status != 'sold' AND status != 'draft' ORDER BY created_at DESC LIMIT ?";
    $stmt = execute_query($sql, [$limit]);
    return $stmt ? $stmt->fetchAll() : [];
}

/**
 * Get property by ID
 */
function get_property($id) {
    return get_record('properties', $id);
}

/**
 * Get team members
 */
function get_team_members() {
    return get_records('team_members', ['is_active' => 1], 'name ASC');
}

/**
 * Get FAQs
 */
function get_faqs($category = null) {
    if ($category) {
        $sql = "SELECT * FROM faqs WHERE category = ? ORDER BY order_index ASC";
        $params = [$category];
    } else {
        $sql = "SELECT * FROM faqs ORDER BY order_index ASC";
        $params = [];
    }
    
    $stmt = execute_query($sql, $params);
    return $stmt ? $stmt->fetchAll() : [];
}
/**
 * Get blog posts
 */
function get_blog_posts($limit = 10, $category = '') {
    $conditions = ['status' => 'published'];
    if (!empty($category)) {
        $conditions['category'] = $category;
    }
    return get_records('blog_posts', $conditions, 'created_at DESC', $limit);
}

/**
 * Get blog post by slug
 */
function get_blog_post($slug) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = ? AND status = 'published'");
    $stmt->execute([$slug]);
    return $stmt->fetch();
}



/**
 * Handle contact form submission
 */
function handle_contact_form() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
        
        // Validate email
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Please enter a valid email address.'];
        }
        
        // Insert message
        $data = [
            'first_name' => clean_input($_POST['first-name']),
            'last_name' => clean_input($_POST['last-name']),
            'email' => clean_input($_POST['email']),
            'phone' => clean_input($_POST['phone'] ?? ''),
            'message' => clean_input($_POST['message']),
            'status' => 'new'
        ];
        
        $message_id = insert_record('contact_messages', $data);
        
        if ($message_id) {
            // Send email notification
            $admin_email = get_setting('admin_email', 'admin@elimo.rw');
            $subject = 'New Contact Form Submission';
            $message = "New contact form submission:\n\n";
            $message .= "Name: " . $data['first_name'] . " " . $data['last_name'] . "\n";
            $message .= "Email: " . $data['email'] . "\n";
            $message .= "Phone: " . $data['phone'] . "\n\n";
            $message .= "Message:\n" . $data['message'];
            
            $headers = "From: " . $data['email'] . "\r\n";
            $headers .= "Reply-To: " . $data['email'] . "\r\n";
            $headers .= "X-Mailer: PHP/" . phpversion();
            
            mail($admin_email, $subject, $message, $headers);
            
            return ['success' => true, 'message' => 'Your message has been sent successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to send message. Please try again.'];
        }
    }
    return null;
}

/**
 * Handle newsletter subscription
 */
function handle_newsletter_subscription() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newsletter_form'])) {
        $email = clean_input($_POST['email'] ?? '');
        
        if (empty($email)) {
            return ['success' => false, 'message' => 'Please enter your email address.'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Please enter a valid email address.'];
        }
        
        // Check if already subscribed
        if (count_records('newsletter_subscribers', ['email' => $email]) > 0) {
            return ['success' => false, 'message' => 'You are already subscribed.'];
        }
        
        // Add subscriber
        $subscriber_id = insert_record('newsletter_subscribers', ['email' => $email]);
        
        if ($subscriber_id) {
            return ['success' => true, 'message' => 'Thank you for subscribing!'];
        } else {
            return ['success' => false, 'message' => 'Failed to subscribe. Please try again.'];
        }
    }
    return null;
}

/**
 * Handle property inquiry
 */
function handle_property_inquiry() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inquiry_form'])) {
        // Verify CSRF token
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            return ['success' => false, 'message' => 'Invalid request. Please try again.'];
        }
        
        $property_id = (int)($_POST['property_id'] ?? 0);
        $name = clean_input($_POST['name'] ?? '');
        $email = clean_input($_POST['email'] ?? '');
        $phone = clean_input($_POST['phone'] ?? '');
        $message = clean_input($_POST['message'] ?? '');
        
        if (empty($property_id) || empty($name) || empty($email)) {
            return ['success' => false, 'message' => 'Please fill in all required fields.'];
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Please enter a valid email address.'];
        }
        
        // Check if property exists
        $property = get_property($property_id);
        if (!$property) {
            return ['success' => false, 'message' => 'Property not found.'];
        }
        
        // Insert inquiry
        $data = [
            'property_id' => $property_id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'message' => $message
        ];
        
        $inquiry_id = insert_record('property_inquiries', $data);
        
        if ($inquiry_id) {
            return ['success' => true, 'message' => 'Your inquiry has been sent successfully!'];
        } else {
            return ['success' => false, 'message' => 'Failed to send inquiry. Please try again.'];
        }
    }
    return null;
}

/**
 * Get property statistics
 */
function get_property_stats() {
    $stats = [];
    
    $stats['total_properties'] = count_records('properties');
    $stats['for_rent'] = count_records('properties', ['status' => 'for-rent']);
    $stats['for_sale'] = count_records('properties', ['status' => 'for-sale']);
    $stats['developments'] = count_records('properties', ['category' => 'Developments']);
    
    return $stats;
}

/**
 * Format date
 */
function format_date($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

/**
 * Create URL-friendly slug
 */
function create_slug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-');
}

/**
 * Upload file
 */
function upload_file($file, $destination_folder, $allowed_types = null, $max_size = null) {
    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'message' => 'Invalid file upload.'];
    }
    
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return ['success' => false, 'message' => 'File too large.'];
        default:
            return ['success' => false, 'message' => 'Unknown upload error.'];
    }
    
    // Use provided max size or default
    $max_file_size = $max_size ?? MAX_FILE_SIZE;
    if ($file['size'] > $max_file_size) {
        return ['success' => false, 'message' => 'File too large.'];
    }
    
    // Use provided allowed types or default
    $allowed_file_types = $allowed_types ?? ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed_file_types)) {
        return ['success' => false, 'message' => 'Invalid file type.'];
    }
    
    if (!is_dir($destination_folder)) {
        mkdir($destination_folder, 0755, true);
    }
    
    // Sanitize filename
    $original_name = basename($file['name']);
    $sanitized_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $original_name);
    $filename = uniqid() . '_' . $sanitized_name;
    $destination = $destination_folder . '/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'message' => 'Failed to upload file.'];
    }
}

/**
 * Send email (basic implementation)
 */
function send_email($to, $subject, $message, $from = 'noreply@elimo.rw') {
    $headers = "From: $from\r\n";
    $headers .= "Reply-To: $from\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}
?>
