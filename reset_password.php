
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
  <style>
      * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
          font-family: 'Arial', sans-serif;
      }
      body {
          height: 100vh;
          background: url('Brgy bg.png') no-repeat center center fixed;
          background-size: cover;
          display: flex;
          justify-content: center;
          align-items: center;
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
      .password-container {
          position: relative;
          z-index: 2;
          background: rgba(255, 255, 255, 0.95);
          padding: 40px;
          border-radius: 15px;
          width: 90%;
          max-width: 500px;
          box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      }
      .password-header {
          text-align: center;
          margin-bottom: 30px;
      }
      .password-header img {
          width: 180px;
          margin-bottom: 25px;
      }
      .password-header h2 {
          color: #333;
          font-size: 28px;
          margin-bottom: 10px;
      }
      .input-group {
          position: relative;
          margin-bottom: 40px;
          width: 100%;
      }
      .input-group input {
          width: 100%;
          padding: 15px 0;
          font-size: 20px;
          color: #333;
          border: none;
          border-bottom: 3px solid #ccc;
          outline: none;
          background: none;
          transition: border-color 0.3s ease;
      }
      .input-group input:focus {
          border-bottom-color: #007BFF;
      }
      .input-group label {
          position: absolute;
          top: 12px;
          left: 0;
          font-size: 18px;
          color: #555;
          pointer-events: none;
          transition: all 0.3s ease;
      }
      .input-group input:focus + label,
      .input-group input:not(:placeholder-shown) + label {
          top: -25px;
          font-size: 16px;
          color: #007BFF;
      }
      .input-group .icon {
          position: absolute;
          top: 50%;
          left: -35px;
          transform: translateY(-50%);
          font-size: 22px;
          color: #555;
      }
      .btn {
          width: 100%;
          padding: 20px;
          font-size: 20px;
          color: #fff;
          background-color: #007BFF;
          border: none;
          border-radius: 25px;
          cursor: pointer;
          transition: background-color 0.3s ease;
      }
      .btn:hover {
          background-color: #0056b3;
      }
      .auth-links {
          margin-top: 25px;
          text-align: center;
      }
      .auth-links a {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          color: #007BFF;
          text-decoration: none;
          font-size: 18px;
          transition: color 0.3s ease;
      }
      .auth-links a:hover {
          color: #0056b3;
      }
      .alert-error {
          padding: 15px;
          margin-bottom: 20px;
          border-radius: 8px;
          background: #f8d7da;
          color: #721c24;
          border: 1px solid #f5c6cb;
      }
      @media (max-width: 768px) {
          .password-container {
              padding: 30px;
              margin: 20px;
          }
          .input-group .icon {
              left: -25px;
              font-size: 20px;
          }
      }
  </style>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="overlay"></div>
  <div class="password-container">
      <div class="password-header">
          <img src="logo.png" alt="Logo" class="logo">
          <h2>Set New Password</h2>
      </div>

      <!-- Display any error message -->
      <?php if (isset($_SESSION['error'])): ?>
          <div class="alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
          <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <!-- Password reset form -->
      <form method="POST">
          <!-- Preserve the token in a hidden field -->
          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
          
          <div class="input-group">
              <i class="fas fa-lock icon"></i>
              <input type="password" id="password" name="password" placeholder=" " required>
              <label for="password">New Password</label>
          </div>
          
          <div class="input-group">
              <i class="fas fa-lock icon"></i>
              <input type="password" id="confirm_password" name="confirm_password" placeholder=" " required>
              <label for="confirm_password">Confirm Password</label>
          </div>
          
          <button type="submit" class="btn">Reset Password</button>
          
          <div class="auth-links">
              <a href="login.php">
                  <i class="fas fa-arrow-left"></i> Back to Login
              </a>
          </div>
      </form>
  </div>
</body>
</html>
