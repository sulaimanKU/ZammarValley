@extends('layouts.index')

@section('content')
@push('styles')
<style>

</style>
@endpush

<div class="email-wrap">

    {{-- Back button --}}
    <div style="margin-bottom: 16px;">
        <a href="{{ route('setting.view') }}" style="font-size:13px;font-weight:700;color:#1e3a8a;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
            ← Back to Settings
        </a>
    </div>

    <div class="email-card">
        <div class="email-header">
            <h2><i class="bi bi-envelope-fill" style="margin-right:8px;"></i>Email Configuration</h2>
            <p>Configure your SMTP settings for sending emails</p>
        </div>

        <div class="email-body">

            @if(session('success'))
            <div class="alert-success">✓ {{ session('success') }}</div>
            @endif

            @if(session('error'))
            <div class="alert-error">✗ {{ session('error') }}</div>
            @endif

            {{-- Gmail help box --}}
            <div style="background:#fffbeb;border:1.5px solid #fde68a;border-radius:12px;padding:14px 18px;margin-bottom:24px;">
                <div style="font-size:12px;font-weight:800;color:#92400e;margin-bottom:6px;">
                    <i class="bi bi-info-circle-fill"></i> Gmail Users
                </div>
                <div style="font-size:12px;color:#a16207;line-height:1.6;">
                    Use <strong>App Password</strong> not your regular Gmail password.<br>
                    Go to: Google Account → Security → 2-Step Verification → App Passwords
                </div>
            </div>

            <form method="POST" action="{{ route('settings.config.email') }}">
                @csrf

                <div class="row g-3">

                    {{-- SMTP Host --}}
                    <div class="col-md-8">
                        <label class="form-label-custom">SMTP Host</label>
                        <input type="text" name="mail_host" class="form-control"
                               value="{{ old('mail_host', $mail_host) }}"
                               placeholder="smtp.gmail.com">
                        <div class="hint">Gmail: smtp.gmail.com | Outlook: smtp.office365.com</div>
                        @error('mail_host')<div style="color:#dc2626;font-size:11px;margin-top:3px;">{{ $message }}</div>@enderror
                    </div>

                    {{-- Port --}}
                    <div class="col-md-4">
                        <label class="form-label-custom">Port</label>
                        <input type="number" name="mail_port" class="form-control"
                               value="{{ old('mail_port', $mail_port) }}"
                               placeholder="587">
                        <div class="hint">TLS: 587 | SSL: 465</div>
                        @error('mail_port')<div style="color:#dc2626;font-size:11px;margin-top:3px;">{{ $message }}</div>@enderror
                    </div>

                    {{-- Encryption --}}
                    <div class="col-md-4">
                        <label class="form-label-custom">Encryption</label>
                        <select name="mail_encryption" class="form-select">
                            <option value="tls"  {{ old('mail_encryption', $mail_encryption) == 'tls'  ? 'selected' : '' }}>TLS</option>
                            <option value="ssl"  {{ old('mail_encryption', $mail_encryption) == 'ssl'  ? 'selected' : '' }}>SSL</option>
                            <option value="none" {{ old('mail_encryption', $mail_encryption) == 'none' ? 'selected' : '' }}>None</option>
                        </select>
                        @error('mail_encryption')<div style="color:#dc2626;font-size:11px;margin-top:3px;">{{ $message }}</div>@enderror
                    </div>

                    {{-- Username --}}
                    <div class="col-md-8">
                        <label class="form-label-custom">Email Address (Username)</label>
                        <input type="email" name="mail_username" class="form-control"
                               value="{{ old('mail_username', $mail_username) }}"
                               placeholder="yourname@gmail.com">
                        @error('mail_username')<div style="color:#dc2626;font-size:11px;margin-top:3px;">{{ $message }}</div>@enderror
                    </div>

                    {{-- Password --}}
                    <div class="col-12">
                        <label class="form-label-custom">App Password</label>
                        <div style="position:relative;">
                            <input type="password" name="mail_password" id="mailPassword"
                                   class="form-control"
                                   value="{{ old('mail_password', $mail_password) }}"
                                   placeholder="Gmail app password">
                            <button type="button" onclick="togglePassword()"
                                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;">
                                <i class="bi bi-eye" id="eyeIcon"></i>
                            </button>
                        </div>
                        <div class="hint">Use App Password for Gmail — not your regular password</div>
                        @error('mail_password')<div style="color:#dc2626;font-size:11px;margin-top:3px;">{{ $message }}</div>@enderror
                    </div>

                    {{-- From Name --}}
                    <div class="col-12">
                        <label class="form-label-custom">From Name</label>
                        <input type="text" name="mail_from_name" class="form-control"
                               value="{{ old('mail_from_name', $mail_from_name) }}"
                               placeholder="Zamar Valley">
                        <div class="hint">This name appears in the recipient's inbox</div>
                        @error('mail_from_name')<div style="color:#dc2626;font-size:11px;margin-top:3px;">{{ $message }}</div>@enderror
                    </div>

                </div>

                <hr class="divider">

                <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check-lg"></i> Save Configuration
                    </button>
                    <button type="button" class="btn-test" onclick="testEmail()">
                        <i class="bi bi-send"></i> Send Test Email
                    </button>
                    <span id="testResult" style="font-size:12px;font-weight:700;"></span>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword() {
    const input = document.getElementById('mailPassword');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

function testEmail() {
    const result = document.getElementById('testResult');
    result.textContent = 'Sending...';
    result.style.color = '#94a3b8';

    fetch('{{ route("settings.email.test") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            result.textContent = '✓ Test email sent successfully!';
            result.style.color = '#16a34a';
        } else {
            result.textContent = '✗ Failed: ' + data.message;
            result.style.color = '#dc2626';
        }
    })
    .catch(() => {
        result.textContent = '✗ Request failed';
        result.style.color = '#dc2626';
    });
}
</script>
@endpush
