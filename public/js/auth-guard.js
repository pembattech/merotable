(function () {

    function showBody(user_type) {
        if (document.body) {
            if (userType !== 'staff') {
                document.body.style.display = 'flex';
            } else {
                document.body.style.display = 'block';
            }
        } else {
            document.addEventListener('DOMContentLoaded', function () {
                if (userType !== 'staff') {
                    document.body.style.display = 'flex';
                } else {
                    document.body.style.display = 'block';
                }
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
        showBody(user_type = userType);
    };

    window.requireAuth = function (redirectTo = '/auth') {
        if (!localStorage.getItem('auth_token')) {
            console.log("Redirecting to auth");
            window.location.replace(redirectTo);
            return;
        }

        showBody(user_type = userType);

    };

})();
