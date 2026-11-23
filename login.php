<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Weddingku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>
    <style>
        .bg-noise {
            background-image: radial-gradient(circle at 20% 50%, rgba(236,72,153,0.08) 0%, transparent 50%),
                              radial-gradient(circle at 80% 20%, rgba(168,85,247,0.08) 0%, transparent 50%),
                              radial-gradient(circle at 50% 80%, rgba(59,130,246,0.06) 0%, transparent 50%);
        }
        .card-glow { box-shadow: 0 0 60px rgba(236,72,153,0.08), 0 25px 50px rgba(0,0,0,0.5); }
        .input-focus:focus { box-shadow: 0 0 0 2px rgba(236,72,153,0.3); }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }
        .float-anim { animation: float 4s ease-in-out infinite; }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center font-sans bg-noise p-4">

    <!-- Background decorations -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-pink-500/5 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full bg-violet-500/5 blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 rounded-full bg-rose-500/3 blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-sm">

        <!-- Logo / Brand -->
        <div class="text-center mb-8 float-anim">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-pink-500 to-rose-400 shadow-lg shadow-pink-500/30 mb-4">
                <i class="fa-solid fa-heart text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">Weddingku</h1>
            <p class="text-slate-400 text-sm mt-1">Admin Dashboard</p>
        </div>

        <!-- Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-2xl p-8 card-glow">
            <h2 class="text-lg font-semibold text-white mb-1">Selamat datang 👋</h2>
            <p class="text-slate-400 text-sm mb-6">Masuk ke akun Anda untuk melanjutkan</p>

            <?php if (!empty($_GET['error'])): ?>
            <div class="flex items-center gap-2 bg-red-500/10 border border-red-500/20 text-red-400 text-sm rounded-xl px-4 py-3 mb-5">
                <i class="fa-solid fa-circle-exclamation text-xs flex-shrink-0"></i>
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
            <?php endif; ?>

            <form action="auth_login.php" method="POST" class="space-y-5">

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-slate-500 text-sm"></i>
                        </div>
                        <input type="email" name="email" required
                               placeholder="admin@example.com"
                               class="w-full bg-slate-800/60 border border-slate-700 text-white placeholder-slate-500
                                      rounded-xl pl-10 pr-4 py-3 text-sm
                                      focus:outline-none focus:border-pink-500/60 input-focus
                                      transition duration-200">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-slate-500 text-sm"></i>
                        </div>
                        <input type="password" name="password" id="passwordInput" required
                               placeholder="••••••••"
                               class="w-full bg-slate-800/60 border border-slate-700 text-white placeholder-slate-500
                                      rounded-xl pl-10 pr-11 py-3 text-sm
                                      focus:outline-none focus:border-pink-500/60 input-focus
                                      transition duration-200">
                        <button type="button" onclick="togglePassword()"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition">
                            <i class="fa-solid fa-eye text-sm" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <!-- Forgot password -->
                <div class="flex justify-end">
                    <a href="forgot_password.php" class="text-xs text-pink-400 hover:text-pink-300 transition">
                        Lupa password?
                    </a>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full bg-gradient-to-r from-pink-500 to-rose-500
                               hover:from-pink-400 hover:to-rose-400
                               text-white font-semibold py-3 rounded-xl text-sm
                               shadow-lg shadow-pink-500/20 hover:shadow-pink-500/30
                               transition-all duration-200 active:scale-[0.98]
                               flex items-center justify-center gap-2">
                    <i class="fa-solid fa-right-to-bracket text-sm"></i>
                    Masuk
                </button>

            </form>

            <!-- Register link -->
            <p class="text-center text-sm text-slate-500 mt-6">
                Belum punya akun?
                <a href="register.php" class="text-pink-400 hover:text-pink-300 font-medium transition">Daftar sekarang</a>
            </p>
        </div>

        <!-- Footer -->
        <p class="text-center text-slate-600 text-xs mt-6">© 2025 Weddingku · All rights reserved</p>
    </div>

<script>
function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon  = document.getElementById('eyeIcon');
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    icon.classList.toggle('fa-eye', !isHidden);
    icon.classList.toggle('fa-eye-slash', isHidden);
}
</script>
</body>
</html>
