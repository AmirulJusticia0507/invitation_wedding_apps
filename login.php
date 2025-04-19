<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Wedding App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: url('images/bg-miror.jpg') no-repeat center center fixed;
            background-size: cover;
        }
    </style>
</head>
<body class="d-flex align-items-center vh-100">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card p-4">
                <h4 class="text-center mb-4">
                    <i class="fa fa-ring"></i> Wedding App Login
                </h4>
                <form action="auth_login.php" method="POST">
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3 position-relative">
                        <label>Password</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" id="password">
                            <span class="input-group-text" onclick="togglePassword()">
                                <i class="fa fa-eye" id="eyeIcon"></i>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                    <div class="text-center mt-2">
                        <a href="register.php">Register</a> |
                        <a href="forgot_password.php">Forgot Password?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function togglePassword() {
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eyeIcon");
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.classList.remove("fa-eye");
            eyeIcon.classList.add("fa-eye-slash");
        } else {
            passwordInput.type = "password";
            eyeIcon.classList.remove("fa-eye-slash");
            eyeIcon.classList.add("fa-eye");
        }
    }
</script>
</body>
</html>