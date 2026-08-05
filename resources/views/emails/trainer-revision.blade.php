<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration Correction Required</title>
</head>
<body style="font-family: Arial, sans-serif; color: #222; line-height: 1.5;">
    <p>Dear {{ $trainerName }},</p>

    <p>Your trainer registration needs correction. Please review the admin remarks below and update your details / documents.</p>

    <p style="background:#fff3cd;border:1px solid #ffc107;padding:12px;border-radius:6px;">
        <strong>Admin Remarks:</strong><br>
        {{ $remarks }}
    </p>

    <p>
        Use this link to edit and resubmit your form:<br>
        <a href="{{ $editUrl }}">{{ $editUrl }}</a>
    </p>

    <p>Until you correct and resubmit, your application will remain on hold.</p>

    <p>Regards,<br>SOPL Team</p>
</body>
</html>
