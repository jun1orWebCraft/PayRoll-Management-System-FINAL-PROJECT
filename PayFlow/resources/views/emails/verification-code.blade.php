<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PayFlow Verification Code</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f8f9fa; padding: 20px;">
    <div style="max-width: 500px; background: #ffffff; margin: 0 auto; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #007bff;">PayFlow Password Reset</h2>
        <p>Hello,</p>
        <p>Your password reset verification code is:</p>

        <div style="font-size: 32px; font-weight: bold; text-align: center; margin: 20px 0; color: #333;">
            {{ $code }}
        </div>

        <p>Please enter this code on the website to reset your password.</p>
        <p>If you didn’t request this, please ignore this message.</p>

        <p style="margin-top: 30px; color: #6c757d;">— The PayFlow Team</p>
    </div>
</body>
</html>
