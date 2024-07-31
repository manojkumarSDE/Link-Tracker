<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'config.php';

$mail = new PHPMailer(true);

$url = isset($_GET['url']) ? $_GET['url'] : null;
$date = date('Y-m-d');
$dateTime = date('Y-m-d h:i:s');

if(!$url || !filter_var($url, FILTER_VALIDATE_URL)){
    echo json_encode(['status' => 'failure']);
    exit;
}

$sql = "INSERT INTO referrals.track_links (link_text, created) VALUES ('{$conn->real_escape_string($url)}', '$dateTime')";

if($conn->query($sql)){

    try {
        //Server settings
        $mail->SMTPDebug = 2;                                       // Enable verbose debug output
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host       = 'smtp.gmail.com';                     // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = $email_username;               // SMTP username
        $mail->Password   = $email_password;                        // SMTP password
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption, `ssl` also accepted
        $mail->SMTPSecure = 'ssl';         // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = 465;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom($email_username, 'Ctl Serve');
        $mail->addAddress($email_username, 'Ctl Serve');     // Add a recipient

        $sql = "SELECT * FROM referrals.track_links WHERE date(created) = '$date' ";
        $query = $conn->query($sql);
        
        $html = "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        $html .= "<tr>
        <th>Link</th>
        <th>Created</th>
        </tr>";

        while($result = $query->fetch_assoc()){

            $html .= "<tr>";
            $html .= "<td>{$result['link_text']}</td>";
            $html .= "<td>{$result['created']}</td>";
            $html .= "</tr>";

        }

        $html .= "</table>";

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = 'New Referral Click Detected '. $date;
        $mail->Body    = $html;
        //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if($mail->send()){
            //echo 'Message has been sent';
        }else{
            //echo 'Message could not be sent. Mailer Error: {$mail->ErrorInfo}';
        }

        //echo 'Message has been sent';
    } catch (Exception $e) {
        //echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }

    header('Location:' . $url);
    exit;
}

$conn->close();

echo json_encode(['status' => 'failure']);
exit;
?>