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
    $asunto = "Nueva Solicitud de Distribuidor - Web";

    // Recoger y limpiar los datos del formulario
    $nombreNegocio = htmlspecialchars($_POST['Nombre_Negocio']);
    $ruc = htmlspecialchars($_POST['RUC']);
    $correo = htmlspecialchars($_POST['Correo']);
    $telefono = htmlspecialchars($_POST['Telefono']);
    $ciudad = htmlspecialchars($_POST['Ciudad']);
    $direccion = htmlspecialchars($_POST['Direccion']);
    $lineaInteres = htmlspecialchars($_POST['Linea_Interes']);
    $comentarios = htmlspecialchars($_POST['Comentarios']);
    
    // Verificar si el checkbox de llamada urgente fue marcado
    $urgente = isset($_POST['Urgente']) ? "Sí, requiere llamada urgente" : "No";

    // Construir el cuerpo del mensaje
    $mensaje = "Has recibido una nueva solicitud para ser distribuidor:\n\n";
    $mensaje .= "--- Datos del Negocio ---\n";
    $mensaje .= "Nombre / Razón Social: $nombreNegocio\n";
    $mensaje .= "RUC: $ruc\n";
    $mensaje .= "Correo: $correo\n";
    $mensaje .= "Teléfono: $telefono\n";
    $mensaje .= "Ciudad: $ciudad\n";
    $mensaje .= "Dirección: $direccion\n\n";
    $mensaje .= "--- Intereses y Comentarios ---\n";
    $mensaje .= "Línea de Interés: $lineaInteres\n";
    $mensaje .= "Llamada Urgente: $urgente\n";
    $mensaje .= "Comentarios:\n$comentarios\n";

    // Cabeceras del correo (Usa el correo creado en Hostinger)
    $headers = "From: web@agroorganico.com.ec\r\n";
    $headers .= "Reply-To: $correo\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Enviar el correo
    if (mail($destinatario, $asunto, $mensaje, $headers)) {
        echo "<script>alert('¡Solicitud enviada con éxito! Nuestro equipo comercial la evaluará pronto.'); window.location.href = 'ser-distribuidor.html';</script>";
    } else {
        echo "<script>alert('Hubo un error al enviar la solicitud. Inténtalo de nuevo.'); window.history.back();</script>";
    }
} else {
    // Redirigir si se accede al archivo directamente
    header("Location: index.html");
}
?>