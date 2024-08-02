<?php

require 'config.php';

// Check IP address

function getUserIP() {
    // Check for the shared internet IP (e.g., from a proxy)
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    // Check for the forwarded IP (e.g., from a load balancer)
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    // Default fallback: remote IP
    else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Handle cases where the IP address may contain multiple addresses (comma-separated list)
    if (strpos($ip, ',') !== false) {
        $ip = explode(',', $ip)[0];
    }

    return $ip;
}

$url = isset($_GET['url']) ? $_GET['url'] : null;
$date = date('Y-m-d');
$dateTime = date('Y-m-d h:i:s');
$ip_address = getUserIP();

if(!$url || !filter_var($url, FILTER_VALIDATE_URL)){
    echo json_encode(['status' => 'failure']);
    exit;
}

if(!in_array($url, $urls_arr)){
    echo json_encode(['status' => 'failure']);
    exit;
}

$sql = "INSERT INTO referrals.track_links (link_text, created, ip_address) VALUES ('{$conn->real_escape_string($url)}', '$dateTime', '$ip_address')";

if($conn->query($sql)){
    header('Location:' . $url);
}

$conn->close();

echo json_encode(['status' => 'failure']);
exit;
?>