@extends('layouts.committee')

@section('title', 'Conference Contents')

@section('styles')
    <style>
        .page-header {
            margin-bottom: 32px;
        }

        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .page-header p {
            color: #666;
            font-size: 15px;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-textarea {
            width: 100%;
            padding: 16px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 15px;
            transition: all 0.2s;
            background: #fff;
            resize: vertical;
            min-height: 400px;
        }

        .form-textarea:focus {
            border-color: #FF5722;
            outline: none;
            box-shadow: 0 0 0 4px rgba(255, 87, 34, 0.1);
        }

        .btn-save {
            background: #FF5722;
            color: #fff;
            border: none;
            padding: 14px 28px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-save:hover {
            background: #e64a19;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 87, 34, 0.2);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 600;
            font-size: 14px;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1>Conference Contents</h1>
        <p>Edit the main descriptive content and landing page text for <strong>{{ $conference->name }}</strong></p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('conference.contents.update', $conference->conference_id) }}">
        @csrf

        <div class="card">
            <div class="card-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                </svg>
                Main Description & Call for Papers
            </div>

            <p style="color: #666; font-size: 14px; margin-bottom: 16px;">
                This content will be displayed on the conference's public landing page. You can use standard text or HTML
                formatting.
            </p>

            <div class="form-group">
                <textarea name="contents" class="form-textarea"
                    placeholder="Describe your conference, including tracks, submission guidelines, and key speakers...">{{ old('contents', $conference->contents) }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 24px;">
                <button type="submit" class="btn-save">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" />
                        <polyline points="17 21 17 13 7 13 7 21" />
                        <polyline points="7 3 7 8 15 8" />
                    </svg>
                    Save Description
                </button>
            </div>
        </div>
    </form>
@endsection