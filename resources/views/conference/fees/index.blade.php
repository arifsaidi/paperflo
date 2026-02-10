@extends('layouts.committee')

@section('title', 'Conference Fees')

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

        .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .fee-table th {
            text-align: left;
            padding: 12px 16px;
            border-bottom: 2px solid #f5f5f7;
            color: #666;
            font-size: 14px;
        }

        .fee-table td {
            padding: 16px;
            border-bottom: 1px solid #f5f5f7;
            vertical-align: middle;
        }

        .form-input {
            padding: 10px 14px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            width: 100%;
        }

        .form-input:focus {
            border-color: #FF5722;
            outline: none;
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
        }

        .btn-save:hover {
            background: #e64a19;
            transform: translateY(-2px);
        }

        .add-row {
            background: #fafafa;
            border-radius: 12px;
            padding: 20px;
            margin-top: 10px;
            border: 1px dashed #ddd;
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

@section('scripts')
    <form id="delete-fee-form" method="POST" style="display:none;">
        @csrf
    </form>

    <script>
        function deleteFee(url) {
            if (confirm('Are you sure you want to remove this fee category?')) {
                const form = document.getElementById('delete-fee-form');
                form.action = url;
                form.submit();
            }
        }
    </script>
@endsection

@section('content')
    <div class="page-header">
        <h1>Conference Fees</h1>
        <p>Manage registration fees for different participant categories for <strong>{{ $conference->name }}</strong></p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('conference.fees.update', $conference->conference_id) }}">
        @csrf

        <div class="card">
            <div class="card-title">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <line x1="12" y1="1" x2="12" y2="23" />
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                </svg>
                Registration Fees
            </div>

            <table class="fee-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">Category / Registration Type</th>
                        <th style="width: 30%;">Amount (MYR)</th>
                        <th style="width: 20%; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fees as $fee)
                        <tr>
                            <td>
                                <input name="fees[{{ $fee->fee_id }}][name]" class="form-input" value="{{ $fee->name }}"
                                    required>
                            </td>
                            <td>
                                <input type="number" step="0.01" name="fees[{{ $fee->fee_id }}][amount]" class="form-input"
                                    value="{{ $fee->amount }}" required>
                            </td>
                            <td style="text-align: right;">
                                <button type="button" class="btn-delete"
                                    style="color: #c62828; background: none; border: none; cursor: pointer; padding: 8px; border-radius: 8px; transition: all 0.2s;"
                                    title="Delete Fee"
                                    onclick="deleteFee('{{ route('conference.fees.destroy', [$conference->conference_id, $fee->fee_id]) }}')">
                                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M19 7l-.867 12.142A2 2 0 0 1 16.138 21H7.862a2 2 0 0 1-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <!-- Initial blank rows handled by Add Fee section -->
                    @endforelse
                </tbody>
            </table>

            <div class="add-row">
                <p style="font-weight: 700; margin-bottom: 12px; color: #333; font-size: 14px;">Add New Fee Category</p>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <input name="new_fee_name" class="form-input" placeholder="e.g., Early Bird Professional">
                    </div>
                    <div>
                        <input type="number" step="0.01" name="new_fee_amount" class="form-input" placeholder="0.00">
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 32px;">
                <button type="submit" class="btn-save">Update Fees</button>
            </div>
        </div>
    </form>
@endsection