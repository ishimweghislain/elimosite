<?php
/**
 * AJAX Image Upload Handler
 * Handles asynchronous upload of multiple images
 */

require_once '../../includes/upload-config.php';
require_once '../../includes/config.php';
require_admin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$response = [
    'success' => false,
    'message' => '',
    'uploaded_files' => [],
    'failed_files' => []
];

// Check if files were uploaded
if (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
    $response['message'] = 'No files uploaded';
    echo json_encode($response);
    exit;
}

$upload_dir = '../../images/';
$allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$max_file_size = 5 * 1024 * 1024; // 5MB per file

// Process each uploaded file
$total_files = count($_FILES['images']['name']);

for ($i = 0; $i < $total_files; $i++) {
    $file_name = $_FILES['images']['name'][$i];
    $file_tmp = $_FILES['images']['tmp_name'][$i];
    $file_type = $_FILES['images']['type'][$i];
    $file_error = $_FILES['images']['error'][$i];
    $file_size = $_FILES['images']['size'][$i];
    
    // Skip if there's an error with this file
    if ($file_error !== UPLOAD_ERR_OK) {
        $response['failed_files'][] = [
            'name' => $file_name,
            'error' => 'Upload error code: ' . $file_error
        ];
        continue;
    }
    
    // Validate file type
    if (!in_array($file_type, $allowed_types)) {
        $response['failed_files'][] = [
            'name' => $file_name,
            'error' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.'
        ];
        continue;
    }
    
    // Validate file size
    if ($file_size > $max_file_size) {
        $response['failed_files'][] = [
            'name' => $file_name,
            'error' => 'File too large. Maximum size is 5MB.'
        ];
        continue;
    }
    
    // Generate unique filename
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $unique_filename = uniqid('img_', true) . '_' . time() . '.' . $file_extension;
    $destination = $upload_dir . $unique_filename;
    
    // Move uploaded file
    if (move_uploaded_file($file_tmp, $destination)) {
        $response['uploaded_files'][] = [
            'original_name' => $file_name,
            'saved_name' => $unique_filename
        ];
    } else {
        $response['failed_files'][] = [
            'name' => $file_name,
            'error' => 'Failed to save file to server'
        ];
    }
}

// Set success status
if (count($response['uploaded_files']) > 0) {
    $response['success'] = true;
    $response['message'] = count($response['uploaded_files']) . ' file(s) uploaded successfully';
    
    if (count($response['failed_files']) > 0) {
        $response['message'] .= ', ' . count($response['failed_files']) . ' file(s) failed';
    }
} else {
    $response['message'] = 'All uploads failed';
}

echo json_encode($response);
