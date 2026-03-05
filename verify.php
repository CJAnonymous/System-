<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Your Account</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
            animation: fadeIn 1s ease-in-out;
        }
        h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 10px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-group input:focus {
            border-color: #6a11cb;
            box-shadow: 0 0 8px rgba(106, 17, 203, 0.3);
            outline: none;
        }
        .btn {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #6a11cb;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .btn:hover {
            background-color: #2575fc;
            transform: translateY(-2px);
        }
        .resend-container {
            margin-top: 20px;
            font-size: 14px;
            color: #555;
        }
        .resend-container button {
            background: none;
            border: none;
            color: #6a11cb;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: underline;
            transition: color 0.3s ease;
        }
        .resend-container button:disabled {
            color: #bbb;
            cursor: not-allowed;
        }
        .timer {
            margin-top: 5px;
            font-size: 12px;
            color: #888;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Verify Your Account</h1>
        <form action="verify_process.php" method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">
            <div class="form-group">
                <label for="verification_code">Enter Verification Code:</label>
                <input type="text" id="verification_code" name="verification_code" placeholder="6-digit code" required>
            </div>
            <button type="submit" class="btn">Verify</button>
        </form>
        <div class="resend-container">
            <button id="resend-btn" disabled>Resend Code</button>
            <div class="timer" id="timer">You can resend the code in 2:00 minutes.</div>
        </div>
    </div>

    <script>
        // Timer logic
        const timerElement = document.getElementById("timer");
        const resendButton = document.getElementById("resend-btn");
        let timeLeft = 120; // 2 minutes in seconds

        const updateTimer = () => {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `You can resend the code in ${minutes}:${seconds.toString().padStart(2, '0')} minutes.`;

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                resendButton.disabled = false;
                timerElement.textContent = "You can now resend the code.";
            } else {
                timeLeft--;
            }
        };

        // Start the timer
        const timerInterval = setInterval(updateTimer, 1000);

        // Add event listener for the resend button
        resendButton.addEventListener("click", () => {
            resendButton.disabled = true;
            timeLeft = 120; // Reset the timer to 2 minutes
            updateTimer(); // Update the timer display immediately
            alert("Verification code has been resent to your email.");
            // Add your resend logic here, e.g., an AJAX request or page redirection
        });
    </script>
</body>
</html>
