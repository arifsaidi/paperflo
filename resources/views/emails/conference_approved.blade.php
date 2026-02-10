<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f7;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #1a1a2e;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            color: #ffffff;
            margin: 0;
            font-family: 'Georgia', serif;
            font-size: 24px;
        }

        .header span {
            color: #FF5722;
        }

        .content {
            padding: 40px;
        }

        .welcome-text {
            font-size: 18px;
            color: #1a1a2e;
            margin-bottom: 20px;
        }

        .credentials-box {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 6px;
            padding: 20px;
            margin: 25px 0;
        }

        .credential-item {
            margin-bottom: 10px;
        }

        .credential-label {
            font-weight: bold;
            color: #666;
            display: block;
            margin-bottom: 4px;
            font-size: 12px;
            text-transform: uppercase;
        }

        .credential-value {
            font-family: 'Monaco', 'Consolas', monospace;
            background: #fff;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: block;
            color: #333;
            font-size: 14px;
        }

        .btn {
            display: inline-block;
            background-color: #FF5722;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 20px;
            text-align: center;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #888;
            border-top: 1px solid #eee;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>PaperFlo<span>.</span></h1>
        </div>
        <div class="content">
            <h2 class="welcome-text">Your Conference Request is Approved!</h2>
            <p>Dear Organizer,</p>
            <p>We are pleased to inform you that your request to host <strong>{{ $conferenceName }}</strong> on PaperFlo
                has been approved.</p>

            <p>You have been assigned as the Conference Administrator. Below are your login credentials to access the
                committee dashboard where you can start setting up your conference.</p>

            <div class="credentials-box">
                <div class="credential-item">
                    <span class="credential-label">Login URL</span>
                    <span class="credential-value"><a href="{{ route('committee.login') }}"
                            style="color: #FF5722; text-decoration: none;">{{ route('committee.login') }}</a></span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Email</span>
                    <span class="credential-value">{{ $email }}</span>
                </div>
                <div class="credential-item">
                    <span class="credential-label">Password</span>
                    <span class="credential-value">{{ $password }}</span>
                </div>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('committee.login') }}" class="btn">Login to Dashboard</a>
            </div>

            <p style="margin-top: 30px; font-size: 14px; color: #666;">
                For security reasons, we recommend changing your password after your first login.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} PaperFlo. All rights reserved.
        </div>
    </div>
</body>

</html>