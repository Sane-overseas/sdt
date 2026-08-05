<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Registration Received</title>
</head>
<body style="margin:0;padding:0;background:#f3f6f9;font-family:Segoe UI,Tahoma,Geneva,Verdana,sans-serif;color:#142033;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:24px 12px;">
        <tr>
            <td align="center">
                <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5ebf2;">
                    <tr>
                        <td style="background:#0f2744;color:#ffffff;padding:20px 24px;text-align:center;">
                            <div style="font-size:18px;font-weight:700;">SOPL Trainer Registration</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 24px;">
                            <p style="margin:0 0 14px;font-size:15px;">Dear {{ $trainerName }},</p>
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.55;">
                                Your registration is done successfully.
                            </p>
                            <p style="margin:0 0 14px;font-size:15px;line-height:1.55;">
                                Please keep waiting for approval. Once your registration is approved by the admin,
                                login credentials will be sent to this email.
                            </p>
                            <p style="margin:0 0 6px;font-size:14px;color:#5b6b7c;">
                                Your temporary registration code:
                            </p>
                            <p style="margin:0 0 18px;font-size:16px;font-weight:700;color:#0f766e;">
                                {{ $instructorCode }}
                            </p>
                            <p style="margin:0;font-size:14px;color:#5b6b7c;line-height:1.5;">
                                Thank you,<br>
                                Sane Overseas Pvt. Ltd. (SOPL)
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
