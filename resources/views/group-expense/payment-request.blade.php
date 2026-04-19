<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Request - TrackFlow</title>
    <link rel="icon" type="image/png" href="{{ asset('img/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .pay-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s ease;
        }

        .pay-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(16, 185, 129, 0.4);
        }

        .pay-btn:active {
            transform: translateY(0);
        }

        .pulse-ring {
            animation: pulse-ring 1.5s cubic-bezier(0.215, 0.61, 0.355, 1) infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.9);
                opacity: 1;
            }

            80%,
            100% {
                transform: scale(1.3);
                opacity: 0;
            }
        }
    </style>
</head>

<body class="flex items-center justify-center p-4">
    <div class="glass-card rounded-3xl p-8 max-w-md w-full mx-auto">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div
                class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <i class="fas fa-wallet text-white text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Payment Request</h1>
            <p class="text-gray-500 mt-1">via TrackFlow</p>
        </div>

        <!-- Payment Details Card -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 mb-6">
            <div class="text-center mb-4">
                <p class="text-sm text-gray-500 mb-1">Pay to</p>
                <h2 class="text-xl font-bold text-gray-800">{{ $name }}</h2>
            </div>

            <div class="border-t border-gray-200 pt-4 mt-4">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-500">UPI ID</span>
                    <span class="font-mono text-gray-800 text-sm">{{ $upiId }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Amount</span>
                    <span class="text-3xl font-bold text-green-600">₹{{ number_format($amount, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Pay Now Button -->
        <div class="relative mb-6">
            <div class="absolute inset-0 bg-green-500 rounded-2xl pulse-ring"></div>
            <a href="#" onclick="handleUpiPayClick(event, '{{ $upiLink }}', '{{ $upiId }}')"
                class="pay-btn relative block w-full text-white text-center py-4 px-6 rounded-2xl font-bold text-lg shadow-lg">
                <i class="fas fa-paper-plane mr-2"></i>
                Pay Now ₹{{ number_format($amount, 2) }}
            </a>
        </div>

        <!-- Instructions -->
        <div class="text-center text-sm text-gray-500">
            <p class="mb-2">
                <i class="fas fa-info-circle mr-1"></i>
                Tap the button to open your UPI app
            </p>
            <p class="text-xs">
                Works with Google Pay, PhonePe, Paytm, BHIM & more
            </p>
            <p id="desktopUpiNote" class="text-xs text-indigo-600 font-medium mt-2 hidden">
                On desktop, the UPI ID will be copied to your clipboard.
            </p>
        </div>

        <!-- UPI Apps Icons -->
        <div class="flex justify-center gap-4 mt-6 opacity-60">
            <div class="w-10 h-10 bg-white rounded-lg shadow flex items-center justify-center">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f2/Google_Pay_Logo.svg/512px-Google_Pay_Logo.svg.png"
                    alt="GPay" class="w-6 h-6 object-contain">
            </div>
            <div class="w-10 h-10 bg-white rounded-lg shadow flex items-center justify-center">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/71/PhonePe_Logo.svg/512px-PhonePe_Logo.svg.png"
                    alt="PhonePe" class="w-6 h-6 object-contain">
            </div>
            <div class="w-10 h-10 bg-white rounded-lg shadow flex items-center justify-center">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Paytm_Logo_%28standalone%29.svg/512px-Paytm_Logo_%28standalone%29.svg.png"
                    alt="Paytm" class="w-6 h-6 object-contain">
            </div>
            <div class="w-10 h-10 bg-white rounded-lg shadow flex items-center justify-center">
                <span class="text-blue-600 font-bold text-xs">BHIM</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 pt-6 border-t border-gray-200 text-center">
            <p class="text-xs text-gray-400">
                Secured by <span class="font-semibold text-indigo-600">TrackFlow</span>
            </p>
        </div>
    </div>

    <script>
        // Show desktop note if not on mobile
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (!isMobile) {
            const note = document.getElementById('desktopUpiNote');
            if (note) note.classList.remove('hidden');
        }

        function handleUpiPayClick(event, upiLink, upiId) {
            event.preventDefault();
            if (isMobile) {
                // On mobile, open UPI app
                window.location.href = upiLink;
            } else {
                // On desktop, copy UPI ID to clipboard
                navigator.clipboard.writeText(upiId).then(() => {
                    alert('UPI ID copied to clipboard! Please open your UPI app on your phone to pay.');
                }).catch(() => {
                    // Fallback: show the UPI ID in a prompt for manual copy
                    prompt('UPI link cannot open on desktop. Copy this UPI ID and pay from your phone:', upiId);
                });
            }
        }
    </script>
</body>

</html>