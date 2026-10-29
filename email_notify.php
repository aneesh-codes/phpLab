<!-- Write a server-side PHP script to send an email notification to a specified email address.
Accept the recipient email address, subject, and message through an HTML form and
send the email after validating the submitted information. -->

<?php
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $to = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $to = filter_var($to, FILTER_SANITIZE_EMAIL);
    $subject = strip_tags($subject);
    $message = strip_tags($message);

    if (empty($to) || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid recipient email address.";
    } elseif (empty($subject)) {
        $error = "Please enter a subject.";
    } elseif (empty($message)) {
        $error = "Please enter a message.";
    } else {
        $headers = [
            "From: no-reply@example.com",
            "Reply-To: no-reply@example.com",
            "MIME-Version: 1.0",
            "Content-type: text/plain; charset=UTF-8"
        ];

        if (mail($to, $subject, $message, implode("\r\n", $headers))) {
            $success = "Email sent successfully.";
        } else {
            $error = "Email could not be sent. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
        }
        form {
            max-width: 500px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            margin-bottom: 16px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 20px;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <h2>Send Email Notification</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label for="email">Recipient Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter recipient email" required>

        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" placeholder="Enter email subject" required>

        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="6" placeholder="Write your message" required></textarea>

        <button type="submit">Send Email</button>
    </form>
</body>
</html>