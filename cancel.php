<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled | TopUp</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; }
        .card { background: #fff; border-radius: 16px; padding: 40px; text-align: center; max-width: 420px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .icon { width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 2rem; background: #fef2f2; color: #ef4444; }
        h2 { color: #1e293b; margin-bottom: 8px; font-size: 1.3rem; }
        p { color: #64748b; margin-bottom: 20px; line-height: 1.5; font-size: 0.9rem; }
        .btn { display: inline-block; padding: 12px 28px; border-radius: 10px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="card">
        <div class="icon">
            <i class="fas fa-times"></i>
        </div>
        <h2>Payment Cancelled</h2>
        <p>Your transaction was not completed. No charges were made to your account. You can try again anytime.</p>
        <a href="index.php" class="btn btn-primary">Return to Store</a>
    </div>
</body>
</html>
