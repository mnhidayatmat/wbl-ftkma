<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Your Password</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .email-header {
            background: linear-gradient(135deg, #003A6C 0%, #0084C5 100%);
            padding: 30px 20px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-body {
            padding: 40px 30px;
        }
        .email-body h2 {
            color: #003A6C;
            margin-top: 0;
            font-size: 20px;
            font-weight: 600;
        }
        .email-body p {
            color: #666;
            margin: 15px 0;
            font-size: 16px;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            padding: 14px 32px;
            background-color: #00A86B;
            color: #ffffff;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #008F5C;
        }
        .email-footer {
            background-color: #f5f7fa;
            padding: 20px 30px;
            text-align: center;
            font-size: 14px;
            color: #999;
        }
        .email-footer p {
            margin: 5px 0;
        }
        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 30px 0;
        }
        .alternative-link {
            background-color: #f9fafb;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .alternative-link code {
            background-color: #e5e7eb;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
            word-break: break-all;
        }
        .warning-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .warning-box p {
            margin: 0;
            color: #92400e;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div style="padding: 20px;">
        <div class="email-container">
            <!-- Header -->
            <div class="email-header">
                <h1>WBL Management System</h1>
            </div>

            <!-- Body -->
            <div class="email-body">
                <h2>Reset Your Password</h2>
                
                <p>Hello,</p>
                
                <p>You are receiving this email because we received a password reset request for your account.</p>

                <div class="button-container">
                    <a href="{{ $url }}" class="button">Reset Password</a>
                </div>

                <div class="divider"></div>

                <p>This password reset link will expire in {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60) }} minutes.</p>

                <div class="warning-box">
                    <p><strong>⚠️ Security Notice:</strong> If you did not request a password reset, please ignore this email. Your password will remain unchanged.</p>
                </div>

                <div class="alternative-link">
                    <p style="margin: 0 0 10px 0; font-weight: 600;">If the button doesn't work, copy and paste this link into your browser:</p>
                    <code>{{ $url }}</code>
                </div>
            </div>

            <!-- Footer -->
            <div class="email-footer">
                <p><strong>WBL Management System</strong></p>
                <p>Faculty of Mechanical and Automotive Engineering Technology</p>
                <p>Universiti Malaysia Pahang Al-Sultan Abdullah (UMPSA)</p>
                <p style="margin-top: 15px; font-size: 12px; color: #999;">
                    This is an automated email. Please do not reply to this message.
                </p>
            </div>
        </div>
    </div>
</body>
</html>

