<?php
require_once '../includes/config.php';
require_login();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $property_id = (int)($_POST['property_id'] ?? 0);
    $is_edit = $property_id > 0;
    
    // Validate required fields
    $required_fields = ['title', 'category', 'property_type', 'status', 'location', 'description'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $error = "All required fields must be filled.";
            break;
        }
    }
    
    if (empty($error)) {
        $status = $_POST['status'];
        if (!is_admin()) {
            $status = 'draft';
        }

        $data = [
            'title' => clean_input($_POST['title']),
            'category' => $_POST['category'],
            'property_type' => $_POST['property_type'],
            'status' => $status,
            'price' => !empty($_POST['price']) ? (float)$_POST['price'] : null,
            'location' => clean_input($_POST['location']),
            'province' => clean_input($_POST['province'] ?? ''),
            'district' => clean_input($_POST['district'] ?? ''),
            'bedrooms' => !empty($_POST['bedrooms']) ? (int)$_POST['bedrooms'] : null,
            'bathrooms' => !empty($_POST['bathrooms']) ? (int)$_POST['bathrooms'] : null,
            'garage' => !empty($_POST['garage']) ? (int)$_POST['garage'] : 0,
            'size_sqm' => !empty($_POST['size_sqm']) ? (float)$_POST['size_sqm'] : null,
            'year_built' => !empty($_POST['year_built']) ? (int)$_POST['year_built'] : null,
            'description' => clean_input($_POST['description']),
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (!$is_edit) {
            $data['created_by'] = $_SESSION['user_id'];
        }
        
        // Handle file upload
        if (isset($_FILES['image_main']) && $_FILES['image_main']['error'] === UPLOAD_ERR_OK) {
            $upload_result = upload_file($_FILES['image_main'], '../images/uploads');
            if ($upload_result['success']) {
                $data['image_main'] = $upload_result['filename'];
            } else {
                $error = $upload_result['message'];
            }
        }
        
        if (empty($error)) {
            if ($is_edit) {
                // Update existing property
                $success = update_record('properties', $data, ['id' => $property_id]);
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

// Redirect back with message
$redirect_url = 'properties-new.php';
if (!empty($message)) {
    $redirect_url .= '?success=' . urlencode($message);
} elseif (!empty($error)) {
    $redirect_url .= '?error=' . urlencode($error);
}

header('Location: ' . $redirect_url);
exit;
?>
