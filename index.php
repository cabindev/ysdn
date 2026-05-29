<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YSDN Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/ysdn/theme/css/bootstrap.css">
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
        }

        .hero {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #f58220 0%, #6c757d 100%);
        }

        .hero-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            background: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .hero-content h1 {
            font-size: 3rem;
            font-weight: bold;
            margin: 0;
            color: #333;
        }

        .hero-content h5 {
            font-size: 1.5rem;
            font-weight: bold;
            color: #f97316;
            margin: 10px 0 20px;
        }

        .hero-content p {
            font-size: 1rem;
            color: #666;
            margin: 0 0 30px;
        }

        .card {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-top: 20px;
        }

        .card-body {
            padding: 20px;
        }

        .form-control {
            width: 100%;
            margin-bottom: 20px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            font-size: 1rem;
            text-align: center;
            text-decoration: none;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #f58220;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .mt-6 {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="hero">
        <div class="hero-content">
            <h1>Welcome!</h1>
            <h5>YSDN</h5>
            <p>Please login or signup to continue.</p>
            <div class="card">
                <div class="card-body">
                    <div class="form-control mt-6">
                        <a href="https://ysdnthailand.com/ysdn/auth/login.php" class="btn btn-primary w-full">Login</a>
                    </div>
                    <div class="form-control mt-6">
                        <a href="https://ysdnthailand.com/ysdn/auth/registerUser.php" class="btn btn-secondary w-full">Signup</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
