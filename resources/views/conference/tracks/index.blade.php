@extends('layouts.committee')

@section('title', 'Conference Tracks')

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

        .track-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .track-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 24px;
            border-radius: 12px;
            border: 1px solid #eee;
            transition: all 0.2s;
        }

        .track-item:hover {
            border-color: #FF5722;
            background: #fffaf9;
        }

        .track-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }



        .btn-delete {
            color: #c62828;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-delete:hover {
            background: #ffebee;
        }

        .add-track-form {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .form-input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            font-size: 15px;
        }

        .form-input:focus {
            border-color: #FF5722;
            outline: none;
            box-shadow: 0 0 0 4px rgba(255, 87, 34, 0.1);
        }

        .btn-add {
            background: #FF5722;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-add:hover {
            background: #e64a19;
            transform: translateY(-2px);
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

        .alert-error {
            background: #fff5f5;
            color: #c62828;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <h1>Conference Tracks</h1>
        <p>Define and manage submission tracks for <strong>{{ $conference->name }}</strong></p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="card">
        <div class="card-title">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
            </svg>
            Active Tracks
        </div>

        <div class="track-list">
            @forelse($tracks as $track)
                <div class="track-item">
                    <div class="track-info">

                        <span style="font-weight: 600; color: #333;">{{ $track->name }}</span>
                    </div>
                    <form method="POST"
                        action="{{ route('conference.tracks.destroy', [$conference->conference_id, $track->track_id]) }}"
                        onsubmit="return confirm('Are you sure you want to delete this track?')">
                        @csrf
                        <button type="submit" class="btn-delete" title="Delete Track">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path
                                    d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            @empty
                <p style="text-align: center; color: #888; padding: 20px;">No tracks defined yet.</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('conference.tracks.store', $conference->conference_id) }}"
            class="add-track-form">
            @csrf
            <input name="name" class="form-input" placeholder="Enter new track name..." required>
            <button type="submit" class="btn-add">Add Track</button>
        </form>
    </div>
@endsection