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
        :root {
            --bg:            #faf6ef;
            --bg-card:       rgba(255,255,255,0.78);
            --border:        rgba(180,140,90,0.18);
            --border-md:     rgba(180,140,90,0.28);
            --border-strong: rgba(180,140,90,0.45);
            --text-1:        #2c1f0f;
            --text-2:        #7a5230;
            --text-3:        #b89a72;
            --text-4:        #d4b896;
            --amber:         #b8894a;
            --amber-dk:      #7a5230;
            --amber-lt:      rgba(184,137,74,0.10);
            --shadow-md:     0 4px 24px rgba(160,120,70,0.11);
            --shadow-lg:     0 8px 40px rgba(160,120,70,0.14);
        }

        [x-cloak] { display: none !important; }

        body {
            background-color: var(--bg);
            color: var(--text-1);
            font-family: 'Instrument Sans', sans-serif;
            -webkit-font-smoothing: antialiased;
            overflow: hidden;
        }

        /* Ambient blobs */
        .blob { position: absolute; border-radius: 50%; pointer-events: none; }

        /* Card */
        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border-md);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            backdrop-filter: blur(14px);
            padding: 40px;
        }

        /* Field */
        .field-wrap { position: relative; }
        .field-icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: var(--text-3); pointer-events: none;
            width: 16px; height: 16px;
            transition: color 0.2s;
        }
        .field-wrap:focus-within .field-icon { color: var(--amber-dk); }

        input.field {
            width: 100%;
            background: rgba(250,246,239,0.8);
            border: 1px solid var(--border-md);
            border-radius: 12px;
            padding: 13px 16px 13px 42px;
            font-size: 14px;
            color: var(--text-1);
            font-family: 'Instrument Sans', sans-serif;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input.field::placeholder { color: var(--text-4); }
        input.field:focus {
            border-color: var(--amber);
            box-shadow: 0 0 0 3px rgba(184,137,74,0.13);
        }
        input.field.pr { padding-right: 44px; }

        .eye-btn {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer;
            color: var(--text-3); transition: color 0.15s;
            padding: 0; display: flex;
        }
        .eye-btn:hover { color: var(--amber-dk); }

        /* Label */
        .field-label {
            display: block;
            font-size: 10px; font-weight: 700;
            letter-spacing: 0.15em; text-transform: uppercase;
            color: var(--text-3); margin-bottom: 8px;
        }

        /* Submit button */
        .btn-submit {
            width: 100%;
            background: var(--amber-dk);
            color: #fdf8f2;
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 14px; font-weight: 700;
            font-family: 'Instrument Sans', sans-serif;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 4px 18px rgba(122,82,48,0.24);
            transition: background 0.18s, box-shadow 0.18s, opacity 0.18s;
        }
        .btn-submit:hover:not(:disabled) { background: #8f6238; box-shadow: 0 5px 22px rgba(122,82,48,0.3); }
        .btn-submit:disabled { opacity: 0.55; cursor: not-allowed; }

        @keyframes spin { to { transform: rotate(360deg); } }
        .spinner { animation: spin 0.75s linear infinite; }

        /* Toast */
        .toast-wrap { position: fixed; top: 24px; right: 24px; z-index: 200; display: flex; flex-direction: column; gap: 10px; }
        .toast {
            display: flex; align-items: center; gap: 11px;
            padding: 13px 18px;
            background: rgba(255,255,255,0.97);
            border: 1px solid var(--border-strong);
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            min-width: 280px; max-width: 360px;
            font-size: 13px; font-weight: 600;
            color: var(--text-1);
        }
        .toast-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .toast-dot.error   { background: #a03030; }
        .toast-dot.success { background: #5a8a5a; }
        .toast-dot.info    { background: var(--amber); }
    </style>
</head>
<body x-data="loginForm()">

    {{-- Ambient blobs --}}
    <div style="position:fixed;inset:0;pointer-events:none;overflow:hidden;z-index:0;">
        <div class="blob" style="top:-10%;left:-8%;width:48%;height:48%;background:radial-gradient(circle,rgba(214,174,120,0.17) 0%,transparent 70%);"></div>
        <div class="blob" style="bottom:-10%;right:-8%;width:44%;height:44%;background:radial-gradient(circle,rgba(188,145,100,0.13) 0%,transparent 70%);"></div>
        <div class="blob" style="top:38%;left:52%;width:34%;height:34%;background:radial-gradient(circle,rgba(230,200,160,0.10) 0%,transparent 70%);"></div>
    </div>

    {{-- Toasts --}}
    <div class="toast-wrap">
        <template x-for="toast in toasts" :key="toast.id">
            <div class="toast"
                 x-transition:enter="transition ease-out duration-250"
                 x-transition:enter-start="opacity-0 translate-x-4"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-180"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="toast-dot" :class="toast.type"></div>
                <span x-text="toast.message"></span>
            </div>
        </template>
    </div>

    {{-- Login form --}}
    <div class="min-h-screen flex items-center justify-center p-6" style="position:relative;z-index:1;">
        <div class="w-full" style="max-width:420px;">

            {{-- Header --}}
            <div class="text-center" style="margin-bottom:28px;">
                <div style="display:inline-flex;align-items:center;justify-content:center;width:60px;height:60px;border-radius:16px;background:var(--amber-lt);border:1px solid var(--border-md);margin-bottom:16px;">
                    <img src="/images/lz5.png" alt="Logo" style="width:38px;height:38px;object-fit:contain;">
                </div>
                <h1 style="font-size:22px;font-weight:700;color:var(--text-1);margin:0 0 6px;">Admin Portal</h1>
                <p style="font-size:13px;color:var(--text-3);margin:0;">Sign in to manage reports</p>
            </div>

            {{-- Card --}}
            <form action="{{ route('login') }}" method="POST" @submit="handleSubmit" class="login-card">
                @csrf

                <div style="display:flex;flex-direction:column;gap:20px;">

                    {{-- Email --}}
                    <div>
                        <label class="field-label">Email Address</label>
                        <div class="field-wrap">
                            <i data-lucide="mail" class="field-icon"></i>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                   placeholder="admin@celz5.org" class="field">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="field-label">Password</label>
                        <div class="field-wrap">
                            <i data-lucide="lock" class="field-icon"></i>
                            <input :type="showPassword ? 'text' : 'password'" name="password" required
                                   placeholder="••••••••" class="field pr">
                            <button type="button" @click="showPassword = !showPassword" class="eye-btn">
                                <i :data-lucide="showPassword ? 'eye-off' : 'eye'" style="width:15px;height:15px;"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Submit --}}
                    <button type="submit" :disabled="loading" class="btn-submit" style="margin-top:4px;">
                        <template x-if="loading">
                            <svg class="spinner" style="width:16px;height:16px;" viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="10" stroke="rgba(255,255,255,0.3)" stroke-width="3"/>
                                <path d="M12 2a10 10 0 0 1 10 10" stroke="#fdf8f2" stroke-width="3" stroke-linecap="round"/>
                            </svg>
                        </template>
                        <span x-text="loading ? 'Authenticating…' : 'Access Dashboard'"></span>
                        <i data-lucide="arrow-right" style="width:15px;height:15px;" x-show="!loading"></i>
                    </button>
                </div>
            </form>

            <p style="text-align:center;margin-top:28px;font-size:10px;letter-spacing:0.18em;text-transform:uppercase;color:var(--text-4);font-weight:600;">
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

                    @if (isset($errors) && $errors->any())
                        this.addToast("{{ $errors->first() }}", 'error');
                    @endif

                    @if (session('success'))
                        this.addToast("{{ session('success') }}", 'success');
                    @endif
                },

                addToast(message, type = 'info') {
                    const id = Date.now();
                    this.toasts.push({ id, message, type });
                    this.$nextTick(() => lucide.createIcons());
                    setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 4500);
                },

                handleSubmit() {
                    this.loading = true;
                    this.addToast('Verifying credentials…', 'info');
                }
            }
        }
    </script>
</body>
</html>