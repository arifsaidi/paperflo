@extends('layouts.participant_horizontal')

@section('title', 'New Submission - ' . $conference->name)

@section('hero')
    <header class="hero">
        @if($conference->logo)
            <img src="{{ asset('storage/' . $conference->logo) }}" alt="Conference Logo"
                style="max-height: 100px; margin-bottom: 24px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
            <br>
        @endif
        <span class="status-badge">{{ $conference->conference_status }}</span>
        <h1>{{ $conference->name }}</h1>
    </header>
@endsection

@section('styles')
    <style>
        /* Page Header */
        .page-header {
            margin-bottom: 24px;
        }

        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1a1a2e;
        }

        .page-header p {
            color: #666;
            margin-top: 4px;
        }

        /* Main Content */
        .form-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #666;
            font-weight: 500;
            margin-bottom: 24px;
            font-size: 0.9rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: #FF5722;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 24px;
            color: #1a1a2e;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 24px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: #1a1a2e;
        }

        .form-input,
        .form-textarea,
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            font-family: inherit;
            font-size: 0.95rem;
            transition: all 0.2s;
        }

        .form-input:focus,
        .form-textarea:focus,
        .form-select:focus {
            border-color: #FF5722;
            outline: none;
            box-shadow: 0 0 0 4px rgba(255, 87, 34, 0.1);
        }

        /* Authors */
        .coauthor-row {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 12px;
            margin-bottom: 12px;
            align-items: center;
        }

        .btn-add {
            background: #f1f3f5;
            color: #495057;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
        }

        .btn-add:hover {
            background: #e9ecef;
        }

        .btn-remove {
            background: #fff5f5;
            color: #c62828;
            border: none;
            padding: 12px;
            border-radius: 8px;
            cursor: pointer;
        }

        /* Action Buttons */
        .action-container {
            display: flex;
            gap: 16px;
            margin-top: 40px;
        }

        .btn-submit {
            flex: 1;
            background: #FF5722;
            color: #fff;
            border: none;
            padding: 16px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-submit:hover {
            background: #e64a19;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 87, 34, 0.2);
        }

        .btn-draft {
            flex: 1;
            background: #f8f9fa;
            color: #1a1a2e;
            border: 1px solid #e0e0e0;
            padding: 16px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-draft:hover {
            background: #fff;
            border-color: #1a1a2e;
        }

        .alert-error {
            background: #fff5f5;
            color: #c62828;
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 0.9rem;
        }

        .alert-error ul {
            margin-left: 20px;
        }
    </style>
@endsection

@section('content')

    <div class="conference-nav">
        <div class="nav-container">
            <a href="{{ route('participant.conference.show', $conference->conference_id) }}" class="conf-nav-item">
                Conference Details
            </a>
            <a href="{{ route('participant.registration.show', $conference->conference_id) }}" class="conf-nav-item">
                My Registration
            </a>
            <a href="{{ route('conference.submissions.index', $conference->conference_id) }}" class="conf-nav-item active">
                My Submissions
            </a>
        </div>
    </div>

    <main class="container">
        <div class="form-container">
            <a href="{{ route('conference.submissions.index', $conference->conference_id) }}" class="back-link">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z" />
                </svg>
                Back to My Submissions
            </a>

            @if(session('error') || $errors->any())
                <div class="alert-error">
                    @if(session('error'))
                    <p>{{ session('error') }}</p> @endif
                    @if($errors->any())
                        <ul> @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
                    @endif
                </div>
            @endif

            <form method="POST" action="{{ route('submissions.store', $conference->conference_id) }}">
                @csrf

                <div class="card">
                    <h2 class="section-title">Author Information</h2>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div class="form-group">
                            <label class="form-label">Corresponding Author Name</label>
                            <input name="corresponding_author_name"
                                value="{{ old('corresponding_author_name', $participant->name ?? '') }}" class="form-input"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Corresponding Author Email</label>
                            <input name="corresponding_author_email"
                                value="{{ old('corresponding_author_email', $participant->email ?? '') }}"
                                class="form-input" required>
                        </div>
                    </div>

                    <div style="margin-top: 10px;">
                        <label class="form-label">Co-authors (Optional)</label>
                        <div id="coauthors">
                            @if(old('coauthor_name'))
                                @foreach(old('coauthor_name') as $index => $name)
                                    <div class="coauthor-row">
                                        <input name="coauthor_name[]" value="{{ $name }}" placeholder="Full Name"
                                            class="form-input">
                                        <input name="coauthor_email[]" value="{{ old('coauthor_email')[$index] }}"
                                            placeholder="Email Address" class="form-input">
                                        <button type="button" class="btn-remove" onclick="this.parentElement.remove()">
                                            <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                                <path
                                                    d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                                <path fill-rule="evenodd"
                                                    d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                            </svg>
                                        </button>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <button type="button" class="btn-add" onclick="addCoauthor()">+ Add More Authors</button>
                    </div>
                </div>

                <div class="card">
                    <h2 class="section-title">Submission Content</h2>

                    <div class="form-group">
                        <label class="form-label">Title</label>
                        <input name="title" value="{{ old('title') }}" class="form-input"
                            placeholder="Enter your paper title" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Abstract</label>
                        <textarea name="abstract_text" class="form-textarea" style="height: 200px;"
                            placeholder="Paste your abstract here..." required>{{ old('abstract_text') }}</textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                        <div class="form-group">
                            <label class="form-label">Track</label>
                            <select name="track_id" class="form-select" required>
                                <option value="">-- Select Track --</option>
                                @foreach($tracks as $t)
                                    <option value="{{ $t->track_id }}" {{ old('track_id') == $t->track_id ? 'selected' : '' }}>
                                        {{ $t->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Keywords</label>
                            <input name="keywords" value="{{ old('keywords') }}" class="form-input"
                                placeholder="e.g. AI, Healthcare, Privacy" required>
                        </div>
                    </div>
                </div>

                <div class="action-container">
                    <button type="submit" name="action" value="draft" class="btn-draft">Save as Draft</button>
                    <button type="submit" name="action" value="submit" class="btn-submit"
                        onclick="return confirm('Ready to submit your abstract?');">
                        Submit Abstract
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        function addCoauthor() {
            const container = document.getElementById('coauthors');
            const row = document.createElement('div');
            row.className = 'coauthor-row';
            row.innerHTML = `
                                            <input name="coauthor_name[]" placeholder="Full Name" class="form-input">
                                            <input name="coauthor_email[]" placeholder="Email Address" class="form-input">
                                            <button type="button" class="btn-remove" onclick="this.parentElement.remove()">
                                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg>
                                            </button>
                                        `; container.appendChild(row);
        }
    </script>
@endsection