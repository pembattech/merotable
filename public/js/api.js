function apiTest() {
    const token = localStorage.getItem('auth_token');

    fetch('/api/user', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    })
    .then(async res => {
        if (res.status === 401) {
            localStorage.clear();
            // window.location.replace('/auth');
            throw new Error('Unauthorized');
        }

        if (res.status === 403) {
            const data = await res.json();
            if (data.redirect_url) {
                window.location.replace(data.redirect_url); // Redirect to /pricing
            }
            throw new Error('Subscription expired');
        }

        return res.json();
    })
    .then(user => {
        console.log('User info:', user);
    })
    .catch(err => {
        console.warn('API error:', err.message);
    });
}