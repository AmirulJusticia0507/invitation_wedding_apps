<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Weddingku</title>
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
            background-image: radial-gradient(circle at 30% 40%, rgba(59,130,246,0.08) 0%, transparent 50%),
                              radial-gradient(circle at 70% 70%, rgba(236,72,153,0.06) 0%, transparent 50%);
        }
        .card-glow { box-shadow: 0 0 60px rgba(59,130,246,0.08), 0 25px 50px rgba(0,0,0,0.5); }
        .input-focus:focus { box-shadow: 0 0 0 2px rgba(59,130,246,0.3); }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center font-sans bg-noise p-4">

    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 rounded-full bg-blue-500/5 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 rounded-full bg-pink-500/5 blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-sm">

        <!-- Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-400 shadow-lg shadow-blue-500/30 mb-4">
                <i class="fa-solid fa-key text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">Lupa Password</h1>
            <p class="text-slate-400 text-sm mt-1">Masukkan email untuk mendapatkan token reset</p>
        </div>

        <!-- Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-2xl p-8 card-glow">

            <!-- Info box -->
            <div class="flex items-start gap-3 bg-blue-500/10 border border-blue-500/20 rounded-xl p-4 mb-6">
                <i class="fa-solid fa-circle-info text-blue-400 text-sm mt-0.5 flex-shrink-0"></i>
                <p class="text-blue-300 text-xs leading-relaxed">
                    Masukkan email yang terdaftar. Token reset akan ditampilkan dan kamu bisa langsung reset password.
                </p>
            </div>

            <form id="forgotForm" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Email Terdaftar</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-slate-500 text-sm"></i>
                        </div>
                        <input type="email" name="email" id="emailInput" required placeholder="admin@example.com"
                               class="w-full bg-slate-800/60 border border-slate-700 text-white placeholder-slate-500
                                      rounded-xl pl-10 pr-4 py-3 text-sm
                                      focus:outline-none focus:border-blue-500/60 input-focus transition duration-200">
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-500 to-cyan-500
                               hover:from-blue-400 hover:to-cyan-400
                               text-white font-semibold py-3 rounded-xl text-sm
                               shadow-lg shadow-blue-500/20
                               transition-all duration-200 active:scale-[0.98]
                               flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane text-sm"></i>
                    Kirim Token Reset
                </button>
            </form>

            <!-- Result box (hidden by default) -->
            <div id="tokenResult" class="hidden mt-5 bg-emerald-500/10 border border-emerald-500/20 rounded-xl p-4">
                <p class="text-emerald-400 text-xs font-semibold mb-1">✅ Token berhasil dibuat!</p>
                <p class="text-slate-400 text-xs mb-2">Gunakan token ini untuk reset password:</p>
                <div class="flex items-center gap-2 bg-slate-800 rounded-lg px-3 py-2">
                    <code id="tokenValue" class="text-emerald-300 text-xs flex-1 break-all"></code>
                    <button onclick="copyToken()" class="text-slate-400 hover:text-white transition flex-shrink-0">
                        <i class="fa-solid fa-copy text-sm" id="copyIcon"></i>
                    </button>
                </div>
                <a id="resetLink" href="#"
                   class="mt-3 w-full flex items-center justify-center gap-2 bg-emerald-500/20 hover:bg-emerald-500/30
                          text-emerald-400 text-xs font-medium rounded-lg px-4 py-2.5 transition">
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                    Lanjut ke Reset Password
                </a>
            </div>

            <p class="text-center text-sm text-slate-500 mt-6">
                <a href="login.php" class="text-blue-400 hover:text-blue-300 font-medium transition">
                    <i class="fa-solid fa-arrow-left text-xs mr-1"></i> Kembali ke Login
                </a>
            </p>
        </div>

        <p class="text-center text-slate-600 text-xs mt-6">© 2025 Weddingku · All rights reserved</p>
    </div>

<script>
document.getElementById('forgotForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn   = this.querySelector('button[type=submit]');
    const email = document.getElementById('emailInput').value;

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm"></i> Memproses...';

    const data = new FormData();
    data.append('email', email);

    try {
        const res  = await fetch('auth_forgot_password.php', { method: 'POST', body: data });
        const json = await res.json();

        if (json.status === 'success') {
            document.getElementById('tokenValue').textContent = json.token;
            document.getElementById('resetLink').href = 'reset_password.php?token=' + encodeURIComponent(json.token);
            document.getElementById('tokenResult').classList.remove('hidden');
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: json.message,
                background: '#0f172a', color: '#f1f5f9', confirmButtonColor: '#3b82f6' });
        }
    } catch {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan.',
            background: '#0f172a', color: '#f1f5f9', confirmButtonColor: '#3b82f6' });
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-paper-plane text-sm"></i> Kirim Token Reset';
});

function copyToken() {
    const token = document.getElementById('tokenValue').textContent;
    navigator.clipboard.writeText(token).then(() => {
        const icon = document.getElementById('copyIcon');
        icon.classList.replace('fa-copy', 'fa-check');
        setTimeout(() => icon.classList.replace('fa-check', 'fa-copy'), 2000);
    });
}
</script>
</body>
</html>
