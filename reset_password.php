<?php
$token = htmlspecialchars($_GET['token'] ?? '', ENT_QUOTES);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Weddingku</title>
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
            background-image: radial-gradient(circle at 30% 50%, rgba(16,185,129,0.07) 0%, transparent 50%),
                              radial-gradient(circle at 70% 20%, rgba(236,72,153,0.06) 0%, transparent 50%);
        }
        .card-glow { box-shadow: 0 0 60px rgba(16,185,129,0.08), 0 25px 50px rgba(0,0,0,0.5); }
        .input-focus:focus { box-shadow: 0 0 0 2px rgba(16,185,129,0.3); }
    </style>
</head>
<body class="bg-slate-950 min-h-screen flex items-center justify-center font-sans bg-noise p-4">

    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -left-40 w-80 h-80 rounded-full bg-emerald-500/5 blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 w-80 h-80 rounded-full bg-pink-500/5 blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-sm">

        <?php if (empty($token)): ?>
        <!-- Token tidak ada -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-red-500/20 border border-red-500/30 mb-4">
                <i class="fa-solid fa-triangle-exclamation text-red-400 text-2xl"></i>
            </div>
            <h1 class="text-xl font-bold text-white mb-2">Token Tidak Valid</h1>
            <p class="text-slate-400 text-sm mb-6">Link reset password tidak valid atau sudah kadaluarsa.</p>
            <a href="forgot_password.php"
               class="inline-flex items-center gap-2 bg-gradient-to-r from-pink-500 to-rose-500
                      text-white font-semibold py-3 px-6 rounded-xl text-sm
                      shadow-lg shadow-pink-500/20 transition-all duration-200">
                <i class="fa-solid fa-rotate-left text-sm"></i>
                Minta Token Baru
            </a>
        </div>
        <?php else: ?>

        <!-- Brand -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-400 shadow-lg shadow-emerald-500/30 mb-4">
                <i class="fa-solid fa-shield-halved text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">Reset Password</h1>
            <p class="text-slate-400 text-sm mt-1">Buat password baru untuk akun Anda</p>
        </div>

        <!-- Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800 rounded-2xl p-8 card-glow">
            <form id="resetForm" class="space-y-5">
                <input type="hidden" name="token" value="<?= $token ?>">

                <!-- Password baru -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Password Baru</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-slate-500 text-sm"></i>
                        </div>
                        <input type="password" name="password" id="newPassword" required placeholder="Min. 8 karakter"
                               class="w-full bg-slate-800/60 border border-slate-700 text-white placeholder-slate-500
                                      rounded-xl pl-10 pr-11 py-3 text-sm
                                      focus:outline-none focus:border-emerald-500/60 input-focus transition duration-200">
                        <button type="button" onclick="togglePassword('newPassword', 'eyeNew')"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition">
                            <i class="fa-solid fa-eye text-sm" id="eyeNew"></i>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi password -->
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock-open text-slate-500 text-sm"></i>
                        </div>
                        <input type="password" id="confirmPassword" required placeholder="Ulangi password baru"
                               class="w-full bg-slate-800/60 border border-slate-700 text-white placeholder-slate-500
                                      rounded-xl pl-10 pr-11 py-3 text-sm
                                      focus:outline-none focus:border-emerald-500/60 input-focus transition duration-200">
                        <button type="button" onclick="togglePassword('confirmPassword', 'eyeConfirm')"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-slate-300 transition">
                            <i class="fa-solid fa-eye text-sm" id="eyeConfirm"></i>
                        </button>
                    </div>
                    <p id="matchMsg" class="text-xs mt-1.5 hidden"></p>
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-emerald-500 to-teal-500
                               hover:from-emerald-400 hover:to-teal-400
                               text-white font-semibold py-3 rounded-xl text-sm
                               shadow-lg shadow-emerald-500/20
                               transition-all duration-200 active:scale-[0.98]
                               flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-sm"></i>
                    Simpan Password Baru
                </button>
            </form>

            <p class="text-center text-sm text-slate-500 mt-6">
                <a href="login.php" class="text-emerald-400 hover:text-emerald-300 font-medium transition">
                    <i class="fa-solid fa-arrow-left text-xs mr-1"></i> Kembali ke Login
                </a>
            </p>
        </div>
        <?php endif; ?>

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

// Real-time password match check
document.getElementById('confirmPassword')?.addEventListener('input', function() {
    const pass    = document.getElementById('newPassword').value;
    const confirm = this.value;
    const msg     = document.getElementById('matchMsg');
    if (!confirm) { msg.classList.add('hidden'); return; }
    msg.classList.remove('hidden');
    if (pass === confirm) {
        msg.textContent = '✓ Password cocok';
        msg.className = 'text-xs mt-1.5 text-emerald-400';
    } else {
        msg.textContent = '✗ Password tidak cocok';
        msg.className = 'text-xs mt-1.5 text-red-400';
    }
});

document.getElementById('resetForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const pass    = document.getElementById('newPassword').value;
    const confirm = document.getElementById('confirmPassword').value;

    if (pass !== confirm) {
        Swal.fire({ icon: 'error', title: 'Password tidak cocok!',
            text: 'Pastikan kedua password sama.',
            background: '#0f172a', color: '#f1f5f9', confirmButtonColor: '#10b981' });
        return;
    }
    if (pass.length < 8) {
        Swal.fire({ icon: 'error', title: 'Password terlalu pendek!',
            text: 'Minimal 8 karakter.',
            background: '#0f172a', color: '#f1f5f9', confirmButtonColor: '#10b981' });
        return;
    }

    const btn = this.querySelector('button[type=submit]');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin text-sm"></i> Menyimpan...';

    const data = new FormData(this);
    try {
        const res  = await fetch('auth_reset_password.php', { method: 'POST', body: data });
        const json = await res.json();
        if (json.status === 'success') {
            await Swal.fire({ icon: 'success', title: 'Berhasil!', text: json.message,
                background: '#0f172a', color: '#f1f5f9', confirmButtonColor: '#10b981' });
            window.location.href = 'login.php';
        } else {
            Swal.fire({ icon: 'error', title: 'Gagal', text: json.message,
                background: '#0f172a', color: '#f1f5f9', confirmButtonColor: '#10b981' });
        }
    } catch {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan.',
            background: '#0f172a', color: '#f1f5f9', confirmButtonColor: '#10b981' });
    }
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-floppy-disk text-sm"></i> Simpan Password Baru';
});
</script>
</body>
</html>
