@extends('layouts.reviewer')

@section('title', isset($readonly) && $readonly ? 'Review Details' : 'Review Form')

@section('styles')
    <style>
        .review-container {
            background: #fff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: 1px solid #f0f0f0;
        }

        .paper-field {
            margin-bottom: 24px;
        }

        .field-label {
            font-size: 14px;
            color: #888;
            margin-bottom: 8px;
            display: block;
        }

        .field-value {
            background: #f9f9f9;
            border: 1px solid #eef0f2;
            border-radius: 12px;
            padding: 12px 20px;
            font-size: 15px;
            color: #333;
            width: 100%;
            display: inline-block;
        }

        .section-header {
            font-size: 16px;
            font-weight: 500;
            color: #333;
            margin: 32px 0 16px 0;
        }

        /* Rating Table */
        .rating-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ccc;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .rating-table th, .rating-table td {
            border: 1px solid #ccc;
            padding: 16px;
            text-align: left;
            font-size: 14px;
        }

        .rating-table th {
            background: #fdfdfd;
            font-weight: 600;
            color: #666;
            text-align: center;
        }

        .rating-table td:first-child, .rating-table th:first-child { width: 50px; text-align: center; }
        .rating-table .point-col { width: 300px; }
        .rating-table .score-col { background: #fff; width: 350px; }
        .rating-table .weight-col { width: 100px; text-align: center; }

        .score-header-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            color: #666;
            font-weight: 500;
            margin-bottom: 8px;
            text-align: center;
        }

        .radio-set-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 4px;
            align-items: center;
            justify-items: center;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 4px;
            font-size: 13px;
        }

        .total-row {
            display: flex;
            justify-content: flex-end;
            gap: 30px;
            margin-top: 12px;
            font-weight: 600;
            font-size: 14px;
        }

        /* Generic Form Groups */
        .form-section {
            margin-top: 32px;
        }

        .radio-group-simple {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 12px;
        }

        .radio-inline {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            font-size: 14px;
            color: #555;
        }

        .textarea-plain {
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 12px 16px;
            min-height: 50px;
            margin-top: 12px;
            font-family: inherit;
        }

        .btn-submit-green {
            background: #4cc065;
            color: #fff;
            border: none;
            padding: 12px 32px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-submit-green:hover { opacity: 0.9; }

        .footer-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 40px;
        }
    </style>
@endsection

@section('content')
    @php
        $isReadonly = isset($readonly) && $readonly;
        $isSubmitted = $review->review_status === 'Submitted';
        $disabled = ($isReadonly || $isSubmitted) ? 'disabled' : '';
    @endphp

    <div style="margin-bottom: 20px;">
        <h2 style="font-size: 18px; font-weight: 600; color: #333;">Review Form</h2>
    </div>

    <div class="review-container">
        <div class="paper-field">
            <label class="field-label">Paper ID</label>
            <div class="field-value">
                {{ $review->conference_name }}: #{{ $review->paper_id }}
            </div>
        </div>

        <div class="paper-field">
            <label class="field-label">Paper Title</label>
            <div class="field-value">
                {{ $review->title }}
            </div>
        </div>

        <div class="paper-field">
            <label class="field-label">Full Paper</label>
            <div class="field-value">
                @if(!empty($review->fullpaper_file_path))
                    <a href="{{ asset('storage/' . $review->fullpaper_file_path) }}" target="_blank" style="color: #2962ff; text-decoration: none;">
                        Download Full Paper
                    </a>
                @else
                    <span style="color: #999;">Not available</span>
                @endif
            </div>
        </div>

        <form method="POST" action="{{ route('reviewer.reviews.submit', $review->review_id) }}" enctype="multipart/form-data">
            @csrf

            <h4 class="section-header">Section A: How do you rate this Manuscript according the listed items?</h4>
            
            <table class="rating-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th class="point-col">Review Point</th>
                        <th class="score-col">
                            <div class="score-header-grid">
                                <span>Weak</span>
                                <span style="grid-column: span 4;">Fair</span>
                                <span>Good</span>
                            </div>
                        </th>
                        <th class="weight-col">Weightage</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $criteria = [
                            ['field' => 'rate_originality', 'label' => 'Originality of Work'],
                            ['field' => 'rate_literature_depth', 'label' => 'Depth of Literature Review'],
                            ['field' => 'rate_methodology', 'label' => 'Suitability of Research Methodology'],
                            ['field' => 'rate_novelty', 'label' => 'Novelty of Findings'],
                            ['field' => 'rate_clarity', 'label' => 'Well Written, Organised and Clearly Understood'],
                            ['field' => 'rate_contribution', 'label' => 'Contribution to Research Advancement and Policy'],
                        ];
                    @endphp

                    @foreach($criteria as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item['label'] }}</td>
                            <td class="score-col">
                                <div class="radio-set-grid">
                                    @for($i = 0; $i <= 5; $i++)
                                        <label class="radio-item" style="flex-direction: column; gap: 4px;">
                                            <input type="radio" name="{{ $item['field'] }}" value="{{ $i }}" 
                                                {{ $review->{$item['field']} == $i ? 'checked' : '' }} {{ $disabled }}>
                                            <span style="font-size: 11px;">{{ $i }}</span>
                                        </label>
                                    @endfor
                                </div>
                            </td>
                            <td class="weight-col">
                                @if($review->{$item['field']} !== null)
                                    {{ $review->{$item['field']} }}/5
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="total-row">
                <span>Total Score</span>
                <span>{{ !empty($review->score) ? number_format(($review->score / 5) * 100, 0) . '%' : '-' }}</span>
            </div>

            <div class="form-section">
                <h4 class="section-header">Section B: Accept for Presentation?</h4>
                <div class="radio-group-simple">
                    <label class="radio-inline">
                        <input type="radio" name="accept_presentation" value="Yes" {{ $review->accept_presentation === 'Yes' ? 'checked' : '' }} {{ $disabled }}> Yes
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="accept_presentation" value="No" {{ $review->accept_presentation === 'No' ? 'checked' : '' }} {{ $disabled }}> No
                    </label>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-header">Section C: Comments (Overall Comments: will not be revealed to Author) ; (Comments to Author: will be revealed to Author)</h4>
                <label class="field-label">Overall Comment</label>
                <textarea name="overall_comments" class="textarea-plain" {{ $disabled }}>{{ $review->overall_comments }}</textarea>

                <div style="margin-top: 24px;">
                    <label class="field-label" style="font-weight: 600; color: #333;">Comment to Author</label>
                    <textarea name="comments_to_author" class="textarea-plain" {{ $disabled }}>{{ $review->comments_to_author }}</textarea>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-header">Section D: Proposed for Scopus Indexed Journal?</h4>
                <div class="radio-group-simple">
                    <label class="radio-inline">
                        <input type="radio" name="propose_scopus" value="Yes" {{ $review->propose_scopus === 'Yes' ? 'checked' : '' }} {{ $disabled }}> Yes
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="propose_scopus" value="No" {{ $review->propose_scopus === 'No' ? 'checked' : '' }} {{ $disabled }}> No
                    </label>
                </div>
            </div>

            <div class="form-section">
                <h4 class="section-header">Section E: Proposed for Non-Scopus Indexed Journal?</h4>
                <div class="radio-group-simple">
                    <label class="radio-inline">
                        <input type="radio" name="propose_non_scopus" value="Yes" {{ $review->propose_non_scopus === 'Yes' ? 'checked' : '' }} {{ $disabled }}> Yes
                    </label>
                    <label class="radio-inline">
                        <input type="radio" name="propose_non_scopus" value="No" {{ $review->propose_non_scopus === 'No' ? 'checked' : '' }} {{ $disabled }}> No
                    </label>
                </div>
            </div>

            <div class="form-section">
                <label class="field-label" style="font-weight: 600; color: #333;">File (edited/revised/track change format manuscript)</label>
                @if(!empty($review->revised_file_path))
                    <div style="margin-bottom: 8px;">
                        <a href="{{ asset('storage/' . $review->revised_file_path) }}" target="_blank" style="color: #2962ff; font-size: 13px;">View current file</a>
                    </div>
                @endif
                <input type="file" name="revised_file" class="textarea-plain" style="padding: 10px;" {{ $disabled }}>
            </div>

            @if(!$isReadonly && !$isSubmitted)
                <div class="footer-actions">
                    <button type="submit" class="btn-submit-green" onclick="return confirm('Submit final review?');">
                        Submit
                    </button>
                </div>
            @endif
        </form>
    </div>

    <div style="height: 60px;"></div>
@endsection


