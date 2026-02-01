(function () {

    function showBody() {
        if (document.body) {
            document.body.style.display = 'flex';
        } else {
            document.addEventListener('DOMContentLoaded', function () {
                document.body.style.display = 'flex';
            });
        }
    }

    let userType = localStorage.getItem('user_type');
    if (userType === 'restaurant') {
        dashboardLink = 'restaurant/dashboard';
    } else if (userType === 'staff') {
        dashboardLink = 'staff/dashboard';
    } else {
        dashboardLink = '/';
    }


    window.requireGuest = function (redirectTo = dashboardLink) {
        if (localStorage.getItem('auth_token')) {
            window.location.replace(redirectTo);
            return;
        }
        document.body.style.display = 'block';
    };

    window.requireAuth = function (redirectTo = '/auth') {
        if (!localStorage.getItem('auth_token')) {
            console.log("Redirecting to auth");
            window.location.replace(redirectTo);
            return;
        }

        showBody();
    };

})();
