<?php

class MailController
{
    public function send()
    {
        if (empty($_POST['token']) || $_POST['token'] !== 'FsWga4&@f6aw') {
            http_response_code(400);
            echo '<span class="notice">Error!</span>';
            exit;
        }

        $name = $_POST['name'] ?? '';
        $from = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $subject = stripslashes(nl2br($_POST['subject'] ?? ''));
        $message = stripslashes(nl2br($_POST['message'] ?? ''));

        $headers = "From: Form Contact <$from>\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1";

        ob_start();
        echo "Hi imransdesign!<br /><br />";
        echo ucfirst($name)." has sent you a message via contact form on your website!";
        echo "<br /><br />";
        echo "Name: ".ucfirst($name)."<br />";
        echo "Email: ".$from."<br />";
        echo "Phone: ".$phone."<br />";
        echo "Subject: ".$subject."<br />";
        echo "Message: <br /><br />";
        echo $message;
        echo "<br /><br />";
        echo "============================================================";

        $body = ob_get_clean();
        $to = 'support@fruitkha.com';

        $success = mail($to, $subject, $body, $headers, "-t -i -f $from");

        if ($success) {
            echo '<div class="success"><i class="fas fa-check-circle"></i><h3>Thank You!</h3>Your message has been sent successfully.</div>';
        } else {
            http_response_code(500);
            echo '<div>Your message sending failed!</div>';
        }
    }
}
