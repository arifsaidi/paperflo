<h2>Abstract Decisions</h2>
<p><b>Conference:</b> {{ $conference->name }}</p>

@if(session('success'))
<p style="color:green">{{ session('success') }}</p> @endif
@if(session('error'))
<p style="color:red">{{ session('error') }}</p> @endif

@if($items->isEmpty())
    <p>No submitted abstracts yet.</p>
@else
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>Paper</th>
            <th>Track</th>
            <th>Abstract Status</th>
            <th>Submitted Date</th>
            <th>Action</th>
        </tr>

        @foreach($items as $i)
            <tr>
                <td>
                    <b>{{ $i->title }}</b><br>
                    <small>Keywords: {{ $i->keywords }}</small>
                </td>
                <td>{{ $i->track_name }}</td>
                <td>{{ $i->abstract_status }}</td>
                <td>{{ $i->submitted_date }}</td>
                <td>
                    @if($i->abstract_status === 'Submitted')
                        <form method="POST"
                            action="{{ route('abstract.decisions.decide', [$conference->conference_id, $i->paper_id]) }}">
                            @csrf

                            <select name="decision" required>
                                <option value="">-- Select --</option>
                                <option value="Accepted">Accepted</option>
                                <option value="Rejected">Rejected</option>
                            </select>

                            <button type="submit">Save</button>
                        </form>
                    @else
                        <b>Final:</b> {{ $i->abstract_status }}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endif

<p style="margin-top:20px;">
    <a href="{{ route('home') }}">← Back</a>
</p>