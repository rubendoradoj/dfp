<?php
require_once(__DIR__ . '/../../config.php');

// Check if the user is logged in
require_login();

global $USER;

$jsonData = file_get_contents('php://input');
// Decode the JSON data into a PHP associative array
$data = json_decode($jsonData, true);

$number = "";
$mensaje = "";
$cuestionario = "";
$textoPregunta = "";
$duda = false;
$user = null;

// Check if decoding was successful
if ($data !== null) {
    // Access the data and perform operations
    $number = $data['number'];
    $mensaje = $data['mensaje'];
    $textoPregunta = $data['textoPregunta'];
    $cuestionario = $data['cuestionario'];
    $duda = $data['duda'];
    // Perform further processing or respond to the request
} else {
    // JSON decoding failed
    http_response_code(400); // Bad Request
    echo "Invalid JSON data";
}

if (!$duda) {
    // Compose the email subject and message
    $subject = "Envío de Impugnaciones";
    $message = "Cuestionario <b>" . $cuestionario . "</b>\n\n";
    $message .= "El usuario " . $USER->firstname . " " . $USER->lastname . " (" . $USER->email . ") ha IMPUGNADO la pregunta: <b>" . $number . " - " . $textoPregunta . "</b>\n\n";
    $message .= "El mensaje proporcionado fue: <i>" . $mensaje . "</i>\n\n";

    $user = get_complete_user_data('id', 9);
} else {
    $subject = "Envío de Dudas";
    $message = "Cuestionario <b>" . $cuestionario . "</b>\n\n";
    $message .= "El usuario " . $USER->firstname . " " . $USER->lastname . " (" . $USER->email . ") ha enviado una DUDA relacionada a la pregunta: <b>" . $number . " - " . $textoPregunta . "</b>\n\n";
    $message .= "El mensaje proporcionado fue: <i>" . $mensaje . "</i>\n\n";

    $user = get_complete_user_data('id', 10);
}

    //Firma del correo
    $message .= "DFPol Aula virtual";

    //Descomentar lo siguiente Solo para pruebas
    //$user = get_complete_user_data('id', 7);

    // Send the email using email_to_user() function
    email_to_user(
        $user,
        $USER,
        $subject,
        $message,
        '',
        '',
        '',
        false
    );

    // Redirect back to the page with a success message
    //redirect(new moodle_url('/path/to/your/page.php'), 'Email sent successfully', 3);
    echo "Message sent to: " . $user->email;
