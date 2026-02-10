@extends('layouts.committee')

@section('title', 'Conference Setup')

@section('styles')
    <style>
        .page-header {
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .page-title h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .page-title p {
            color: #666;
            font-size: 15px;
        }

        .badge-status {
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-draft { background: #f5f5f7; color: #666; }
        .status-active { background: #e8f5e9; color: #2e7d32; }
        .status-closed { background: #ffebee; color: #c62828; }

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

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            font-family: inherit;
            font-size: 15px;
            transition: all 0.2s;
            background: #fff;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            border-color: #FF5722;
            outline: none;
            box-shadow: 0 0 0 4px rgba(255, 87, 34, 0.1);
        }

        .form-input:read-only, .form-textarea:read-only {
            background: #f8f9fa;
            cursor: not-allowed;
            color: #888;
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

        .btn-activate {
            background: #2e7d32;
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

        .btn-activate:hover {
            background: #1b5e20;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.2);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
        }

        .alert-success { background: #e8f5e9; color: #2e7d32; }
        .alert-error { background: #fff5f5; color: #c62828; }

        .form-footer {
            display: flex;
            justify-content: flex-end;
            gap: 16px;
            margin-top: 20px;
        }

        .logo-preview {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid #eee;
            margin-bottom: 12px;
            display: block;
        }
    </style>
@endsection

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h1>Conference Setup</h1>
            <p>Manage basic details and configuration for {{ $conference->name }}</p>
        </div>
        <div class="page-actions">
            @php $status = strtolower($conference->conference_status); @endphp
            <span class="badge-status status-{{ $status }}">
                {{ $status }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
            </svg>
            <ul> @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('conference.setup.update', $conference->conference_id) }}" enctype="multipart/form-data">
        @csrf

        <div class="card">
            <div class="card-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
                Core Identity
            </div>
            <div class="form-grid">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Conference Title / Name</label>
                    <input name="name" class="form-input" value="{{ old('name', $conference->name) }}" 
                           {{ $conference->conference_status === 'closed' ? 'readonly' : '' }} required>
                </div>
                <div class="form-group">
                    <label class="form-label">Conference Logo</label>
                    @if($conference->logo)
                        <img src="{{ asset($conference->logo) }}" class="logo-preview" alt="Conference Logo">
                    @endif
                    <input type="file" name="logo" class="form-input" 
                           {{ $conference->conference_status === 'closed' ? 'disabled' : '' }}>
                    <p style="font-size: 12px; color: #888; margin-top: 4px;">Recommended: Square, PNG or JPG, max 2MB.</p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/>
                </svg>
                Chairman & Leadership
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Chairman Name</label>
                    <input name="chairman_name" class="form-input" value="{{ old('chairman_name', $conference->chairman_name) }}" 
                           {{ $conference->conference_status === 'closed' ? 'readonly' : '' }} required>
                </div>
                <div class="form-group">
                    <label class="form-label">Chairman Email</label>
                    <input type="email" name="chairman_email" class="form-input" value="{{ old('chairman_email', $conference->chairman_email) }}" 
                           {{ $conference->conference_status === 'closed' ? 'readonly' : '' }} required>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                </svg>
                Official Contact Details
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Conference Phone Number</label>
                    <input name="conference_phone" class="form-input" value="{{ old('conference_phone', $conference->conference_phone) }}" 
                           {{ $conference->conference_status === 'closed' ? 'readonly' : '' }}>
                </div>
                <div class="form-group">
                    <label class="form-label">Conference Official Email</label>
                    <input type="email" name="conference_email" class="form-input" value="{{ old('conference_email', $conference->conference_email) }}" 
                           {{ $conference->conference_status === 'closed' ? 'readonly' : '' }}>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                </svg>
                Location & Digital Presence
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Conference Venue</label>
                    <input name="conference_venue" class="form-input" value="{{ old('conference_venue', $conference->conference_venue) }}" 
                           {{ $conference->conference_status === 'closed' ? 'readonly' : '' }} required>
                </div>
                <div class="form-group">
                    <label class="form-label">Conference Website (URL)</label>
                    <input type="url" name="conference_website" class="form-input" value="{{ old('conference_website', $conference->conference_website) }}" 
                           {{ $conference->conference_status === 'closed' ? 'readonly' : '' }}>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                Administrative & Publication
            </div>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Publication Method / Publishment</label>
                    <input name="publication" class="form-input" value="{{ old('publication', $conference->publication) }}" 
                           {{ $conference->conference_status === 'closed' ? 'readonly' : '' }}>
                </div>
                <div class="form-group">
                    <label class="form-label">Conference Date</label>
                    <input type="date" name="conference_date" class="form-input" 
                           value="{{ old('conference_date', $conference->conference_date ? substr($conference->conference_date,0,10) : '') }}"
                           {{ $conference->conference_status !== 'draft' ? 'readonly' : '' }} required>
                    <p style="font-size: 12px; color: #888; margin-top: 4px;">Date can only be set during Draft stage.</p>
                </div>
            </div>
            <div class="form-group" style="margin-top: 10px;">
                <label class="form-label">Secretariat Address</label>
                <textarea name="secretariat_address" class="form-textarea" rows="3" 
                          {{ $conference->conference_status === 'closed' ? 'readonly' : '' }}>{{ old('secretariat_address', $conference->secretariat_address) }}</textarea>
            </div>
        </div>

        @if($conference->conference_status !== 'closed')
            <div class="form-footer">
                <button type="submit" class="btn-save">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        @endif
    </form>
@endsection
