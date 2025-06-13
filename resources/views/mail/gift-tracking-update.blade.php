<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gift Tracking Code Update</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #f9f9f9;
        }
        .header {
            background: #007bff;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
        }
        .content {
            margin-top: 20px;
        }
        .footer {
            margin-top: 20px;
            padding: 10px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gift Tracking Code Update</h1>
        </div>
        <div class="content">
            <p>Dear {{ $user }},</p>
            <p>We wanted to let you know that the tracking code for your gift has been updated. Please find the new tracking information below:</p>

            <p><strong>Order Number:</strong> {{ $order }}</p>
            <p><strong>Tracking info:</strong> {{ strip_tags($trackingInfo) }}</p>

            <p>If you have any questions or need further assistance, please do not hesitate to contact our support team.</p>

            <p>Thank you for shopping with us!</p>
            <p>Best regards,</p>
            <p>The {{ $settings->site_name }} Team</p>
        </div>
        <div class="footer">
            <p>© {{ date('Y') }} {{$settings->site_name}}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
