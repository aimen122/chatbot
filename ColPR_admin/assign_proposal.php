<?php
session_start();
include 'config.php';

// Include PHPMailer files manually
require_once 'PHPMailer-master/src/PHPMailer.php';
require_once 'PHPMailer-master/src/Exception.php';
require_once 'PHPMailer-master/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['admin_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $proposal_id = intval($_POST['proposal_id']);
    $member_id = intval($_POST['member_id']);

    // Initialize variables
    $proposal = null;
    $member = null;
    $error = '';

    // Fetch proposal details
    $stmt1 = $conn->prepare("SELECT * FROM proposals WHERE id = ?");
    $stmt1->bind_param("i", $proposal_id);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    $proposal = $result1->fetch_assoc();
    $stmt1->close();

    // Fetch member details
    $stmt2 = $conn->prepare("SELECT name, email FROM marketing_members WHERE id = ?");
    $stmt2->bind_param("i", $member_id);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    $member = $result2->fetch_assoc();
    $stmt2->close();

    if ($proposal && $member) {
        // Update database - Change status to 'Sent' instead of 'Assigned'
        $stmt3 = $conn->prepare("UPDATE proposals SET status = 'Sent', assigned_member_id = ? WHERE id = ?");
        $stmt3->bind_param("ii", $member_id, $proposal_id);
        
        if ($stmt3->execute()) {
            // PHPMailer setup
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'aimenatif080@gmail.com';
                $mail->Password = 'lznf xryn udhp hrpy';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->SMTPDebug = 0;

                // Recipients
                $mail->setFrom('aimenatif080@gmail.com', 'ColPR Admin');
                $mail->addAddress($member['email'], $member['name']);
                $mail->addReplyTo('aimenatif080@gmail.com', 'ColPR Admin');
                
                // Add logo as embedded image
                $logo_path = __DIR__ . '/colpr-logo.png';
                if (file_exists($logo_path)) {
                    $mail->addEmbeddedImage($logo_path, 'company_logo');
                }

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'New Proposal Assigned - ID ' . $proposal['id'];
                
                // HTML email content
                $message = "
                <!DOCTYPE html>
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                        .content { background: #f9f9f9; padding: 20px; border-radius: 0 0 10px 10px; }
                        .proposal-details { background: white; padding: 15px; border-radius: 5px; margin: 10px 0; border-left: 4px solid #667eea; }
                        .footer { text-align: center; margin-top: 20px; padding: 10px; color: #666; font-size: 12px; }
                    </style>
                </head>
                <body>
                    <div class='container'>
                        <div class='header'>
                            <div style='text-align: center; margin-bottom: 15px;'>
                                <img src='cid:company_logo' alt='ColPR Logo' style='max-height: 60px; max-width: 200px; object-fit: contain;'>
                            </div>
                            <h2 style='margin: 10px 0 5px 0;'>ColPR Admin</h2>
                            <p style='margin: 0 0 10px 0; font-size: 14px; opacity: 0.9;'>ColPR Software</p>
                            <h3 style='margin: 15px 0 5px 0; font-size: 18px;'>New Proposal Assigned</h3>
                            <p style='margin: 0; font-size: 13px; opacity: 0.9;'>Proposal Management System</p>
                        </div>
                        <div class='content'>
                            <p>Dear <strong>{$member['name']}</strong>,</p>
                            <p>You have been assigned a new proposal. Please find the details below:</p>
                            
                            <div class='proposal-details'>
                                <h3>Proposal Details</h3>
                                <p><strong>Proposal ID:</strong> {$proposal['id']}</p>
                                <p><strong>Type:</strong> {$proposal['proposal_type']}</p>
                                <p><strong>User ID:</strong> {$proposal['user_id']}</p>
                                <p><strong>Status:</strong> Sent</p>
                                <p><strong>Estimated Price:</strong> \${$proposal['estimated_price']}</p>
                                <p><strong>Timeline:</strong> {$proposal['estimated_timeline']} days</p>
                                <p><strong>Requirements:</strong><br>" . nl2br(htmlspecialchars($proposal['requirements'])) . "</p>
                            </div>";

                // Add rejection reason if available
                if (!empty($proposal['rejection_reason'])) {
                    $message .= "<div class='proposal-details' style='border-left-color: #ef4444;'>
                                <p><strong>Rejection Reason:</strong><br>" . nl2br(htmlspecialchars($proposal['rejection_reason'])) . "</p>
                             </div>";
                }

                // Add escalation reason if available
                if (!empty($proposal['escalation_reason'])) {
                    $message .= "<div class='proposal-details' style='border-left-color: #f59e0b;'>
                                <p><strong>Escalation Reason:</strong><br>" . nl2br(htmlspecialchars($proposal['escalation_reason'])) . "</p>
                             </div>";
                }

                // Add full proposal content if available
                if (!empty($proposal['proposal_content'])) {
                    $message .= "<div class='proposal-details'>
                                <p><strong>Full Proposal Content:</strong><br>" . nl2br(htmlspecialchars($proposal['proposal_content'])) . "</p>
                             </div>";
                }

                $message .= "
                            <p>Please review this proposal and contact the client to proceed with the next steps.</p>
                            <p>You can access the full details through the Admin Dashboard.</p>
                            
                            <p>Best regards,<br>
                            <strong>Admin Team</strong></p>
                        </div>
                        <div class='footer'>
                            <p>This is an automated message from Proposal Management System.</p>
                        </div>
                    </div>
                </body>
                </html>";

                $mail->Body = $message;

                // Alternative plain text version
                $mail->AltBody = "Dear {$member['name']},

You have been assigned a new proposal.

Proposal ID: {$proposal['id']}
Type: {$proposal['proposal_type']}
User ID: {$proposal['user_id']}
Status: Sent
Requirements: {$proposal['requirements']}
Estimated Price: \${$proposal['estimated_price']}
Estimated Timeline: {$proposal['estimated_timeline']} days
" . ($proposal['rejection_reason'] ? "Rejection Reason: {$proposal['rejection_reason']}\n" : "") .
($proposal['escalation_reason'] ? "Escalation Reason: {$proposal['escalation_reason']}\n" : "") .

"Please review this proposal and contact the client to proceed.

Best regards,
Admin Team";

                // Send email
                $mailSent = $mail->send();
                
                // Generate success message
                $emailStatus = $mailSent ? "and notification email sent successfully" : "but email notification failed";
                $success_message = "✅ Proposal #{$proposal_id} has been successfully assigned to {$member['name']} and status updated to Sent {$emailStatus}.";
                
                // Clear any previous output
                if (ob_get_length()) ob_clean();
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'message' => $success_message,
                    'details' => [
                        'proposal_id' => $proposal_id,
                        'assigned_to' => $member['name'],
                        'email' => $member['email'],
                        'status' => 'Sent', // Updated to 'Sent'
                        'email_sent' => $mailSent
                    ]
                ]);
                
            } catch (Exception $e) {
                // Email failed but assignment was successful
                $success_message = "✅ Proposal #{$proposal_id} has been assigned to {$member['name']} and status updated to Sent, but email notification failed: " . $e->getMessage();
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'message' => $success_message,
                    'details' => [
                        'proposal_id' => $proposal_id,
                        'assigned_to' => $member['name'],
                        'email' => $member['email'],
                        'status' => 'Sent', // Updated to 'Sent'
                        'email_sent' => false,
                        'email_error' => $e->getMessage()
                    ]
                ]);
            }
            
            $stmt3->close();
            
        } else {
            $error = "Database update failed: " . $conn->error;
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false, 
                'message' => $error
            ]);
            exit();
        }
        
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => 'Proposal or member not found.'
        ]);
        exit();
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request method.'
    ]);
    exit();
}

$conn->close();
?>