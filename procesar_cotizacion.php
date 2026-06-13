<?php
// Comprobar si el formulario fue enviado
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
    $asunto = "Nueva Solicitud de Cotización - Web";

    // Recoger y limpiar los datos del formulario (asegúrate de que los 'name' coincidan)
    $nombres = htmlspecialchars($_POST['Nombres']);
    $apellidos = htmlspecialchars($_POST['Apellidos']);
    $correo = htmlspecialchars($_POST['Correo']);
    $tipoPersona = htmlspecialchars($_POST['Tipo_Persona']);
    $ciudad = htmlspecialchars($_POST['Ciudad']);
    $solucion = htmlspecialchars($_POST['Solucion']);
    $cantidad = htmlspecialchars($_POST['Cantidad']);
    $comentarios = htmlspecialchars($_POST['Comentarios']);

    // Construir el cuerpo del mensaje
    $mensaje = "Has recibido una nueva cotización desde la web:\n\n";
    $mensaje .= "Nombres: $nombres $apellidos\n";
    $mensaje .= "Correo: $correo\n";
    $mensaje .= "Tipo de Persona: $tipoPersona\n";
    $mensaje .= "Ciudad: $ciudad\n\n";
    $mensaje .= "--- Detalles de Cotización ---\n";
    $mensaje .= "Producto: $solucion\n";
    $mensaje .= "Cantidad: $cantidad\n";
    $mensaje .= "Comentarios: $comentarios\n";

    // Cabeceras del correo (Importante: el 'From' debe ser un correo de tu dominio en Hostinger para evitar que llegue a SPAM)
    $headers = "From: web@agroorganico.com.ec\r\n";
    $headers .= "Reply-To: $correo\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Enviar el correo
    if (mail($destinatario, $asunto, $mensaje, $headers)) {
        // Redirigir de vuelta con un mensaje de éxito (puedes crear una página de gracias)
        echo "<script>alert('¡Cotización enviada con éxito!'); window.location.href = 'cotizacion.html';</script>";
    } else {
        echo "<script>alert('Hubo un error al enviar el mensaje. Inténtalo de nuevo.'); window.history.back();</script>";
    }
} else {
    // Si alguien intenta acceder al archivo directamente
    header("Location: index.html");
}
?>