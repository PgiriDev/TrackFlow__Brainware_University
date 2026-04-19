<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Forbidden | TrackFlow</title>
    <link rel="icon" type="image/png" href="{{ asset('trackflow-main/fav-icon.png') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 50%, #6ee7b7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        .illustration {
            position: absolute;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .illustration-content {
            position: relative;
            width: 400px;
            height: 400px;
            opacity: 0.3;
        }

        .lock {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120px;
            height: 140px;
        }

        .lock-body {
            width: 120px;
            height: 90px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 15px;
            position: absolute;
            bottom: 0;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
        }

        .lock-shackle {
            width: 80px;
            height: 60px;
            border: 12px solid #10b981;
            border-bottom: none;
            border-radius: 40px 40px 0 0;
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
        }

        .keyhole {
            width: 20px;
            height: 20px;
            background: #d1fae5;
            border-radius: 50%;
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
        }

        .keyhole::after {
            content: '';
            width: 8px;
            height: 25px;
            background: #d1fae5;
            position: absolute;
            top: 15px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 0 0 4px 4px;
        }

        .guard {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100px;
            margin-top: 80px;
        }

        .guard-head {
            width: 50px;
            height: 50px;
            background: #064e3b;
            border-radius: 50%;
            margin: 0 auto;
            position: relative;
        }

        .guard-body {
            width: 80px;
            height: 100px;
            background: #065f46;
            margin: 5px auto 0;
            position: relative;
            border-radius: 10px 10px 0 0;
        }

        .guard-arm {
            width: 30px;
            height: 60px;
            background: #065f46;
            position: absolute;
            top: 10px;
            border-radius: 15px;
        }

        .guard-arm.left {
            left: -25px;
            transform: rotate(-45deg);
        }

        .guard-arm.right {
            right: -25px;
            transform: rotate(45deg);
        }

        .barrier {
            position: absolute;
            bottom: 100px;
            left: 50%;
            transform: translateX(-50%);
            width: 300px;
            height: 40px;
            background: repeating-linear-gradient(45deg,
                    #065f46,
                    #065f46 30px,
                    #fbbf24 30px,
                    #fbbf24 60px);
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .traffic-cone {
            position: absolute;
            bottom: 80px;
            width: 40px;
            height: 60px;
        }

        .traffic-cone.left {
            left: 20%;
        }

        .traffic-cone.right {
            right: 20%;
        }

        .cone-top {
            width: 0;
            height: 0;
            border-left: 20px solid transparent;
            border-right: 20px solid transparent;
            border-bottom: 50px solid #f97316;
            position: relative;
        }

        .cone-stripes {
            position: absolute;
            width: 100%;
            height: 10px;
            background: white;
            top: 20px;
        }

        .cone-base {
            width: 50px;
            height: 10px;
            background: #064e3b;
            margin: 0 auto;
            border-radius: 5px;
        }

        .container {
            text-align: center;
            color: #064e3b;
            z-index: 10;
            position: relative;
            padding: 40px;
            max-width: 600px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
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
            filter: drop-shadow(0 4px 12px rgba(16, 185, 129, 0.3));
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 4px 20px rgba(16, 185, 129, 0.2);
            letter-spacing: -5px;
        }

        .error-title {
            font-size: clamp(20px, 4vw, 32px);
            margin-bottom: 10px;
            font-weight: 700;
            color: #065f46;
        }

        .error-message {
            font-size: clamp(14px, 2.5vw, 18px);
            margin-bottom: 10px;
            color: #047857;
            font-weight: 500;
        }

        .error-submessage {
            font-size: clamp(12px, 2vw, 16px);
            margin-bottom: 40px;
            color: #059669;
        }

        .btn-back {
            display: inline-block;
            padding: 16px 40px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 32px rgba(16, 185, 129, 0.6);
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
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
            color: #047857;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
            padding: 8px 16px;
            border-radius: 20px;
            background: rgba(16, 185, 129, 0.1);
            font-weight: 500;
        }

        .links a:hover {
            background: rgba(16, 185, 129, 0.2);
            color: #065f46;
        }

        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
                margin: 20px;
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

            .illustration-content {
                width: 300px;
                height: 300px;
            }

            .lock {
                width: 80px;
                height: 100px;
            }

            .lock-body {
                width: 80px;
                height: 60px;
            }

            .lock-shackle {
                width: 50px;
                height: 40px;
                border-width: 8px;
                top: -35px;
            }

            .barrier {
                width: 200px;
                height: 30px;
            }
        }
    </style>
</head>

<body>
    <div class="illustration">
        <div class="illustration-content">
            <div class="lock">
                <div class="lock-shackle"></div>
                <div class="lock-body">
                    <div class="keyhole"></div>
                </div>
            </div>
            <div class="guard">
                <div class="guard-head"></div>
                <div class="guard-body">
                    <div class="guard-arm left"></div>
                    <div class="guard-arm right"></div>
                </div>
            </div>
            <div class="barrier"></div>
            <div class="traffic-cone left">
                <div class="cone-top">
                    <div class="cone-stripes"></div>
                </div>
                <div class="cone-base"></div>
            </div>
            <div class="traffic-cone right">
                <div class="cone-top">
                    <div class="cone-stripes"></div>
                </div>
                <div class="cone-base"></div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="logo">
            <img src="{{ asset('trackflow-main/logo.png') }}" alt="TrackFlow Logo">
        </div>

        <h1 class="error-code">403</h1>
        <h2 class="error-title">ERROR FORBIDDEN</h2>
        <p class="error-message">Access Denied</p>
        <p class="error-submessage">You don't have permission to access this resource</p>

        <a href="{{ url('/dashboard') }}" class="btn-back">Go Back Home</a>

        <div class="links">
            <a href="{{ url('/dashboard') }}">Dashboard</a>
            <a href="{{ url('/transactions') }}">Transactions</a>
            <a href="{{ url('/budgets') }}">Budgets</a>
            <a href="{{ url('/reports') }}">Reports</a>
        </div>

        <div
            style="margin-top: 40px; padding-top: 20px; border-top: 1px solid rgba(6, 78, 59, 0.2); font-size: 12px; color: #047857;">
            <p>© {{ date('Y') }} TrackFlow - Personal Finance Management System</p>
            <p style="margin-top: 5px; font-size: 11px;">All rights reserved.</p>
        </div>
    </div>
</body>

</html>