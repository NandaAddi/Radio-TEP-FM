<?php
    /*
    name
    email
    message
    */
    // Only process POST reqeusts.
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get the form fields and remove whitespace.
        $name = strip_tags(trim($_POST["name"]));
        $name = str_replace(array("\r","\n"),array(" "," "),$name);
        $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
        $message = trim($_POST["message"]);

        

        // Set the recipient email address.
        // FIXME: Update this to your desired email address.
        $recipient = "nnvnxx.10@gmail.com";

        
        // Build the email content.
        $email_content = "Name $name\n";
        $email_content .= "Email \n$message\n";
        $email_content .= "Message \n$message\n";

        // Build the email headers.
        $email_headers = "From: $name <$email>";

        // Send the email.
        if (mail($recipient,  $email_content, $email_headers)) {
            // Set a 200 (okay) response code.
            http_response_code(200);
            echo "Thank You! Your message has been sent.";
        } else {
            // Set a 500 (internal server error) response code.
            http_response_code(500);
            echo "Oops! Something went wrong ande we couldn't send your message.";
        }

                // TELEGRAM
        $telegram_token = '8152564117:AAEmot6-fv_7ZfSefr-fZnxrufGZ1onrT2E';
        $telegram_chat_id = '5301302742';
        $telegram_message = "📩 Pesan Baru dari Form Kontak\n\n👤 Nama: $name\n📧 Email: $email\n💬 Pesan:\n$message";

        // Kirim ke Telegram via API
        $sendToTelegram = file_get_contents("https://api.telegram.org/bot$telegram_token/sendMessage?chat_id=$telegram_chat_id&text=" . urlencode($telegram_message));


    } else {
        // Not a POST request, set a 403 (forbidden) response code.
        http_response_code(403);
        echo "There was a problem with your submission, please try again.";
    }

?>