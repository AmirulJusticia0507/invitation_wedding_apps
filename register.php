<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Weddingku</title>
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
            background-image: radial-gradient(circle at 20% 50%, rgba(168,85,247,0.08) 0%, transparent 50%),
                              radial-gradient(circle at 80% 20%, rgba(236,72,153,0.08) 0%, transparent 50%),
                              radial-gradient(circle at 50% 80%, rgba(59,130,246,0.06) 0%, transparent 50%);
        }
        .card-glow { box-shadow: 0 0 60px rgba(168,85,247,0.08), 0 25px 50px rgba(0,0,0,0.5); }
        .input-focus:focus { box-shadow: 0 0 0 2px rgba(168,85,247,0.3); }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center font-sans bg-noise p-4">

    <!-- Background decorations -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -left-40 w-80 h-80 rounded-full bg-violet-500/5 blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 w-80 h-80 rounded-full bg-pink-500/5 blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-sm">

        <!-- Brand -->
        <div class="text-center mb-8">
            <a href="login.php" class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-400 shadow-lg shadow-violet-500/30 mb-4">
                <i class="fa-solid fa-user-plus text-white text-2xl"></i>
            </a>
            <h1 class="text-2xl font-bold text-white">Buat Akun</h1>
            <p class="text-slate-400 text-sm mt-1">Daftar untuk mulai kelola undangan</p>
        </div>

        <!-- Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-2xl p-8 card-glow">
            <form id="registerForm" action="auth_register.php" method="POST" class="space-y-5">

                <!-- Nama -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Nama Lengkap</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-user text-slate-500 text-sm"></i>
                        </div>
                        <input type="text" name="nama" required placeholder="Nama lengkap Anda"
                               class="w-full bg-slate-800/60 border border-slate-700 text-white placeholder-slate-500
                                      rounded-xl pl-10 pr-4 py-3 text-sm
                                      focus:outline-none focus:border-violet-500/60 input-focus transition duration-200">
                    </div>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-slate-500 text-sm"></i>
                        </div>
                        <input type="email" name="email" required placeholder="admin@example.com"
                               class="w-full bg-slate-800/60 border border-slate-700 text-white placeholder-slate-500
                                      rounded-xl pl-10 pr-4 py-3 text-sm
                                      focus:outline-none focus:border-violet-500/60 input-focus transition duration-200">
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-slate-500 text-sm"></i>
                        </div>
                        <input type="password" name="password" id="registerPassword" required placeholder="Min. 8 karakter"
                               class="w-full bg-slate-800/60 border border-slate-700 text-white placeholder-slate-500
                                      rounded-xl pl-10 pr-11 py-3 text-sm
                                      focus:outline-none focus:border-violet-500/60 input-focus transition duration-200">
                        <button type="button" onclick="togglePassword('registerPassword', 'eyeReg')"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition">
                            <i class="fa-solid fa-eye text-sm" id="eyeReg"></i>
                        </button>
                    </div>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full bg-gradient-to-r from-violet-500 to-purple-500
                               hover:from-violet-400 hover:to-purple-400
                               text-white font-semibold py-3 rounded-xl text-sm
                               shadow-lg shadow-violet-500/20 hover:shadow-violet-500/30
                               transition-all duration-200 active:scale-[0.98]
                               flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-plus text-sm"></i>
                    Daftar Sekarang
                </button>

            </form>

            <p class="text-center text-sm text-slate-500 mt-6">
                Sudah punya akun?
                <a href="login.php" class="text-violet-400 hover:text-violet-300 font-medium transition">Masuk di sini</a>
            </p>
        </div>

        <p class="text-center text-slate-600 text-xs mt-6">© 2025 Weddingku · All rights reserved</p>
    </div>

<script>
function togglePassword(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    const isHidden = input.type === 'password';
    input.type = isHidden ? 'text' : 'password';
    icon.classList.toggle('fa-eye', !isHidden);
    icon.classList.toggle('fa-eye-slash', isHidden);
}

document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm"></i> Memproses...';

    const data = new FormData(form);
    try {
        const res  = await fetch('auth_register.php', { method: 'POST', body: data });
        const json = await res.json();
        if (json.status === 'success') {
            await Swal.fire({ icon: 'success', title: 'Berhasil!', text: json.message,
                background: '#0f172a', color: '#f1f5f9',
                confirmButtonColor: '#8b5cf6' });
            window.location.href = 'login.php';
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: json.message,
                background: '#0f172a', color: '#f1f5f9',
                confirmButtonColor: '#8b5cf6' });
        }
    } catch {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan. Coba lagi.',
            background: '#0f172a', color: '#f1f5f9', confirmButtonColor: '#8b5cf6' });
    }
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-user-plus text-sm"></i> Daftar Sekarang';
});
</script>
</body>
</html>
