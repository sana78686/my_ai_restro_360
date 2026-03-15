<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Verification Code</title>
    <style>
        /* Reset styles for email compatibility */
        body, table, td, a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table, td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
            border-collapse: collapse;
        }
        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            background-color: #3490dc;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 30px;
            background-color: #f9f9f9;
        }
        .footer {
            padding: 20px;
            text-align: center;
            background-color: #f1f1f1;
            font-size: 12px;
            color: #666;
        }
        .code-box {
            background-color: #ffffff;
            border: 2px dashed #3490dc;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            margin: 20px 0;
            font-size: 24px;
            font-weight: bold;
            color: #3490dc;
        }
        .button {
            background-color: #3490dc;
            border-radius: 4px;
            color: white;
            display: inline-block;
            font-size: 16px;
            font-weight: bold;
            line-height: 40px;
            text-align: center;
            text-decoration: none;
            width: 200px;
            margin-top: 20px;
        }
        @media screen and (max-width: 600px) {
            .container {
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f5f5f5;">
    <center>
        <div class="container">
            <!-- Header -->
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #3490dc;">
                <tr>
                    <td align="center" style="padding: 20px;">
                        <h1 style="color: white; margin: 0;">Subscription Verification</h1>
                    </td>
                </tr>
            </table>

            <!-- Content -->
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f9f9f9;">
                <tr>
                    <td align="center" style="padding: 30px 20px;">
                        <h2 style="color: #333; margin-top: 0;">Hello!</h2>
                        <p style="color: #666; line-height: 1.6; font-size: 16px;">
                            You've requested to subscribe to our service. Please use the verification code below to complete your subscription process.
                        </p>
                        
                        <!-- Verification Code -->
                        <div class="code-box">
                            {{ $code }}
                        </div>
                        
                        <p style="color: #666; line-height: 1.6; font-size: 16px;">
                            This code will expire in 10 minutes. If you didn't request this subscription, please ignore this email.
                        </p>
                        
                        <a href="{{ url('/subscription/verify') }}" class="button" style="color: white; text-decoration: none;">
                            Verify Subscription
                        </a>
                    </td>
                </tr>
            </table>

            <!-- Footer -->
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f1f1f1;">
                <tr>
                    <td align="center" style="padding: 20px; font-size: 12px; color: #666;">
                        <p style="margin: 0 0 10px 0;">
                            &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                        </p>
                        <p style="margin: 0;">
                            If you have any questions, please contact our support team.
                        </p>
                    </td>
                </tr>
            </table>
        </div>
    </center>
</body>
</html>