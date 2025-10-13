<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Restablecer tu contraseña</h1>
        </div>
        <div class="content">
            <p>Hola,</p>
            <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta. Si tú no realizaste esta solicitud, puedes ignorar este correo.</p>
            <p>Para restablecer tu contraseña, haz clic en el siguiente enlace:</p>
            <p style="text-align: center;">
                <a href="{{ $resetLink }}" class="button">Restablecer contraseña</a>
            </p>
            <p>Este enlace expirará en 60 minutos.</p>
            <p>Si tienes problemas para hacer clic en el botón, copia y pega esta URL en tu navegador:</p>
            <p style="word-break: break-all;">{{ $resetLink }}</p>
        </div>
        <div class="footer">
            <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
            <p>&copy; {{ date('Y') }} Sakila Movies. Todos los derechos reservados.</p>
        </div>
    </div>
</body>
</html>