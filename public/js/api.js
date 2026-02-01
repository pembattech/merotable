function apiTest() {

    const token = localStorage.getItem('auth_token');

    fetch('/api/user', {
        method: 'GET',
        headers: {
            'Authorization': `Bearer ${token}`,
            'Accept': 'application/json'
        }
    })
        .then(res => {
            if (res.status === 401) {
                localStorage.clear();
                window.location.replace('/auth');
                return;
            }
            return res.json();
        })
        .then(user => {
            console.log(user);
        });

}
