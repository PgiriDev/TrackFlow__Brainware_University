<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | TrackFlow</title>
    <link rel="icon" type="image/png" href="{{ asset('trackflow-main/fav-icon.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 50%, #7e22ce 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .circles {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%);
            opacity: 0.6;
            animation: float 20s infinite ease-in-out;
            box-shadow: 0 8px 32px rgba(236, 72, 153, 0.3);
        }

        .circle:nth-child(1) {
            width: 180px;
            height: 180px;
            left: 10%;
            top: 20%;
            animation-duration: 18s;
            animation-delay: 0s;
        }

        .circle:nth-child(2) {
            width: 100px;
            height: 100px;
            left: 40%;
            top: 10%;
            animation-duration: 22s;
            animation-delay: 2s;
        }

        .circle:nth-child(3) {
            width: 60px;
            height: 60px;
            left: 35%;
            top: 30%;
            animation-duration: 15s;
            animation-delay: 4s;
        }

        .circle:nth-child(4) {
            width: 80px;
            height: 80px;
            right: 30%;
            top: 15%;
            animation-duration: 20s;
            animation-delay: 1s;
        }

        .circle:nth-child(5) {
            width: 140px;
            height: 140px;
            right: 10%;
            top: 25%;
            animation-duration: 25s;
            animation-delay: 3s;
        }

        .circle:nth-child(6) {
            width: 90px;
            height: 90px;
            left: 20%;
            bottom: 20%;
            animation-duration: 17s;
            animation-delay: 2s;
        }

        .circle:nth-child(7) {
            width: 120px;
            height: 120px;
            left: 50%;
            bottom: 30%;
            animation-duration: 23s;
            animation-delay: 5s;
        }

        .circle:nth-child(8) {
            width: 200px;
            height: 200px;
            right: 15%;
            bottom: 15%;
            animation-duration: 19s;
            animation-delay: 1s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            25% {
                transform: translate(30px, -30px) scale(1.1);
            }

            50% {
                transform: translate(-20px, 40px) scale(0.9);
            }

            75% {
                transform: translate(40px, 20px) scale(1.05);
            }
        }

        .container {
            text-align: center;
            color: white;
            z-index: 10;
            position: relative;
            padding: 40px;
            max-width: 600px;
        }

        .logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 30px;
            animation: pulse 2s infinite;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(255, 255, 255, 0.3));
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .error-code {
            font-size: clamp(80px, 15vw, 150px);
            font-weight: 900;
            line-height: 1;
            margin-bottom: 20px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            letter-spacing: -5px;
        }

        .error-message {
            font-size: clamp(18px, 3vw, 24px);
            margin-bottom: 10px;
            font-weight: 600;
            opacity: 0.95;
        }

        .error-submessage {
            font-size: clamp(14px, 2.5vw, 18px);
            margin-bottom: 40px;
            opacity: 0.8;
        }

        .btn-back {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px rgba(236, 72, 153, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(236, 72, 153, 0.6);
            background: linear-gradient(135deg, #f472b6 0%, #ec4899 100%);
        }

        .btn-back:active {
            transform: translateY(-1px);
        }

        .links {
            margin-top: 30px;
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .links a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .links a:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .logo {
                width: 60px;
                height: 60px;
                margin-bottom: 20px;
            }

            .error-code {
                margin-bottom: 15px;
            }

            .btn-back {
                padding: 14px 32px;
                font-size: 14px;
            }

            .links {
                gap: 10px;
            }

            .links a {
                font-size: 12px;
                padding: 6px 12px;
            }
        }
    </style>
</head>

<body>
    <div class="circles">
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
        <div class="circle"></div>
    </div>

    <div class="container">
        <div class="logo">
            <img src="{{ asset('trackflow-main/logo.png') }}" alt="TrackFlow Logo">
        </div>

        <h1 class="error-code">404</h1>
        <p class="error-message">It looks like you're lost...</p>
        <p class="error-submessage">That's a trouble?</p>

        <a href="{{ url('/dashboard') }}" class="btn-back">Go Back</a>

        <div class="links">
            <a href="{{ url('/dashboard') }}">Dashboard</a>
            <a href="{{ url('/transactions') }}">Transactions</a>
            <a href="{{ url('/budgets') }}">Budgets</a>
            <a href="{{ url('/reports') }}">Reports</a>
        </div>

        <div
            style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(255, 255, 255, 0.2); font-size: 12px; color: rgba(255, 255, 255, 0.7);">
            <p>© {{ date('Y') }} TrackFlow - Personal Finance Management System</p>
            <p style="margin-top: 5px; font-size: 11px;">All rights reserved.</p>
        </div>
    </div>
</body>

</html>