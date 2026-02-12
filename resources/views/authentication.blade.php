<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multi-User Login System</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=DM+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">

    <style>
        body {
            display: none;
        }
    </style>

    <script src="{{ asset('js/auth-guard.js') }}"></script>
    <script>
        requireGuest();
    </script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #0EA5E9;
            --primary-dark: #0284C7;
            --secondary: #3B82F6;
            --dark: #0a0a0a;
            --light: #ffffff;
            --gray: #94A3B8;
            --success: #10B981;
            --error: #EF4444;
            --warning: #F59E0B;
            --surface: rgba(255, 255, 255, 0.05);
            --border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #000000 0%, #0a1929 50%, #001e3c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, transparent 70%);
            top: -200px;
            left: -200px;
            animation: float 20s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%);
            bottom: -150px;
            right: -150px;
            animation: float 15s ease-in-out infinite reverse;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0, 0);
            }

            50% {
                transform: translate(30px, 30px);
            }
        }

        .container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 450px;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            background: rgba(10, 10, 10, 0.7);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px 40px;
            border: 1px solid rgba(14, 165, 233, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3), 0 0 60px rgba(14, 165, 233, 0.1);
            position: relative;
        }

        .form-wrapper {
            position: relative;
        }

        .form-section {
            display: none;
            animation: fadeIn 0.4s ease-out;
        }

        .form-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 32px;
            font-weight: 700;
            color: var(--light);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            color: var(--gray);
            font-size: 15px;
            margin-bottom: 32px;
        }

        /* User Type Selector */
        .user-type-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 32px;
        }

        .user-type-btn {
            padding: 12px 16px;
            background: rgba(14, 165, 233, 0.05);
            border: 1px solid rgba(14, 165, 233, 0.2);
            border-radius: 12px;
            color: var(--gray);
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .user-type-btn svg {
            width: 24px;
            height: 24px;
            opacity: 0.6;
        }

        .user-type-btn.active {
            background: rgba(14, 165, 233, 0.15);
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 0 20px rgba(14, 165, 233, 0.2);
        }

        .user-type-btn.active svg {
            opacity: 1;
        }

        .user-type-btn:hover:not(.active) {
            background: rgba(14, 165, 233, 0.1);
            border-color: var(--primary);
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            background: rgba(14, 165, 233, 0.05);
            border: 1px solid rgba(14, 165, 233, 0.2);
            border-radius: 12px;
            color: var(--light);
            font-size: 15px;
            font-family: 'DM Sans', sans-serif;
            transition: all 0.3s ease;
            outline: none;
        }

        input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        input:focus {
            background: rgba(14, 165, 233, 0.1);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.15);
            transform: translateY(-2px);
        }

        input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 24px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-right: 8px;
            cursor: pointer;
        }

        .checkbox-group label {
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 400;
            cursor: pointer;
        }

        button {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: var(--light);
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'DM Sans', sans-serif;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }

        button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        button:hover:not(:disabled)::before {
            left: 100%;
        }

        button:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(14, 165, 233, 0.4);
        }

        button:active:not(:disabled) {
            transform: translateY(0);
        }

        .loading {
            display: inline-block;
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
            margin-left: 8px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10B981;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #EF4444;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.15);
            border: 1px solid rgba(245, 158, 11, 0.3);
            color: #F59E0B;
        }

        .toggle-form {
            text-align: center;
            margin-top: 24px;
            color: var(--gray);
            font-size: 14px;
        }

        .toggle-form a {
            color: var(--light);
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .toggle-form a:hover {
            color: var(--primary);
        }

        .forgot-password {
            text-align: right;
            margin-top: -12px;
            margin-bottom: 24px;
        }

        .forgot-password a {
            color: var(--gray);
            font-size: 13px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--light);
        }

        .api-config {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .api-config-btn {
            background: rgba(14, 165, 233, 0.15);
            border: 1px solid rgba(14, 165, 233, 0.3);
            padding: 10px 16px;
            border-radius: 8px;
            color: var(--primary);
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .api-config-btn:hover {
            background: rgba(14, 165, 233, 0.25);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 28px 0;
            color: var(--gray);
            font-size: 13px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .divider span {
            padding: 0 16px;
        }

        @media (max-width: 480px) {
            .card {
                padding: 36px 28px;
            }

            h2 {
                font-size: 28px;
            }

            .user-type-selector {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="api-config">
        <button class="api-config-btn" onclick="toggleApiConfig()">⚙️ API Config</button>
    </div>

    <div class="container">
        <div class="card">
            <div class="form-wrapper">
                <!-- Login Form -->
                <div class="form-section active" id="loginForm">
                    <h2>Welcome back</h2>
                    <p class="subtitle">Select your role and sign in to continue</p>

                    <!-- User Type Selector -->
                    <div class="user-type-selector">
                        <div class="user-type-btn active" data-type="staff" onclick="selectUserType('staff')">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Staff
                        </div>
                        <div class="user-type-btn" data-type="restaurant" onclick="selectUserType('restaurant')">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                            Restaurant
                        </div>
                    </div>

                    <div id="loginAlert"></div>

                    <form onsubmit="handleLogin(event)" id="loginFormElement">
                        <div class="form-group">
                            <label for="login-email">Email</label>
                            <input type="email" id="login-email" placeholder="you@example.com" required>
                        </div>

                        <div class="form-group">
                            <label for="login-password">Password</label>
                            <input type="password" id="login-password" placeholder="Enter your password" required>
                        </div>

                        <div class="forgot-password">
                            <a href="#" onclick="return false;">Forgot password?</a>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="remember-me">
                            <label for="remember-me">Remember me for 30 days</label>
                        </div>

                        <button type="submit" id="loginBtn">
                            <span id="loginBtnText">Sign in</span>
                        </button>
                    </form>

                    <div class="toggle-form" id="registerToggle">
                        Don't have a restaurant account? <a onclick="toggleForm('register')">Register</a>
                    </div>
                </div>

                <!-- Restaurant Register Form -->
                <div class="form-section" id="registerForm">
                    <h2>Register Restaurant</h2>
                    <p class="subtitle">Create your restaurant account to get started</p>

                    <div id="registerAlert"></div>

                    <form onsubmit="handleRegister(event)" id="registerFormElement">
                        <div class="form-group">
                            <label for="register-name">Restaurant Name</label>
                            <input type="text" id="register-name" placeholder="Your Restaurant Name" required>
                        </div>

                        <div class="form-group">
                            <label for="register-email">Email</label>
                            <input type="email" id="register-email" placeholder="restaurant@example.com" required>
                        </div>

                        <div class="form-group">
                            <label for="register-phone">Phone Number</label>
                            <input type="tel" id="register-phone" placeholder="+1234567890" required>
                        </div>

                        <div class="form-group">
                            <label for="register-address">Address</label>
                            <input type="text" id="register-address" placeholder="Restaurant Address" required>
                        </div>

                        <div class="form-group">
                            <label for="register-password">Password</label>
                            <input type="password" id="register-password" placeholder="Create a strong password"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="register-confirm-password">Confirm Password</label>
                            <input type="password" id="register-confirm-password" placeholder="Confirm your password"
                                required>
                        </div>

                        <div class="checkbox-group">
                            <input type="checkbox" id="terms" required>
                            <label for="terms">I agree to the Terms and Privacy Policy</label>
                        </div>

                        <button type="submit" id="registerBtn">
                            <span id="registerBtnText">Create account</span>
                        </button>
                    </form>

                    <div class="toggle-form">
                        Already have an account? <a onclick="toggleForm('login')">Sign in</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Configuration
        let API_BASE_URL = 'http://localhost:8000/api/v1/auth';
        let currentUserType = 'staff'; // Default user type


        function selectUserType(type) {
            currentUserType = type;

            // Update button states
            document.querySelectorAll('.user-type-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-type="${type}"]`).classList.add('active');

            // Show/hide register toggle based on user type
            const registerToggle = document.getElementById('registerToggle');
            if (type === 'restaurant') {
                registerToggle.style.display = 'block';
            } else {
                registerToggle.style.display = 'none';
            }
        }

        function toggleForm(formType) {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');

            if (formType === 'register') {
                loginForm.classList.remove('active');
                registerForm.classList.add('active');
            } else {
                registerForm.classList.remove('active');
                loginForm.classList.add('active');
            }

            // Clear alerts
            clearAlerts();
        }

        function showAlert(elementId, message, type) {
            const alertDiv = document.getElementById(elementId);
            let icon = '';

            if (type === 'success') {
                icon = '✓';
            } else if (type === 'error') {
                icon = '✕';
            } else if (type === 'warning') {
                icon = '⚠';
            }

            alertDiv.innerHTML = `
                <div class="alert alert-${type}">
                    <span style="font-size: 18px; font-weight: bold;">${icon}</span>
                    <span>${message}</span>
                </div>
            `;
        }

        function clearAlerts() {
            document.getElementById('loginAlert').innerHTML = '';
            document.getElementById('registerAlert').innerHTML = '';
        }

        function setButtonLoading(buttonId, isLoading) {
            const btn = document.getElementById(buttonId);
            const btnText = document.getElementById(buttonId + 'Text');

            if (isLoading) {
                btn.disabled = true;
                btnText.innerHTML = 'Processing<span class="loading"></span>';
            } else {
                btn.disabled = false;
                btnText.textContent = buttonId === 'loginBtn' ? 'Sign in' : 'Create account';
            }
        }

        async function handleLogin(event) {
            event.preventDefault();
            clearAlerts();

            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;
            const rememberMe = document.getElementById('remember-me').checked;

            // Determine the endpoint based on user type
            let endpoint = '';
            if (currentUserType === 'restaurant') {
                endpoint = `${API_BASE_URL}/restaurant/login`;
            } else if (currentUserType === 'staff') {
                endpoint = `${API_BASE_URL}/staff/login`;
            }

            setButtonLoading('loginBtn', true);

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        email: email,
                        password: password,
                        remember: rememberMe
                    })
                });

                const data = await response.json();

                if (response.ok) {

                    if (currentUserType === 'restaurant') {
                        localStorage.setItem('auth_token', data.token);
                        localStorage.setItem('user_type', currentUserType);
                        localStorage.setItem('user_name', data.data.name);
                        localStorage.setItem('restro_url', data.data.slug);
                    } else{
                        localStorage.setItem('auth_token', data.token);
                        localStorage.setItem('user_type', currentUserType);
                        localStorage.setItem('user_name', data.staff.name);
                        localStorage.setItem('user_role', data.staff.role);
                        localStorage.setItem('restro_name', data.staff.restaurant.name);
                        localStorage.setItem('restro_url', data.staff.restaurant.slug);
                    }

                    showAlert('loginAlert', `Login successful! Welcome ${currentUserType}`, 'success');

                    // Redirect based on user type
                    setTimeout(() => {
                        if (currentUserType === 'restaurant') {
                            window.location.href = '/restaurant/dashboard';
                        } else if (currentUserType === 'staff') {
                            window.location.href = '/staff/dashboard';
                        }
                    }, 1000);
                } else {
                    showAlert('loginAlert', data.message || 'Login failed. Please check your credentials.', 'error');
                }
            } catch (error) {
                console.error('Login error:', error);
                showAlert('loginAlert', 'Network error. Please check your connection and API URL.', 'error');
            } finally {
                setButtonLoading('loginBtn', false);
            }
        }

        async function handleRegister(event) {
            event.preventDefault();
            clearAlerts();

            const name = document.getElementById('register-name').value;
            const email = document.getElementById('register-email').value;
            const phone = document.getElementById('register-phone').value;
            const address = document.getElementById('register-address').value;
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-confirm-password').value;

            // Validate passwords match
            if (password !== confirmPassword) {
                showAlert('registerAlert', 'Passwords do not match!', 'error');
                return;
            }

            // Validate password strength
            if (password.length < 8) {
                showAlert('registerAlert', 'Password must be at least 8 characters long!', 'error');
                return;
            }

            setButtonLoading('registerBtn', true);

            try {
                const response = await fetch(`${API_BASE_URL}/restaurant/register`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        name: name,
                        email: email,
                        phone: phone,
                        address: address,
                        password: password,
                        password_confirmation: confirmPassword
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert('registerAlert', 'Registration successful! Redirecting to login...', 'success');

                    // Clear form
                    document.getElementById('registerFormElement').reset();

                    // Switch to login after 2 seconds
                    setTimeout(() => {
                        toggleForm('login');
                        selectUserType('restaurant');
                    }, 2000);
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        const errorMessages = Object.values(data.errors).flat().join(', ');
                        showAlert('registerAlert', errorMessages, 'error');
                    } else {
                        showAlert('registerAlert', data.message || 'Registration failed. Please try again.', 'error');
                    }
                }
            } catch (error) {
                console.error('Registration error:', error);
                showAlert('registerAlert', 'Network error. Please check your connection and API URL.', 'error');
            } finally {
                setButtonLoading('registerBtn', false);
            }
        }

        // // Helper function to make authenticated API calls
        // async function authenticatedFetch(url, options = {}) {
        //     const token = localStorage.getItem('auth_token');

        //     const headers = {
        //         'Content-Type': 'application/json',
        //         'Accept': 'application/json',
        //         ...options.headers
        //     };

        //     if (token) {
        //         headers['Authorization'] = `Bearer ${token}`;
        //     }

        //     return fetch(url, {
        //         ...options,
        //         headers
        //     });
        // }

        // // Example usage for authenticated requests
        // async function exampleAuthenticatedRequest() {
        //     try {
        //         const response = await authenticatedFetch('http://localhost:8000/api/v1/owner/restaurant/activities');
        //         const data = await response.json();

        //         if (response.status === 401) {
        //             // Token expired or invalid
        //             localStorage.clear();
        //             window.location.href = '/login';
        //         }

        //         return data;
        //     } catch (error) {
        //         console.error('API Error:', error);
        //     }
        // }

        // Logout function
        async function logout() {
            const token = localStorage.getItem('auth_token');

            if (token) {
                try {
                    await fetch(`${API_BASE_URL}/logout`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                } catch (error) {
                    console.error('Logout error:', error);
                }
            }

            // Clear all stored data
            localStorage.clear();
            window.location.href = '/';
        }

        // // Initialize
        // document.addEventListener('DOMContentLoaded', function() {
        //     // Check if user is already logged in
        //     const token = localStorage.getItem('auth_token');
        //     if (token) {
        //         const userType = localStorage.getItem('user_type');
        //         console.log('User already logged in as:', userType);
        //         // Optionally redirect to dashboard

        //         if (userType === 'restaurant') {
        //             window.location.href = '/restaurant/dashboard';
        //         } else if (userType === 'staff') {
        //             window.location.href = '/staff/dashboard';
        //         }
        //     }
        // });
    </script>
</body>

</html>
