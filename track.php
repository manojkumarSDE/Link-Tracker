<?php
include_once('config.php');

$url = isset($_GET['url']) ? $_GET['url'] : null;
$date = date('Y-m-d h:i:s');

if(!$url || !filter_var($url, FILTER_VALIDATE_URL)){
    echo json_encode(['status' => 'failure']);
    exit;
}

$sql = "INSERT INTO referrals.track_links (link_text, created) VALUES ('{$conn->real_escape_string($url)}', '$date')";

if($conn->query($sql)){
    header('Location:' . $url);
    exit;
}

$conn->close();

echo json_encode(['status' => 'failure']);
exit;
?>