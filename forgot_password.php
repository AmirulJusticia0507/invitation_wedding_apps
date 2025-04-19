<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Wedding App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="d-flex align-items-center vh-100" style="background: url('bg-miror.jpg') no-repeat center center fixed; background-size: cover;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card p-4">
                <h4 class="text-center mb-4">Lupa Password</h4>
                <form action="auth_forgot_password.php" method="POST">
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Kirim Reset Link</button>
                    <div class="text-center mt-3">
                        <a href="login.php">Kembali ke Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>