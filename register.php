<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            height: 100vh;
            background: url('Brgy bg.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.7) 60%, rgba(255, 255, 255, 0.8) 100%);
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.7);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            max-width: 800px;
            width: 100%;
            overflow-y: auto;
            max-height: 90vh;
        }

        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            margin-bottom: 5px;
            color: #555;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #007BFF;
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .back-link {
            margin-top: 10px;
            display: block;
            text-align: center;
            font-size: 14px;
            color: #007BFF;
            text-decoration: none;
        }

        .back-link:hover {
            color: #0056b3;
        }

        .container::-webkit-scrollbar {
            width: 8px;
        }

        .container::-webkit-scrollbar-thumb {
            background: #007BFF;
            border-radius: 5px;
        }

        .container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 20px;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="ph-address-selector.js"></script>
</head>
<body>
    <div class="overlay"></div>
    <div class="container">
        <h1>CREATE YOUR ACCOUNT</h1>
        <form action="register_process.php" method="POST">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="middle_name">Middle Name: (Optional)</label>
                <input type="text" id="middle_name" name="middle_name">
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="blk_street">Blk & Street:</label>
                <input type="text" id="blk_street" name="blk_street" required>
            </div>
            <input type="hidden" id="region" name="region" value="Maguindanao Del Norte">
            <input type="hidden" id="city" name="city" value="Baninsilan">
            <input type="hidden" id="barangay" name="barangay" value="Poblacion 1">
            <div class="form-group">
                <label for="birthdate">Birthdate:</label>
                <input type="date" id="birthdate" name="birthdate" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="" disabled selected>Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>
            <div class="form-group">
                <label for="contact_number">Contact Number:</label>
                <input type="text" id="contact_number" name="contact_number" required 
                    pattern="\d{11}" maxlength="11" 
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11);" 
                    title="Please enter exactly 11 digits">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="repeat_password">Repeat Password:</label>
                <input type="password" id="repeat_password" name="repeat_password" required>
            </div>
            <button type="submit" class="btn">Register</button>
            <p style="font-size:14px; color:#333; margin-top:10px; text-align:center;">Your account will be reviewed by an administrator before it can be activated.</p>
            <a href="login.php" class="back-link">Already have an account? Login here</a>
        </form>
    </div>
</body>
</html>