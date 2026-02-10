@extends('layouts.committee')

@section('title', 'Conference Deadlines')

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

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 15px;
            transition: all 0.2s;
            background: #fff;
        }

        .form-input:focus {
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
        <h1>Conference Deadlines</h1>
        <p>Set and update important dates for <strong>{{ $conference->name }}</strong></p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('conference.deadlines.update', $conference->conference_id) }}">
        @csrf

        <div class="card">
            <div class="card-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
                Submission Deadlines
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Abstract Submission Deadline</label>
                    <input type="datetime-local" name="abstract_deadline" class="form-input"
                        value="{{ old('abstract_deadline', $deadline ? date('Y-m-d\TH:i', strtotime($deadline->abstract_deadline)) : '') }}"
                        required>
                </div>
                <div class="form-group">
                    <label class="form-label">Full Paper Submission Deadline</label>
                    <input type="datetime-local" name="fullpaper_deadline" class="form-input"
                        value="{{ old('fullpaper_deadline', $deadline ? date('Y-m-d\TH:i', strtotime($deadline->fullpaper_deadline)) : '') }}"
                        required>
                </div>
                <div class="form-group">
                    <label class="form-label">Camera-ready Paper Deadline</label>
                    <input type="datetime-local" name="cameraready_paper_deadline" class="form-input"
                        value="{{ old('cameraready_paper_deadline', $deadline ? date('Y-m-d\TH:i', strtotime($deadline->cameraready_paper_deadline)) : '') }}"
                        required>
                </div>
                <div class="form-group">
                    <label class="form-label">Review Completion Deadline</label>
                    <input type="datetime-local" name="review_deadline" class="form-input"
                        value="{{ old('review_deadline', $deadline ? date('Y-m-d\TH:i', strtotime($deadline->review_deadline)) : '') }}"
                        required>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                    <circle cx="8.5" cy="7" r="4" />
                    <path d="M20 8v6M23 11h-6" />
                </svg>
                Registration & Payment Deadlines
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Participant Registration Deadline</label>
                    <input type="datetime-local" name="participant_registration_deadline" class="form-input"
                        value="{{ old('participant_registration_deadline', $deadline ? date('Y-m-d\TH:i', strtotime($deadline->participant_registration_deadline)) : '') }}"
                        required>
                </div>
                <div class="form-group">
                    <label class="form-label">Presenter Payment Deadline</label>
                    <input type="datetime-local" name="presenter_payment_deadline" class="form-input"
                        value="{{ old('presenter_payment_deadline', $deadline ? date('Y-m-d\TH:i', strtotime($deadline->presenter_payment_deadline)) : '') }}"
                        required>
                </div>
                <div class="form-group">
                    <label class="form-label">Listener Payment Deadline</label>
                    <input type="datetime-local" name="listener_payment_deadline" class="form-input"
                        value="{{ old('listener_payment_deadline', $deadline ? date('Y-m-d\TH:i', strtotime($deadline->listener_payment_deadline)) : '') }}"
                        required>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 20px;">
            <button type="submit" class="btn-save">Save All Deadlines</button>
        </div>
    </form>
@endsection