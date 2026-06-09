<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zammar Valley | Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-accent: #3b82f6;
            --pure-white: #ffffff;
            --soft-white: rgba(255,255,255,0.85);
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(rgba(15,23,42,0.55), rgba(15,23,42,0.55)),
                        url('https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        .top-header {
            padding: 18px 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(15,23,42,0.35);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .brand-text { color: var(--pure-white); font-weight: 700; font-size: 1.35rem; }
        .status-badge { display: inline-flex; align-items: center; gap: 6px; color: var(--soft-white); font-size: 0.8rem; }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: rgba(15,23,42,0.78);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.13);
            border-radius: 28px;
            padding: 44px 48px;
            box-shadow: 0 40px 80px rgba(0,0,0,0.5);
        }
        .login-card h2 { color: var(--pure-white); font-weight: 700; font-size: 1.5rem; margin-bottom: 6px; }
        .login-card .subtitle { color: rgba(255,255,255,0.45); font-size: 0.85rem; line-height: 1.5; }
        .form-label {
            color: rgba(255,255,255,0.7);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1.1px;
            font-weight: 700;
            margin-bottom: 8px;
            display: block;
        }
        .input-wrapper { position: relative; margin-bottom: 22px; }
        .input-icon {
            position: absolute;
            left: 17px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255,255,255,0.4);
            font-size: 14px;
            pointer-events: none;
            z-index: 10;
        }
        .form-control {
            background: rgba(255,255,255,0.08) !important;
            border: 1.5px solid rgba(255,255,255,0.15) !important;
            color: var(--pure-white) !important;
            height: 52px;
            padding-left: 46px;
            border-radius: 14px;
            font-size: 0.92rem;
            transition: border-color .2s, background .2s;
        }
        .form-control:focus {
            background: rgba(255,255,255,0.12) !important;
            border-color: rgba(59,130,246,0.7) !important;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15) !important;
        }
        .form-control::placeholder { color: rgba(255,255,255,0.3); }
        .btn-auth {
            background: var(--pure-white);
            color: #0f172a;
            border: none;
            height: 52px;
            border-radius: 14px;
            font-weight: 800;
            font-size: 0.88rem;
            letter-spacing: .8px;
            width: 100%;
            margin-top: 6px;
            transition: background .2s, color .2s, transform .1s;
            cursor: pointer;
        }
        .btn-auth:hover { background: var(--primary-accent); color: var(--pure-white); }
        .btn-auth:active { transform: scale(.98); }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: rgba(255,255,255,0.45);
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 600;
            margin-top: 20px;
            transition: color .15s;
        }
        .back-link:hover { color: var(--primary-accent); }
        .card-alert {
            border-radius: 12px;
            padding: 12px 16px;
            margin-bottom: 22px;
            font-size: 0.82rem;
            font-weight: 600;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .card-alert-error { background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); color: #fca5a5; }
        .card-alert-success { background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); color: #6ee7b7; }
        .card-alert ul { margin: 5px 0 0 14px; padding: 0; font-size: 0.8rem; }

        /* Icon box above title */
        .icon-box {
            width: 56px; height: 56px;
            background: rgba(59,130,246,0.15);
            border: 1px solid rgba(59,130,246,0.3);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            font-size: 22px; color: #60a5fa;
        }
        .bottom-footer {
            padding: 22px;
            text-align: center;
            color: rgba(255,255,255,0.35);
            font-size: 0.78rem;
            background: rgba(15,23,42,0.4);
            border-top: 1px solid rgba(255,255,255,0.08);
            margin-top: auto;
        }
    </style>
</head>
<body>

    <header class="top-header">
        <div class="brand-text">
            <i class="bi bi-mountain-fill text-primary me-2"></i>Zammar Valley
        </div>
        <div class="status-badge">
            <i class="bi bi-circle-fill text-success" style="font-size:7px;"></i>
            System Secured
        </div>
    </header>

    <main class="flex-grow-1 d-flex align-items-center justify-content-center p-3">
        <div class="login-card">

            {{-- Icon + Heading --}}
            <div class="text-center mb-4">
                <div class="icon-box">
                    <i class="bi bi-key-fill"></i>
                </div>
                <h2>Forgot Password?</h2>
                <p class="subtitle">Enter your registered email and we'll send you a password reset link.</p>
            </div>

            {{-- Success message (after email sent) --}}
            @if(session('status'))
            <div class="card-alert card-alert-success">
                <i class="bi bi-check-circle-fill" style="flex-shrink:0;margin-top:1px;font-size:15px;"></i>
                <span>{{ session('status') }}</span>
            </div>
            @endif

            {{-- Error messages --}}
            @if($errors->any())
            <div class="card-alert card-alert-error">
                <i class="bi bi-exclamation-triangle-fill" style="flex-shrink:0;margin-top:1px;"></i>
                <div>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('password.email') }}" method="POST" novalidate>
                @csrf
                <div class="input-wrapper">
                    <label for="email" class="form-label">Email Address</label>
                    <i class="bi bi-envelope-fill input-icon"></i>
                    <input type="email"
                           id="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="admin@zammar.com"
                           value="{{ old('email') }}"
                           autocomplete="email"
                           required>
                </div>

                <button type="submit" class="btn-auth">
                    <i class="bi bi-send-fill me-2"></i>SEND RESET LINK
                </button>
            </form>

            <div class="text-center">
                <a href="{{ route('login') }}" class="back-link">
                    <i class="bi bi-arrow-left"></i> Back to Login
                </a>
            </div>

        </div>
    </main>

    <footer class="bottom-footer">
        &copy; {{ date('Y') }} Zammar Valley Infrastructure &bull; All Rights Reserved
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
