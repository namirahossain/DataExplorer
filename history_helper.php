<?php

// history helper - call this after user actions to log history

function ensure_history_table($conn) {
    $conn->query("
        CREATE TABLE IF NOT EXISTS history (
            history_id INT AUTO_INCREMENT PRIMARY KEY,
            time DATETIME DEFAULT CURRENT_TIMESTAMP,
            user_id VARCHAR(100) NOT NULL,
            report_id VARCHAR(100) DEFAULT NULL
        )
    ");
}

function log_history($conn, $user_id, $report_id = null) {

    if (empty($user_id)) {
        return false;
    }

    ensure_history_table($conn);

    $sql = "INSERT INTO history (user_id, report_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param("ss", $user_id, $report_id);
    $result = $stmt->execute();
    $stmt->close();

    return $result;
}

?>
