<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Celz5 Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite('resources/css/app.css')
    <style>
        [x-cloak] {
            display: none !important;
        }

        .glass-toast {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="bg-slate-950 text-slate-200 font-sans antialiased overflow-hidden" x-data="loginForm()">

    <div class="fixed top-6 right-6 z-[100] flex flex-col gap-3">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-8"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="glass-toast px-5 py-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[320px]">
                <div
                    :class="{
                        'text-red-400': toast.type === 'error',
                        'text-emerald-400': toast.type === 'success',
                        'text-indigo-400': toast.type === 'info'
                    }">
                    <i :data-lucide="toast.type === 'error' ? 'alert-circle' : (toast.type === 'success' ? 'check-circle' : 'info')"
                        class="w-5 h-5"></i>
                </div>
                <span class="text-sm font-medium text-white/90" x-text="toast.message"></span>
            </div>
        </template>
    </div>

    <div class="min-h-screen flex items-center justify-center p-6 relative">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-indigo-600/10 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-cyan-600/10 blur-[120px] rounded-full"></div>

        <div class="w-full max-w-md z-10">
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 mb-4 shadow-inner">
                    <img src="/images/lz5.png" alt="Logo" class="w-10 h-10 object-contain">
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">Admin Portal</h1>
                <p class="text-slate-500 text-sm mt-2">Sign in to manage prayer reports</p>
            </div>

            <form action="{{ route('login') }}" method="POST" @submit="handleSubmit"
                class="bg-white/5 border border-white/10 backdrop-blur-xl p-8 rounded-3xl space-y-6 shadow-2xl">

                @csrf

                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest text-slate-400 font-bold">Email Address</label>
                    <div class="relative group">
                        <i data-lucide="mail"
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 group-focus-within:text-indigo-400 transition-colors"></i>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            placeholder="admin@celz5.org"
                            class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-4 py-3.5 focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 outline-none transition-all placeholder:text-slate-700">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs uppercase tracking-widest text-slate-400 font-bold">Password</label>
                    <div class="relative group">
                        <i data-lucide="lock"
                            class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 group-focus-within:text-indigo-400 transition-colors"></i>
                        <input :type="showPassword ? 'text' : 'password'" name="password" required
                            placeholder="••••••••"
                            class="w-full bg-white/5 border border-white/10 rounded-xl pl-11 pr-12 py-3.5 focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-500/40 outline-none transition-all placeholder:text-slate-700">

                        <button type="button" @click="showPassword = !showPassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-500 hover:text-white transition-colors">
                            <i :data-lucide="showPassword ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" :disabled="loading"
                    class="w-full bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-500/20 transition-all flex items-center justify-center gap-3 group">
                    <template x-if="loading">
                        <div class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                    </template>
                    <span x-text="loading ? 'Authenticating...' : 'Access Dashboard'"></span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"
                        x-show="!loading"></i>
                </button>
            </form>

            <p class="text-center mt-8 text-[10px] uppercase tracking-[0.2em] text-slate-600 font-medium">
                &copy; {{ date('Y') }} Celz5 IT Department
            </p>
        </div>
    </div>

    <script>
        function loginForm() {
            return {
                loading: false,
                toasts: [],
                showPassword: false,

                init() {
                    lucide.createIcons();

                    // 1. Handle Laravel Validation Errors
                    @if (isset($errors) && $errors->any())
                        this.addToast("{{ $errors->first() }}", 'error');
                    @endif

                    // 2. Handle Flash Success Messages
                    @if (session('success'))
                        this.addToast("{{ session('success') }}", 'success');
                    @endif
                },

                addToast(message, type = 'info') {
                    const id = Date.now();
                    this.toasts.push({
                        id,
                        message,
                        type
                    });

                    // Re-init icons for newly added toasts
                    this.$nextTick(() => lucide.createIcons());

                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 4500);
                },

                handleSubmit() {
                    this.loading = true;
                    this.addToast('Verifying credentials...', 'info');
                }
            }
        }
    </script>
</body>

</html>
