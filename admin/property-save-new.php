<?php
require_once '../includes/config.php';
require_admin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = (int)($_POST['id'] ?? 0);
    $is_edit = $property_id > 0;
    
    // Validate required fields
    $required_fields = ['title', 'category', 'property_type', 'location'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $error = "All required fields must be filled.";
            break;
        }
    }
    
    if (empty($error)) {
        $status = isset($_POST['save_draft']) ? 'draft' : clean_input($_POST['status']);
        
        $data = [
            'title' => clean_input($_POST['title']),
            'description' => clean_input($_POST['description'] ?? ''),
            'category' => clean_input($_POST['category']),
            'property_type' => clean_input($_POST['property_type']),
            'status' => $status,
            'price' => !empty($_POST['price']) ? (float)$_POST['price'] : 0,
            'location' => clean_input($_POST['location']),
            'province' => clean_input($_POST['province'] ?? ''),
            'district' => clean_input($_POST['district'] ?? ''),
            'bedrooms' => !empty($_POST['bedrooms']) ? (int)$_POST['bedrooms'] : 0,
            'bathrooms' => !empty($_POST['bathrooms']) ? (int)$_POST['bathrooms'] : 0,
            'garage' => !empty($_POST['garage']) ? (int)$_POST['garage'] : 0,
            'size_sqm' => !empty($_POST['size_sqm']) ? (float)$_POST['size_sqm'] : 0,
            'year_built' => !empty($_POST['year_built']) ? (int)$_POST['year_built'] : date('Y'),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'prop_id' => clean_input($_POST['prop_id'] ?? ''),
            'stories' => intval($_POST['stories'] ?? 0),
            'furnished' => clean_input($_POST['furnished'] ?? ''),
            'multi_family' => clean_input($_POST['multi_family'] ?? ''),
            'plot_size' => floatval($_POST['plot_size'] ?? 0),
            'zoning' => clean_input($_POST['zoning'] ?? ''),
            'views' => clean_input($_POST['views'] ?? ''),
            'ideal_for' => clean_input($_POST['ideal_for'] ?? ''),
            'proximity' => clean_input($_POST['proximity'] ?? ''),
            'features' => isset($_POST['features']) ? json_encode($_POST['features']) : json_encode([]),
            'amenities' => isset($_POST['amenities']) ? json_encode($_POST['amenities']) : json_encode([])
        ];
        
        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_result = upload_file($_FILES['image'], '../images/', ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], 5 * 1024 * 1024); // 5MB
            if ($upload_result['success']) {
                $data['image_main'] = $upload_result['filename']; // Fixed column name
            } else {
                $error = $upload_result['message'];
            }
        }
        
        if (empty($error)) {
            if ($is_edit) {
                // Update existing property
                $success = update_record('properties', $data, $property_id);
                if ($success) {
                    $message = 'Property updated successfully!';
                } else {
                    $error = 'Failed to update property.';
                }
            } else {
                // Insert new property
                $property_id = insert_record('properties', $data);
                if ($property_id) {
                    $message = 'Property added successfully!';
                } else {
                    $error = 'Failed to add property.';
                }
            }
        }
    }
}

// Redirect logic based on status and category
$redirect_url = 'properties-new.php';
if (isset($status) && $status === 'draft') {
    $redirect_url = 'drafts.php';
} elseif (isset($_POST['category']) && $_POST['category'] === 'Developments') {
    $redirect_url = 'developments.php';
}

if (!empty($message)) {
    $redirect_url .= (strpos($redirect_url, '?') === false ? '?' : '&') . 'success=' . urlencode($message);
} elseif (!empty($error)) {
    $redirect_url .= (strpos($redirect_url, '?') === false ? '?' : '&') . 'error=' . urlencode($error);
}

header('Location: ' . $redirect_url);
exit;
?>
