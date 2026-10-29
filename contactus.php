<!-- Write a PHP program to design and implement a functional Contact Us form containing
fields for name, email address, phone number, subject, and message, validate all submitted
data on the server side, and, after successful validation, use PHP's mail() function to send
the submitted message to the your email address, including the sender's name, email, phone
number, subject, and message in the email, and display appropriate success or error
messages depending on whether the email is sent successfully. -->

<?php
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    $name = strip_tags($name);
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $phone = preg_replace('/[^0-9+\-()\s]/', '', $phone);
    $subject = strip_tags($subject);
    $message = strip_tags($message);

    if (empty($name)) {
        $error = "Name is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (empty($phone) || !preg_match('/^[0-9+\-()\s]{7,20}$/', $phone)) {
        $error = "Please enter a valid phone number.";
    } elseif (empty($subject)) {
        $error = "Subject is required.";
    } elseif (empty($message)) {
        $error = "Message is required.";
    } else {
        $to = "your_email@example.com";
        $emailBody = "Name: $name\n" .
                    "Email: $email\n" .
                    "Phone: $phone\n" .
                    "Subject: $subject\n\n" .
                    "Message:\n$message";

        $headers = [
            "From: $name <$email>",
            "Reply-To: $email",
            "MIME-Version: 1.0",
            "Content-type: text/plain; charset=UTF-8"
        ];

        if (mail($to, $subject, $emailBody, implode("\r\n", $headers))) {
            $success = "Your message has been sent successfully!";
        } else {
            $error = "Failed to send the email. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
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
    <h2>Contact Us</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" placeholder="Enter your name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>

        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required>

        <label for="subject">Subject:</label>
        <input type="text" id="subject" name="subject" placeholder="Enter subject" required>

        <label for="message">Message:</label>
        <textarea id="message" name="message" rows="6" placeholder="Enter your message" required></textarea>

        <button type="submit">Send Message</button>
    </form>
</body>
</html>