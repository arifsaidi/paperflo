<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Home</title>
</head>

<body>
    <h2>Home</h2>

    @if(session('success'))
    <p style="color:green">{{ session('success') }}</p> @endif

    <p>user_type: <b>{{ session('user_type') ?? 'guest' }}</b></p>
    <p>user_id: <b>{{ session('user_id') ?? '-' }}</b></p>

    {{-- <a href="{{ route('submissions.index') }}">My Submissions</a> --}}
    <a href="{{ route('participant.conferences') }}">My Conferences (View Submissions via Conference)</a><br>
    <a href="{{ route('participant.profile.show') }}">My Details</a>

    @if(session()->has('user_type'))
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Logout</button>
        </form>
    @else
        <a href="{{ route('login.form') }}">Go to Login</a>
    @endif
</body>

</html>