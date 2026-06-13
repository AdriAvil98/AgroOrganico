<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- VERIFICACIÓN DE RECAPTCHA ---
    $recaptcha_secret = "6LdKuRotAAAAABNvF2ZzxIMN1RlnSk9IVLxTuI1G"; // Reemplaza esto con tu clave secreta de Google
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Hacer la consulta a los servidores de Google
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response
    );
    $options = array(
        'http' => array (
            'method' => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $verify = file_get_contents($url, false, $context);
    $captcha_success = json_decode($verify);

    // Si la validación falla, detenemos el envío
    if ($captcha_success->success == false) {
        echo "<script>alert('Por favor, completa la casilla de seguridad reCAPTCHA.'); window.history.back();</script>";
        exit; // Detiene la ejecución del resto del código
    }
    // --- FIN VERIFICACIÓN RECAPTCHA ---

    // A partir de aquí sigue el código que ya tienes (recoger datos, configurar correo, enviar, etc.)
    // Configuración del correo receptor
    $destinatario = "agro_organico@live.com"; 
    $asunto = "Nueva Postulación de Empleo - Web";

    // Recoger los datos de texto
    $nombres = htmlspecialchars($_POST['Nombres']);
    $apellidos = htmlspecialchars($_POST['Apellidos']);
    $correo = htmlspecialchars($_POST['Correo']);
    $telefono = htmlspecialchars($_POST['Telefono']);
    $ciudad = htmlspecialchars($_POST['Ciudad']);
    $area = htmlspecialchars($_POST['Area']);
    $presentacion = htmlspecialchars($_POST['Presentacion']);

    // Construir el mensaje de texto
    $mensaje = "Has recibido una nueva postulación de empleo:\n\n";
    $mensaje .= "Nombres: $nombres $apellidos\n";
    $mensaje .= "Correo: $correo\n";
    $mensaje .= "Teléfono: $telefono\n";
    $mensaje .= "Ciudad de Residencia: $ciudad\n";
    $mensaje .= "Área de Interés: $area\n\n";
    $mensaje .= "Presentación:\n$presentacion\n";

    // Generar un límite (boundary) para separar el texto del archivo adjunto
    $boundary = md5(time());

    // Cabeceras del correo (Importante: Usa el correo configurado en tu Hostinger)
    $headers = "From: web@agroorganico.com.ec\r\n";
    $headers .= "Reply-To: $correo\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

    // Cuerpo del correo (Texto)
    $body = "--$boundary\r\n";
    $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= $mensaje . "\r\n";

    // Procesar y adjuntar el archivo (CV)
    if (isset($_FILES['Curriculum_Vitae']) && $_FILES['Curriculum_Vitae']['error'] == UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['Curriculum_Vitae']['tmp_name'];
        $file_name = $_FILES['Curriculum_Vitae']['name'];
        $file_type = $_FILES['Curriculum_Vitae']['type'];
        $file_size = $_FILES['Curriculum_Vitae']['size'];

        // Leer el archivo y codificarlo en Base64
        $handle = fopen($file_tmp, "r");
        $content = fread($handle, $file_size);
        fclose($handle);
        $encoded_content = chunk_split(base64_encode($content));

        // Añadir el archivo al cuerpo del correo
        $body .= "--$boundary\r\n";
        $body .= "Content-Type: $file_type; name=\"$file_name\"\r\n";
        $body .= "Content-Disposition: attachment; filename=\"$file_name\"\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= $encoded_content . "\r\n";
    }

    // Cerrar el límite del correo
    $body .= "--$boundary--";

    // Enviar el correo
    if (mail($destinatario, $asunto, $body, $headers)) {
        echo "<script>alert('¡Postulación enviada con éxito! Hemos recibido tu información y currículum.'); window.location.href = 'trabaja-con-nosotros.html';</script>";
    } else {
        echo "<script>alert('Hubo un error al enviar la postulación. Inténtalo de nuevo.'); window.history.back();</script>";
    }
} else {
    header("Location: index.html");
}
?>