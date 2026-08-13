<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Wedding App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="d-flex align-items-center vh-100" style="background: url('bg-miror.jpg') no-repeat center center fixed; background-size: cover;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card p-4">
                <h4 class="text-center mb-4">Reset Password</h4>
                <form action="auth_reset_password.php" method="POST">
                    <input type="hidden" name="token" value="<?= $_GET['token'] ?? '' ?>">
                    <div class="mb-3">
                        <label>Password Baru</label>
                        <div class="input-group">
                            <input type="password" name="password" class="form-control" id="resetPassword" required>
                            <span class="input-group-text" onclick="togglePassword('resetPassword')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Simpan Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    togglePassword = (id) => {
        const input = document.getElementById(id);
        const icon = input.nextElementSibling.querySelector('i');
        if (input.type === "password") {
            input.type = "text";
            icon.classList.replace("fa-eye", "fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.replace("fa-eye-slash", "fa-eye");
        }
    }
</script>
</body>
</html>