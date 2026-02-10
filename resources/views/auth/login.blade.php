<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - PaperFlo.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@700;800&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            height: 100vh;
            display: flex;
            background-color: #fff;
        }

        /* Left Side - Form */
        .login-section {
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px 10%;
            background: #fff;
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 12px;
        }

        .login-header p {
            color: #666;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
            margin-bottom: 8px;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            color: #333;
            transition: border-color 0.2s;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #4E342E;
            /* Dark brown accent */
        }

        .form-control:placeholder {
            color: #aaa;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #4E342E;
            /* Dark brown from screenshot */
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 8px;
            font-family: inherit;
        }

        .btn-submit:hover {
            background: #3e2723;
        }

        .register-link {
            text-align: center;
            margin-top: 24px;
            font-size: 14px;
            color: #666;
        }

        .register-link a {
            color: #2962ff;
            /* Blue for link */
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            border-left: 4px solid #c62828;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14px;
            border-left: 4px solid #2e7d32;
        }

        /* Right Side - Image */
        .image-section {
            width: 50%;
            background-image: url('{{ asset("images/login-bg.jpg") }}');
            background-size: cover;
            background-position: center;
            position: relative;
            /* Full height but with rounded left corners only */
            border-top-left-radius: 40px;
            border-bottom-left-radius: 40px;
            /* No margin means full touch to edges except for the rounded corners against white body */
        }

        @media (max-width: 900px) {
            .image-section {
                display: none;
            }

            .login-section {
                width: 100%;
                padding: 40px 5%;
            }
        }
    </style>
</head>

<body>
    <div class="login-section">
        <div style="max-width: 400px; margin: 0 auto; width: 100%;">
            <div class="login-header">
                <h1>Welcome back!</h1>
                <p>Enter your Credentials to access your account</p>
            </div>

            @if(session('error'))
                <div class="alert-error">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('login.do') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required
                        autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password"
                        required>
                </div>

                <button type="submit" class="btn-submit">Sign in</button>

                <div class="register-link">
                    Don't have an account? <a href="{{ route('participant.register.form') }}">Sign Up</a>
                </div>
            </form>
        </div>
    </div>

    <div class="image-section"></div>
</body>

</html>