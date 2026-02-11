@extends('layouts.participant_horizontal')

@section('title', 'My Registration - ' . $conference->name)

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
        /* Main Content */
        .container {
            max-width: 800px;
            margin: 0 auto;
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

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 16px 0;
            border-bottom: 1px solid #f0f0f2;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            color: #666;
            font-weight: 500;
        }

        .detail-value {
            font-weight: 700;
            color: #1a1a2e;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .status-active {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-pending {
            background: #fff8e1;
            color: #f57f17;
        }

        .btn-confirm {
            background: #FF5722;
            color: #fff;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: all 0.2s;
        }

        .btn-confirm:hover {
            background: #e64a19;
            transform: translateY(-1px);
        }

        .success-msg {
            color: #2e7d32;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #e8f5e9;
            padding: 12px;
            border-radius: 10px;
            margin-top: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="conference-nav">
        <div class="nav-container">
            <a href="{{ route('participant.conference.show', $conference->conference_id) }}"
                class="conf-nav-item {{ request()->routeIs('participant.conference.show') ? 'active' : '' }}">
                Conference Details
            </a>
            <a href="{{ route('participant.registration.show', $conference->conference_id) }}"
                class="conf-nav-item {{ request()->routeIs('participant.registration.show') ? 'active' : '' }}">
                My Registration
            </a>
            @if($registration && $registration->registration_type === 'author')
                <a href="{{ route('conference.submissions.index', $conference->conference_id) }}"
                    class="conf-nav-item {{ request()->routeIs('conference.submissions.*') ? 'active' : '' }}">
                    My Submissions
                </a>
            @endif
        </div>
    </div>

    <main class="container">
        <div class="card">
            <h2 class="section-title">
                Registration Details
            </h2>

            @if($registration)
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="status-badge status-active">{{ ucfirst($registration->registration_status) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Role</span>
                    <span class="detail-value">{{ ucfirst($registration->registration_type) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Category</span>
                    <span class="detail-value">{{ str_replace('_', ' ', ucfirst($registration->registration_category)) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Registered On</span>
                    <span
                        class="detail-value">{{ \Carbon\Carbon::parse($registration->registration_date)->format('M d, Y') }}</span>
                </div>
            @else
                <p style="color:#666; text-align:center; padding: 20px;">
                    You have not completed conference registration yet.
                </p>
            @endif
        </div>

        @if($isInvitedPresenter)
            <div class="card">
                <h2 class="section-title">Presenter Registration</h2>

                <div class="detail-row">
                    <span class="detail-label">Role</span>
                    <span class="detail-value">Presenter</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span
                        class="status-badge {{ $presenterRegistrationStatus === 'Confirmed' ? 'status-active' : 'status-pending' }}">
                        {{ $presenterRegistrationStatus }}
                    </span>
                </div>

                @if($presenterRegistrationStatus === 'Pending Confirmation')
                    <form method="POST" action="{{ route('presenter.confirm', $conference->conference_id) }}">
                        @csrf
                        <button type="submit" class="btn-confirm"
                            onclick="return confirm('Confirm registration as presenter for this conference?');">
                            Confirm Registration
                        </button>
                    </form>
                @elseif($presenterRegistrationStatus === 'Confirmed')
                    <div class="success-msg">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                        </svg>
                        Presenter registration confirmed
                    </div>
                @endif
            </div>
        @endif

        {{-- Listener Fee Payment --}}
        @if($registration && $registration->registration_type === 'listener')
            <div class="card">
                <h2 class="section-title">Registration Fee Payment</h2>

                <div
                    style="display: flex; justify-content: space-between; align-items: center; background: #f8f9fa; padding: 24px; border-radius: 12px; margin-bottom: 20px;">
                    <div>
                        <div
                            style="font-size: 0.75rem; color: #666; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px;">
                            Registration Fee</div>
                        <span style="font-size: 1.5rem; font-weight: 700; color: #1a1a2e;">
                            @if($registrationFee)
                                {{ $registrationFee->currency }} {{ number_format($registrationFee->amount, 2) }}
                            @else
                                RM 0.00
                            @endif
                        </span>
                    </div>
                    <div>
                        <div
                            style="font-size: 0.75rem; color: #666; text-transform: uppercase; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px;">
                            Category</div>
                        <span
                            style="font-weight: 600; color: #1a1a2e;">{{ str_replace('_', ' ', ucfirst($registration->registration_category)) }}</span>
                    </div>
                </div>

                @if($listenerPayment)
                    <div class="success-msg">
                        <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                            <path
                                d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                        </svg>
                        Payment Completed
                    </div>
                @else
                    <button type="button" class="btn-confirm" id="listenerPayBtn"
                        onclick="document.getElementById('listenerPaymentForm').style.display='block'; this.style.display='none';">
                        Pay Now
                    </button>
                    <div id="listenerPaymentForm" style="display:none; margin-top: 20px;">
                        <form method="POST" action="{{ route('listener.pay', $conference->conference_id) }}">
                            @csrf
                            <div
                                style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); border-radius: 16px; padding: 24px; margin-bottom: 20px; color: #fff; position: relative; overflow: hidden;">
                                <div
                                    style="position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.05);">
                                </div>
                                <div
                                    style="position: absolute; bottom: -40px; left: -20px; width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.03);">
                                </div>
                                <div
                                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
                                    <span
                                        style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 2px; opacity: 0.7;">Credit
                                        / Debit Card</span>
                                    <div style="display: flex; gap: 8px;">
                                        <div
                                            style="width: 36px; height: 24px; background: #eb001b; border-radius: 4px; position: relative;">
                                            <div
                                                style="width: 36px; height: 24px; background: #f79e1b; border-radius: 4px; position: absolute; left: 12px; opacity: 0.8;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div style="margin-bottom: 20px;">
                                    <input type="text" placeholder="1234  5678  9012  3456" maxlength="19"
                                        style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px; padding: 14px 16px; color: #fff; font-size: 1.1rem; letter-spacing: 3px; font-family: 'Courier New', monospace; outline: none;"
                                        oninput="this.value=this.value.replace(/[^\d]/g,'').replace(/(.{4})/g,'$1 ').trim()"
                                        required>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px;">
                                    <div>
                                        <label
                                            style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6; display: block; margin-bottom: 6px;">Expiry
                                            Date</label>
                                        <input type="text" placeholder="MM / YY" maxlength="7"
                                            style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px; padding: 12px 14px; color: #fff; font-size: 0.95rem; letter-spacing: 2px; outline: none;"
                                            oninput="let v=this.value.replace(/[^\d]/g,''); if(v.length>=2) v=v.slice(0,2)+' / '+v.slice(2); this.value=v;"
                                            required>
                                    </div>
                                    <div>
                                        <label
                                            style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6; display: block; margin-bottom: 6px;">CVV</label>
                                        <input type="password" placeholder="•••" maxlength="4"
                                            style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px; padding: 12px 14px; color: #fff; font-size: 0.95rem; letter-spacing: 4px; outline: none;"
                                            required>
                                    </div>
                                </div>
                                <div>
                                    <label
                                        style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.6; display: block; margin-bottom: 6px;">Cardholder
                                        Name</label>
                                    <input type="text" placeholder="JOHN DOE"
                                        style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 10px; padding: 12px 14px; color: #fff; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 1px; outline: none;"
                                        required>
                                </div>
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <button type="button"
                                    style="flex:1; padding: 14px; border-radius: 12px; border: 1px solid #ddd; background: #fff; color: #666; font-weight: 600; cursor: pointer; font-size: 0.95rem;"
                                    onclick="document.getElementById('listenerPaymentForm').style.display='none'; document.getElementById('listenerPayBtn').style.display='inline-block';">
                                    Cancel
                                </button>
                                <button type="submit"
                                    style="flex:2; padding: 14px; border-radius: 12px; border: none; background: linear-gradient(135deg, #FF5722, #e64a19); color: #fff; font-weight: 700; cursor: pointer; font-size: 0.95rem; transition: transform 0.2s;"
                                    onmouseover="this.style.transform='translateY(-1px)'" onmouseout="this.style.transform='none'"
                                    onclick="return confirm('Confirm registration fee payment?');">
                                    Pay @if($registrationFee) {{ $registrationFee->currency }}
                                    {{ number_format($registrationFee->amount, 2) }} @else Fee @endif
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        @endif
    </main>
@endsection