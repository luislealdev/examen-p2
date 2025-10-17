<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - {{ config('app.name') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }
        .header .icon {
            font-size: 48px;
            margin-bottom: 10px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #555;
            margin-bottom: 30px;
            line-height: 1.8;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .reset-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            transition: all 0.3s ease;
        }
        .reset-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }
        .alternative-link {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #6c757d;
        }
        .alternative-link strong {
            color: #495057;
        }
        .security-notice {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .security-notice .title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .footer {
            background-color: #2c3e50;
            color: #bdc3c7;
            padding: 20px 30px;
            font-size: 14px;
        }
        .footer-title {
            color: white;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .expiry-info {
            background-color: #e7f3ff;
            border: 1px solid #b3d4fc;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #004085;
        }
        .expiry-info .title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
            }
            .content {
                padding: 20px !important;
            }
            .header {
                padding: 20px !important;
            }
            .reset-button {
                padding: 12px 25px !important;
                font-size: 14px !important;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="icon">🔐</div>
            <h1>{{ config('app.name') }}</h1>
            <p style="margin: 10px 0 0 0; opacity: 0.9;">Restablecimiento de Contraseña</p>
        </div>

        <!-- Content -->
        <div class="content">
            <div class="greeting">
                ¡Hola {{ $user->name ?? 'Usuario' }}!
            </div>

            <div class="message">
                Recibimos una solicitud para restablecer la contraseña de tu cuenta asociada con el correo electrónico <strong>{{ $email }}</strong>.
            </div>

            <div class="message">
                Si solicitaste este cambio, haz clic en el botón de abajo para crear una nueva contraseña:
            </div>

            <!-- Reset Button -->
            <div class="button-container">
                <a href="{{ $url }}" class="reset-button">
                    🔑 Restablecer Mi Contraseña
                </a>
            </div>

            <!-- Alternative Link -->
            <div class="alternative-link">
                <strong>¿No puedes hacer clic en el botón?</strong><br>
                Copia y pega el siguiente enlace en tu navegador:<br>
                <a href="{{ $url }}" style="color: #667eea; word-break: break-all;">{{ $url }}</a>
            </div>

            <!-- Expiry Info -->
            <div class="expiry-info">
                <div class="title">⏰ Tiempo límite</div>
                Este enlace es válido por <strong>24 horas</strong> a partir del momento en que se envió este correo. Después de ese tiempo, necesitarás solicitar un nuevo enlace.
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <div class="title">🛡️ Aviso de Seguridad</div>
                Si no solicitaste este restablecimiento de contraseña, puedes ignorar este correo de forma segura. Tu contraseña actual permanecerá sin cambios.
            </div>

            <div class="message">
                Si tienes problemas para restablecer tu contraseña o no solicitaste este cambio, contacta inmediatamente al administrador del sistema.
            </div>

            <div class="message" style="margin-top: 30px; color: #6c757d; font-size: 14px;">
                Este correo fue enviado el {{ now()->format('d/m/Y') }} a las {{ now()->format('H:i') }} (hora del servidor).
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div class="footer-title">{{ config('app.name') }}</div>
            <p style="margin: 0; font-size: 13px;">
                Este es un correo automático, por favor no respondas a esta dirección.
            </p>
            <p style="margin: 5px 0 0 0; font-size: 13px;">
                © {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
            </p>
        </div>
    </div>
</body>
</html>