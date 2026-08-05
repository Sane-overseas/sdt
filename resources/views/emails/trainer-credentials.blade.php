<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Trainer Credentials</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222; line-height: 1.5;">
    <p>Dear {{ $trainerName }},</p>

    <p>Your trainer registration has been approved. You can now log in with the credentials below:</p>

    <table cellpadding="8" cellspacing="0" style="border-collapse: collapse; margin: 16px 0;">
        <tr>
            <td style="font-weight: bold;">Login Email</td>
            <td>{{ $email }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Trainer ID / Code</td>
            <td>{{ $trainerCode }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Password</td>
            <td>{{ $plainPassword }}</td>
        </tr>
    </table>

    <p>
        Login here:
        <a href="{{ $loginUrl }}">{{ $loginUrl }}</a>
    </p>

    @if(!empty($idCardPath))
        <p>Your official <strong>SOPL Identity Card</strong> is attached to this email (PNG). Please download and keep it safe.</p>
    @endif

    <p>Please change your password after first login if needed. Do not share these credentials.</p>

    <p>Regards,<br>SOPL Team</p>
</body>
</html>
