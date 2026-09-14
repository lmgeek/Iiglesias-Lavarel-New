<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Catedral Cristiana</title>
</head>
<body style="margin:0;padding:0;background:#f5f5f4;font-family:Arial,sans-serif;">
    <div style="max-width:480px;margin:0 auto;">
        <div style="text-align:center;padding:32px 0;">
            <h1 style="color:#1a1714;font-size:24px;margin:0;">Catedral Cristiana</h1>
            <p style="color:#78716c;font-size:14px;">Plataforma de discipulado</p>
        </div>
        <div style="background:#fafaf9;border-radius:12px;padding:32px;">
            <h2 style="color:#1a1714;font-size:18px;margin:0 0 12px;">¡Bienvenido!</h2>
            <p style="color:#57534e;font-size:14px;line-height:1.6;margin:0 0 16px;">
                Hola <strong>{{ $user->fullname }}</strong>, tu cuenta fue creada exitosamente en la Plataforma de Discipulado de la Catedral Cristiana.
            </p>
            @if (!empty($password))
                <p style="color:#57534e;font-size:14px;line-height:1.6;margin:0 0 8px;">
                    Tus datos de acceso al sitio son los siguientes:
                </p>
                <div style="background:#1c1917;border-radius:8px;padding:16px;color:#ffffff;font-size:14px;margin:0 0 16px;">
                    <div style="color:#a8a29e;font-size:12px;margin-bottom:4px;">Usuario</div>
                    <div style="font-weight:600;margin-bottom:12px;">{{ $user->email }}</div>
                    <div style="color:#a8a29e;font-size:12px;margin-bottom:4px;">Contraseña</div>
                    <div style="font-weight:600;">{{ $password }}</div>
                </div>
            @else
                <p style="color:#57534e;font-size:14px;line-height:1.6;margin:0 0 16px;">
                    Inicia sesión para comenzar a gestionar el discipulado.
                </p>
            @endif
            <div style="text-align:center;">
                <a href="{{ url('/login') }}" style="display:inline-block;background:#8b5cf6;color:#ffffff;text-decoration:none;padding:12px 32px;border-radius:8px;font-size:14px;font-weight:600;">
                    Iniciar sesión
                </a>
            </div>
            <p style="color:#a8a29e;font-size:12px;line-height:1.5;margin:24px 0 0;">
                No respondas a este correo. Fue generado automáticamente por la plataforma.
            </p>
        </div>
    </div>
</body>
</html>