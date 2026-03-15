<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Subscription Verified</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            line-height: 1.6; 
            color: #333; 
            margin: 0; 
            padding: 0; 
            background-color: #f6f6f6;
        }
        .container { 
            max-width: 600px; 
            margin: 0 auto; 
            background-color: #ffffff;
        }
        .header { 
            background-color: #4CAF50; 
            color: white; 
            padding: 20px; 
            text-align: center; 
        }
        .content { 
            padding: 30px; 
        }
        .footer { 
            text-align: center; 
            padding: 20px; 
            font-size: 12px; 
            color: #777; 
            background-color: #f9f9f9;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Subscription Verified Successfully</h1>
        </div>
        <div class="content">
            <p>Hello {{ $tenant->name ?? 'Valued Customer' }},</p>
            <p>Your subscription has been successfully verified and activated. Your account is now fully active with all features available.</p>
            
            <p><strong>Subscription Details:</strong></p>
            <ul>
                <li>Status: Active</li>
                <li>Activation Date: {{ now()->format('F d, Y') }}</li>
                <li>Account ID: {{ $tenant->id }}</li>
            </ul>
            
            <p>If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
            
            <p>Thank you for choosing our service!</p>
            
            <p>Best regards,<br>Your Company Team</p>
        </div>
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
            <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
        </div>
    </div>
</body>
</html>