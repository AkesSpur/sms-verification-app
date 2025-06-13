<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Sale Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f7f9fc;
            color: #333333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            margin-bottom: 20px;
        }

        .notification-badge {
            display: inline-block;
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        h1 {
            color: #1e293b;
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .subtitle {
            color: #64748b;
            font-size: 16px;
            margin-bottom: 30px;
        }

        p {
            line-height: 1.6;
            margin-bottom: 20px;
            color: #334155;
        }

        .product-container {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid #4f46e5;
        }

        .product-detail {
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .product-detail:last-child {
            border-bottom: none;
        }

        .label {
            font-weight: 600;
            color: #475569;
            display: inline-block;
            width: 120px;
        }

        .total-amount {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 15px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            text-align: center;
            margin: 25px 0;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .digital-icon {
            text-align: center;
            margin: 20px 0;
            font-size: 36px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">🎁</div>
            <div class="notification-badge">New Sale</div>
            <h1>Digital Product Sale!</h1>
            <p class="subtitle">Your store just made a new sale</p>
        </div>

        <p>Dear {{ $businessOwnerName }},</p>

        <p>Great news! You've just sold digital products from your store. Here are the details:</p>

        @foreach ($products as $product)
        <div class="product-container">
            <div class="digital-icon">💳</div>
            <div class="product-detail">
                <span class="label">Order ID:</span> {{ $product['order Id'] }}
            </div>
            <div class="product-detail">
                <span class="label">Invoice ID:</span> {{ $product['invoice Id'] }}
            </div>
            <div class="product-detail">
                <span class="label">Category:</span> {{ $product['category'] }}
            </div>
            <div class="product-detail">
                <span class="label">Product:</span> {{ $product['name'] }}
            </div>
            <div class="product-detail">
                <span class="label">Quantity:</span> {{ $product['quantity'] }}
            </div>
            <div class="product-detail">
                <span class="label">Price:</span> {{$settings->currency_icon}}{{ number_format($product['price'], 2) }}
            </div>
        </div>
        @endforeach

        <div class="total-amount">
            Total Amount: {{$settings->currency_icon}}{{ number_format($totalAmount, 2) }}
        </div>

        <p>Thank you for using our platform to sell your digital products. We're excited to see your business grow!</p>

        <p>Best regards,<br>{{ $businessOwnerName }}</p>
    </div>

    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ $businessOwnerName }} | Digital Products & Gift Cards</p>
    </div>
</body>

</html>
