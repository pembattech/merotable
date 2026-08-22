
<script>
    const SA_API_BASE = '/api';
    const SA_TOKEN_KEY = 'super_admin_token';
    const SA_USER_KEY = 'super_admin_user';

    function saGetToken() {
        return localStorage.getItem(SA_TOKEN_KEY);
    }

    function saLogout(redirect = true) {
        localStorage.removeItem(SA_TOKEN_KEY);
        localStorage.removeItem(SA_USER_KEY);
        if (redirect) window.location.href = '{{ route('super-admin.login-page') }}';
    }

    // wrapper for all authenticated API calls — use this everywhere instead of raw fetch
    async function saFetch(path, options = {}) {
        const token = saGetToken();

        const res = await fetch(`${SA_API_BASE}${path}`, {
            ...options,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`,
                ...(options.headers || {}),
            },
        });

        if (res.status === 401 || res.status === 403) {
            saLogout();
            throw new Error('Session expired');
        }

        return res;
    }

    (async function guard() {
        const token = saGetToken();
        console.log('Token:', token);

        if (!token) {
            saLogout();
            return;
        }

        try {
            const res = await saFetch('/super-admin/me');
            if (!res.ok) throw new Error('Invalid session');

            const data = await res.json();
            localStorage.setItem(SA_USER_KEY, JSON.stringify(data.user));

            document.body.style.visibility = 'visible';
        } catch (err) {
            saLogout();
        }
    })();
</script>
