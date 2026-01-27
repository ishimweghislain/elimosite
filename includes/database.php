<?php
/**
 * Database connection and helper functions
 */

// Create PDO database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

/**
 * Execute a prepared statement with optional parameters
 */
function execute_query($sql, $params = []) {
    global $pdo;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return false;
    }
}

/**
 * Get a single record
 */
function get_record($table, $id, $id_column = 'id') {
    $sql = "SELECT * FROM $table WHERE $id_column = ?";
    $stmt = execute_query($sql, [$id]);
    return $stmt ? $stmt->fetch() : false;
}

/**
 * Get multiple records with optional conditions
 */
function get_records($table, $conditions = [], $order = '', $limit = '') {
    $sql = "SELECT * FROM $table";
    $params = [];
    
    // Handle string conditions (for ORDER BY)
    if (is_string($conditions) && !empty($conditions)) {
        $sql .= " " . $conditions;
        $conditions = [];
    }
    
    // Handle array conditions (for WHERE clauses)
    if (!empty($conditions) && is_array($conditions)) {
        $where_clauses = [];
        foreach ($conditions as $column => $value) {
            $where_clauses[] = "$column = ?";
            $params[] = $value;
        }
        $sql .= " WHERE " . implode(' AND ', $where_clauses);
    }
    
    if (!empty($order)) {
        $sql .= " ORDER BY " . $order;
    }
    
    if (!empty($limit)) {
        $sql .= " LIMIT " . (int)$limit;
    }
    
    $stmt = execute_query($sql, $params);
    return $stmt ? $stmt->fetchAll() : [];
}

/**
 * Insert a record
 */
function insert_record($table, $data) {
    global $pdo;
    
    $columns = implode(', ', array_keys($data));
    $placeholders = implode(', ', array_fill(0, count($data), '?'));
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        return $pdo->lastInsertId();
    } catch (PDOException $e) {
        error_log("Insert error: " . $e->getMessage());
        return false;
    }
}

/**
 * Update a record
 */
function update_record($table, $data, $conditions) {
    global $pdo;
    
    // Handle case where data is not an array
    if (!is_array($data)) {
        error_log("Update error: data must be an array, " . gettype($data) . " given");
        return false;
    }
    
    $set_clauses = [];
    $params = [];
    
    foreach ($data as $column => $value) {
        $set_clauses[] = "$column = ?";
        $params[] = $value;
    }
    
    // Handle single ID
    if (is_numeric($conditions)) {
        $sql = "UPDATE $table SET " . implode(', ', $set_clauses) . " WHERE id = ?";
        $params[] = $conditions;
    } else {
        // Handle array conditions
        $where_clauses = [];
        foreach ($conditions as $column => $value) {
            $where_clauses[] = "$column = ?";
            $params[] = $value;
        }
        
        $sql = "UPDATE $table SET " . implode(', ', $set_clauses) . " WHERE " . implode(' AND ', $where_clauses);
    }
    
    try {
        $stmt = $pdo->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Update error: " . $e->getMessage());
        return false;
    }
}

/**
 * Delete a record
 */
function delete_record($table, $conditions) {
    // Handle single ID
    if (is_numeric($conditions)) {
        $sql = "DELETE FROM $table WHERE id = ?";
        $params = [$conditions];
    } else {
        // Handle array conditions
        $where_clauses = [];
        $params = [];
        
        foreach ($conditions as $column => $value) {
            $where_clauses[] = "$column = ?";
            $params[] = $value;
        }
        
        $sql = "DELETE FROM $table WHERE " . implode(' AND ', $where_clauses);
    }
    
    $stmt = execute_query($sql, $params);
    return $stmt ? $stmt->rowCount() > 0 : false;
}

/**
 * Count records
 */
function count_records($table, $conditions = []) {
    $sql = "SELECT COUNT(*) as count FROM $table";
    $params = [];
    
    if (!empty($conditions)) {
        $where_clauses = [];
        foreach ($conditions as $column => $value) {
            $where_clauses[] = "$column = ?";
            $params[] = $value;
        }
        $sql .= " WHERE " . implode(' AND ', $where_clauses);
    }
    
    $stmt = execute_query($sql, $params);
    return $stmt ? (int)$stmt->fetch()['count'] : 0;
}

/**
 * Get properties with filters
 */
function get_properties($filters = [], $page = 1, $per_page = 10) {
    $sql = "SELECT * FROM properties WHERE 1=1";
    $params = [];
    
    // Apply filters
    if (!empty($filters['category'])) {
        $sql .= " AND category = ?";
        $params[] = $filters['category'];
    }
    
    if (!empty($filters['property_type'])) {
        $sql .= " AND property_type = ?";
        $params[] = $filters['property_type'];
    }
    
    if (!empty($filters['status'])) {
        $sql .= " AND status = ?";
        $params[] = $filters['status'];
    } else {
        $sql .= " AND status != 'draft'";
    }

    if (!empty($filters['exclude_category'])) {
        $sql .= " AND category != ?";
        $params[] = $filters['exclude_category'];
    }

    if (!empty($filters['province'])) {
        $sql .= " AND province = ?";
        $params[] = $filters['province'];
    }
    
    if (!empty($filters['search'])) {
        $sql .= " AND (title LIKE ? OR location LIKE ? OR province LIKE ? OR district LIKE ?)";
        $search_param = '%' . $filters['search'] . '%';
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }

    if (!empty($filters['location'])) {
        $sql .= " AND (location LIKE ? OR province LIKE ? OR district LIKE ?)";
        $location_param = '%' . $filters['location'] . '%';
        $params[] = $location_param;
        $params[] = $location_param;
        $params[] = $location_param;
    }
    
    if (!empty($filters['min_price'])) {
        $sql .= " AND price >= ?";
        $params[] = $filters['min_price'];
    }
    
    if (!empty($filters['max_price'])) {
        $sql .= " AND price <= ?";
        $params[] = $filters['max_price'];
    }
    
    if (!empty($filters['bedrooms'])) {
        $sql .= " AND bedrooms >= ?";
        $params[] = $filters['bedrooms'];
    }
    
    if (!empty($filters['bathrooms'])) {
        $sql .= " AND bathrooms >= ?";
        $params[] = $filters['bathrooms'];
    }
    
    // Count total records
    $count_sql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
    $count_stmt = execute_query($count_sql, $params);
    $total = $count_stmt ? (int)$count_stmt->fetch()['total'] : 0;
    
    // Add pagination
    $offset = ($page - 1) * $per_page;
    $sql .= " ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
    
    $stmt = execute_query($sql, $params);
    $properties = $stmt ? $stmt->fetchAll() : [];
    
    return [
        'properties' => $properties,
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page,
        'total_pages' => ceil($total / $per_page)
    ];
}

/**
 * Search properties
 */
function search_properties($query, $page = 1, $per_page = 10) {
    $sql = "SELECT * FROM properties WHERE (title LIKE ? OR description LIKE ? OR location LIKE ?) AND status != 'draft'";
    $search_param = '%' . $query . '%';
    $params = [$search_param, $search_param, $search_param];
    
    // Count total records
    $count_sql = str_replace("SELECT *", "SELECT COUNT(*) as total", $sql);
    $count_stmt = execute_query($count_sql, $params);
    $total = $count_stmt ? (int)$count_stmt->fetch()['total'] : 0;
    
    // Add pagination
    $offset = ($page - 1) * $per_page;
    $sql .= " ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
    
    $stmt = execute_query($sql, $params);
    $properties = $stmt ? $stmt->fetchAll() : [];
    
    return [
        'properties' => $properties,
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page,
        'total_pages' => ceil($total / $per_page)
    ];
}
?>
