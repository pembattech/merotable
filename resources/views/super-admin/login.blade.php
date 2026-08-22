{{-- resources/views/super-admin/login.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Login — MeroTable</title>
    <style>
        :root {
            --bg: #0f172a;
            --card: #1e293b;
            --border: #334155;
            --text: #e2e8f0;
            --muted: #94a3b8;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --error: #f87171;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--text);
        }
        .card {
            width: 100%;
            max-width: 380px;
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        .logo {
            text-align: center;
            margin-bottom: 24px;
        }
        .logo h1 {
            font-size: 20px;
            margin: 0 0 4px;
        }
        .logo p {
            margin: 0;
            font-size: 13px;
            color: var(--muted);
        }
        label {
            display: block;
            font-size: 13px;
            margin-bottom: 6px;
            color: var(--muted);
        }
        input {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 16px;
            background: #0f172a;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            font-size: 14px;
        }
        input:focus {
            outline: none;
            border-color: var(--accent);
        }
        button {
            width: 100%;
            padding: 11px;
            background: var(--accent);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s;
        }
        button:hover { background: var(--accent-hover); }
        button:disabled { opacity: 0.6; cursor: not-allowed; }
        .error {
            display: none;
            background: rgba(248, 113, 113, 0.1);
            border: 1px solid rgba(248, 113, 113, 0.3);
            color: var(--error);
            font-size: 13px;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="logo">
            <h1>MeroTable</h1>
            <p>Super Admin Access</p>
        </div>

        <div class="error" id="errorBox"></div>

        <form id="loginForm">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required autocomplete="username">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" required autocomplete="current-password">

            <button type="submit" id="submitBtn">Sign In</button>
        </form>
    </div>

    <script>
        
        // TODO: Token storage in localStorage — simplest to wire up, but it's vulnerable to XSS. If merotable's super admin panel is sensitive (it usually is), consider switching to Sanctum's SPA cookie-based auth instead (httpOnly cookies via /sanctum/csrf-cookie), which is safer. I can rewrite the login page for that flow if you want.

        const form = document.getElementById('loginForm');
        const errorBox = document.getElementById('errorBox');
        const submitBtn = document.getElementById('submitBtn');

        // change this if your API is on a different host
        const API_BASE = '/api';

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            errorBox.style.display = 'none';
            submitBtn.disabled = true;
            submitBtn.textContent = 'Signing in...';

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            try {
                const res = await fetch(`${API_BASE}/super-admin/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email, password }),
                });

                const data = await res.json();

                if (!res.ok) {
                    throw new Error(data.message || 'Login failed');
                }

                // store token — swap for httpOnly cookie flow if you want stronger security
                localStorage.setItem('super_admin_token', data.token);
                localStorage.setItem('super_admin_user', JSON.stringify(data.user));

                window.location.href = '/super-admin/dashboard';

            } catch (err) {
                errorBox.textContent = err.message;
                errorBox.style.display = 'block';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Sign In';
            }
        });
    </script>

</body>
</html>
