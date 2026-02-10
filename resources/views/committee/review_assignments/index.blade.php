<h2>Assign Reviewers</h2>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif
@if(session('error'))
    <p style="color:red">{{ session('error') }}</p>
@endif

<p>
    <a href="{{ route('committee.papers.index', $conference_id) }}">← Back to Paper List</a>
</p>

<hr>

<h3>Full Papers</h3>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <tr>
        <th>No.</th>
        <th>Fullpaper ID</th>
        <th>Paper Title</th>
        <th>Track</th>
        <th>Full Paper Status</th>
        <th>Assigned Reviewers</th>
        <th>Assign</th>
    </tr>

    @foreach($fullpapers as $i => $fp)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $fp->fullpaper_id }}</td>
            <td>{{ $fp->title }}</td>
            <td>{{ $fp->track_name }}</td>
            <td>{{ $fp->fullpaper_status }}</td>

            <td>
                @php $list = $assignments[$fp->fullpaper_id] ?? collect(); @endphp

                @if($list->isEmpty())
                    <span style="color:gray">None</span>
                @else
                    <ul style="margin:0; padding-left:16px">
                        @foreach($list as $a)
                            <li>
                                {{ $a->reviewer_name }} ({{ $a->review_status }}
                                @if($a->review_status === 'Submitted')
                                    , score={{ $a->score }}
                                @endif
                                )
                            </li>
                        @endforeach
                    </ul>
                @endif
            </td>

            <td>
                @if($reviewers->isEmpty())
                    <span style="color:red">No active reviewers in this conference.</span>
                @else
                    <form method="POST" action="{{ route('committee.review.assign', [$conference_id, $fp->fullpaper_id]) }}">
                        @csrf
                        <select name="reviewer_id" required>
                            <option value="">-- Select Reviewer --</option>
                            @foreach($reviewers as $r)
                                <option value="{{ $r->reviewer_id }}">
                                    {{ $r->name }} ({{ $r->email }})
                                </option>
                            @endforeach
                        </select>
                        <button type="submit">Assign</button>
                    </form>
                @endif
            </td>
        </tr>
    @endforeach
</table>