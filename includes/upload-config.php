<?php
/**
 * Upload Configuration
 * Increases PHP limits for handling large file uploads
 * 
 * IMPORTANT: For cPanel hosting, you may also need to update these settings in:
 * - cPanel > Select PHP Version > Options
 * - Or create/edit .htaccess file with these directives
 */

// Increase maximum execution time to 10 minutes
@ini_set('max_execution_time', 600);

// Increase maximum input time to 10 minutes
@ini_set('max_input_time', 600);

// Increase memory limit to 512MB
@ini_set('memory_limit', '512M');

// Increase maximum upload file size to 50MB
@ini_set('upload_max_filesize', '50M');

// Increase maximum POST data size to 100MB (should be larger than upload_max_filesize)
@ini_set('post_max_size', '100M');

// Increase maximum number of files that can be uploaded at once
@ini_set('max_file_uploads', 100);
