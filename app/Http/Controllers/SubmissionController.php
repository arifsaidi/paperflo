<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;


class SubmissionController extends Controller
{
    // 1) My Submissions list (for logged-in author)
    public function index()
    {
        $participantId = session('user_id');

        $papers = DB::table('paper')
            ->join('paper_author', 'paper_author.paper_id', '=', 'paper.paper_id')
            ->join('conference', 'conference.conference_id', '=', 'paper.conference_id')
            ->leftJoin('abstract_submission', 'abstract_submission.paper_id', '=', 'paper.paper_id')
            ->leftJoin('track', 'track.track_id', '=', 'paper.track_id')
            ->where('paper_author.participant_id', $participantId)
            ->where('paper_author.is_corresponding_author', 1)
            ->select([
                'paper.paper_id',
                'paper.title',
                'paper.conference_id',
                'conference.name as conference_name',
                'paper.track_id',
                DB::raw('COALESCE(track.name, CONCAT("Track #", paper.track_id)) as track_name'),
                'abstract_submission.abstract_status',
                'abstract_submission.submitted_date',
            ])
            ->orderByDesc('paper.paper_id')
            ->get();

        foreach ($papers as $p) {
            if ($p->abstract_status === 'Rejected') {
                $p->final_status = 'Rejected (Abstract)';
            } elseif (!empty($p->fullpaper_status) && $p->fullpaper_status === 'Rejected') {
                $p->final_status = 'Rejected (Full Paper)';
            } elseif (!empty($p->cameraready_file_path)) {
                $p->final_status = 'Accepted (Camera-ready Submitted)';
            } elseif (!empty($p->fullpaper_status) && $p->fullpaper_status === 'Accepted') {
                $p->final_status = 'Accepted (Pending Camera-ready)';
            } elseif ($p->abstract_status === 'Accepted') {
                $p->final_status = 'Abstract Accepted';
            } elseif ($p->abstract_status === 'Submitted') {
                $p->final_status = 'Under Review';
            } elseif ($p->abstract_status === 'Draft') {
                $p->final_status = 'Draft';
            } else {
                $p->final_status = '-';
            }
        }


        return view('submission.index', compact('papers'));
    }

    // 2) Add Paper form
    public function create($conference_id)
    {
        $participantId = session('user_id');

        // Conference must be ACTIVE
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->where('conference_status', 'active')
            ->first();

        if (!$conference) {
            abort(403, 'Conference is not open for submissions.');
        }

        // Must be registered as AUTHOR
        $reg = DB::table('conference_registration')
            ->where('conference_id', $conference_id)
            ->where('participant_id', $participantId)
            ->first();

        if (!$reg || $reg->registration_type !== 'author') {
            abort(403, 'Only registered authors can submit papers.');
        }

        // Tracks for dropdown
        $tracks = DB::table('track')
            ->where('conference_id', $conference_id)
            ->select(['track_id', DB::raw('COALESCE(name, CONCAT("Track #", track_id)) as name')])
            ->orderBy('track_id')
            ->get();

        if ($tracks->isEmpty()) {
            return redirect()->route('participant.conference.show', $conference_id)
                ->with('error', 'No tracks available yet. Please contact the conference admin.');
        }

        // Get participant info for paper_author insert
        $participant = DB::table('participant')
            ->where('participant_id', session('user_id'))
            ->first();

        return view('submission.create', compact('conference', 'tracks', 'participant'));
    }

    // 3) Save Draft / Submit Abstract
    public function store(Request $request, $conference_id)
    {
        $participantId = session('user_id');

        // Conference must be ACTIVE
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->where('conference_status', 'active')
            ->first();

        if (!$conference) {
            abort(403, 'Conference is not open for submissions.');
        }

        // Must be registered as AUTHOR
        $reg = DB::table('conference_registration')
            ->where('conference_id', $conference_id)
            ->where('participant_id', $participantId)
            ->first();

        if (!$reg || $reg->registration_type !== 'author') {
            abort(403, 'Only registered authors can submit papers.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'abstract_text' => 'required|string',
            'track_id' => 'required|integer',
            'keywords' => 'required|string|max:500',
            'corresponding_author_name' => 'required|string|max:255',
            'corresponding_author_email' => 'required|email|max:255',

            // co-authors are optional
            'coauthor_name' => 'array',
            'coauthor_name.*' => 'nullable|string|max:255',
            'coauthor_email' => 'array',
            'coauthor_email.*' => 'nullable|email|max:255',

            'action' => 'required|in:draft,submit',
        ]);


        // Ensure selected track belongs to this conference
        $trackExists = DB::table('track')
            ->where('track_id', $request->track_id)
            ->where('conference_id', $conference_id)
            ->exists();

        if (!$trackExists) {
            return back()->with('error', 'Invalid track selected.')->withInput();
        }

        DB::transaction(function () use ($request, $participantId, $conference_id) {

            // Get participant info for paper_author insert
            $participant = DB::table('participant')
                ->where('participant_id', $participantId)
                ->first();

            // 1) Create paper
            $paperId = DB::table('paper')->insertGetId([
                'title' => $request->title,
                'keywords' => $request->keywords,
                'conference_id' => $conference_id,
                'track_id' => $request->track_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 2) Create corresponding author record (YOUR paper_author schema)
            DB::table('paper_author')->insert([
                'name' => $request->corresponding_author_name,
                'author_name' => $request->corresponding_author_name,
                'author_email' => $request->corresponding_author_email,
                'is_corresponding_author' => true,
                'paper_id' => $paperId,
                'participant_id' => $participantId, // ✅ link to logged-in user
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 3) Create co-authors
            $coNames = $request->coauthor_name ?? [];
            $coEmails = $request->coauthor_email ?? [];

            for ($i = 0; $i < max(count($coNames), count($coEmails)); $i++) {
                $n = trim($coNames[$i] ?? '');
                $e = trim($coEmails[$i] ?? '');

                if ($n === '' && $e === '') {
                    continue; // skip empty rows
                }

                if ($n === '' || $e === '') {
                    // one missing -> reject (keeps data clean)
                    throw new \Exception("Co-author name and email must both be filled.");
                }

                DB::table('paper_author')->insert([
                    'name' => $n,
                    'author_name' => $n,
                    'author_email' => $e,
                    'is_corresponding_author' => false,
                    'paper_id' => $paperId,
                    'participant_id' => null, // co-author not linked to participant account yet
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }


            // 4) Create abstract_submission
            $isSubmit = $request->action === 'submit';

            DB::table('abstract_submission')->insert([
                'abstract_text' => $request->abstract_text,
                'status' => 'ACTIVE', // you can standardize later
                'abstract_status' => $isSubmit ? 'Submitted' : 'Draft',
                'submitted_date' => $isSubmit ? now() : now(), // keep now for record
                'paper_id' => $paperId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('conference.submissions.index', $conference_id)
            ->with('success', $request->action === 'submit'
                ? 'Abstract submitted successfully.'
                : 'Draft saved successfully.');
    }

    public function show($paper_id)
    {
        $participantId = session('user_id');

        $participant = DB::table('participant')
            ->where('participant_id', $participantId)
            ->select('participant_id', 'email')
            ->first();

        // 1) Is author (current owner access)
        $isAuthor = DB::table('paper_author')
            ->where('paper_id', $paper_id)
            ->where('participant_id', $participantId)
            ->exists();

        // 2) Is presenter (presenter access)
        $isPresenter = DB::table('presenter_assignment')
            ->where('paper_id', $paper_id)
            ->where(function ($q) use ($participantId, $participant) {
                $q->where('participant_id', $participantId);

                if (!empty($participant?->email)) {
                    $q->orWhere('presenter_email', $participant->email);
                }
            })
            ->exists();

        if (!$isAuthor && !$isPresenter) {
            abort(403, 'You do not have access to this submission.');
        }

        // ✅ Get paper + abstract + conference (NO corresponding-author restriction here)
        $paper = DB::table('paper')
            ->join('conference', 'conference.conference_id', '=', 'paper.conference_id')
            ->leftJoin('abstract_submission', 'abstract_submission.paper_id', '=', 'paper.paper_id')
            ->leftJoin('track', 'track.track_id', '=', 'paper.track_id')
            ->leftJoin('fullpaper_submission', 'fullpaper_submission.paper_id', '=', 'paper.paper_id')
            ->where('paper.paper_id', $paper_id)
            ->select(
                'paper.paper_id',
                'paper.title',
                'paper.keywords',
                'paper.track_id',
                'paper.conference_id',
                'conference.name as conference_name',
                'abstract_submission.abstract_text',
                'abstract_submission.abstract_status',
                'abstract_submission.submitted_date',
                DB::raw('COALESCE(track.name, CONCAT("Track #", paper.track_id)) as track_name'),
                'fullpaper_submission.file_path as fullpaper_file_path',
                'fullpaper_submission.submitted_date as fullpaper_submitted_date',
                'fullpaper_submission.fullpaper_status as fullpaper_status'
            )
            ->first();

        if (!$paper) {
            abort(404, 'Paper not found.');
        }

        $isCorrespondingAuthor = DB::table('paper_author')
            ->where('paper_id', $paper_id)
            ->where('participant_id', $participantId)
            ->where('is_corresponding_author', 1)
            ->exists();

        $canManageSubmission = $isCorrespondingAuthor; // only corresponding author can edit/submit/etc
        $canPresenterActions = $isPresenter;           // presenter can pay + camera-ready



        // Get all authors (corresponding + co-authors)
        $authors = DB::table('paper_author')
            ->where('paper_id', $paper_id)
            ->orderByDesc('is_corresponding_author')
            ->get();

        // Get tracks for the conference
        $tracks = DB::table('track')
            ->where('conference_id', $paper->conference_id)
            ->select(['track_id', DB::raw('COALESCE(name, CONCAT("Track #", track_id)) as name')])
            ->orderBy('track_id')
            ->get();


        // Get conference details for Hero
        $conference = DB::table('conference')->where('conference_id', $paper->conference_id)->first();


        // Check if fullpaper deadline has passed
        $deadline = DB::table('deadline')
            ->where('conference_id', $paper->conference_id)
            ->select('fullpaper_deadline')
            ->first();

        $fullpaperDeadline = $deadline?->fullpaper_deadline; // may be null if not set
        $isFullpaperDeadlinePassed = $fullpaperDeadline
            ? Carbon::now()->gt(Carbon::parse($fullpaperDeadline))
            : false;



        return view('submission.show', compact(
            'paper',
            'conference',
            'authors',
            'tracks',
            'fullpaperDeadline',
            'isFullpaperDeadlinePassed',
            'canManageSubmission',
            'canPresenterActions'
        ));
    }


    public function update(Request $request, $paper_id)
    {
        $participantId = session('user_id');

        $paper = DB::table('paper')
            ->join('paper_author', 'paper_author.paper_id', '=', 'paper.paper_id')
            ->join('abstract_submission', 'abstract_submission.paper_id', '=', 'paper.paper_id')
            ->where('paper.paper_id', $paper_id)
            ->where('paper_author.participant_id', $participantId)
            ->where('paper_author.is_corresponding_author', 1)
            ->select('paper.paper_id', 'abstract_submission.abstract_status')
            ->first();

        if (!$paper) {
            abort(403);
        }

        if ($paper->abstract_status !== 'Draft') {
            abort(403, 'Submitted abstracts cannot be edited.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'abstract_text' => 'required|string',
            'keywords' => 'required|string|max:500',
            'track_id' => 'required|integer',
            'action' => 'required|in:update,submit',
        ]);

        // Check if the track exists for the conference
        $trackExists = DB::table('track')
            ->where('track_id', $request->track_id)
            ->where('conference_id', function ($q) use ($paper_id) {
                $q->select('conference_id')
                    ->from('paper')
                    ->where('paper_id', $paper_id);
            })
            ->exists();

        if (!$trackExists) {
            return back()->with('error', 'Invalid track selected.');
        }


        DB::transaction(function () use ($request, $paper_id) {

            DB::table('paper')
                ->where('paper_id', $paper_id)
                ->update([
                    'title' => $request->title,
                    'keywords' => $request->keywords,
                    'track_id' => $request->track_id,
                    'updated_at' => now(),
                ]);

            $abstractUpdate = [
                'abstract_text' => $request->abstract_text,
                'updated_at' => now(),
            ];

            if ($request->action === 'submit') {
                $abstractUpdate['abstract_status'] = 'Submitted';
                $abstractUpdate['submitted_date'] = now();
            }

            DB::table('abstract_submission')
                ->where('paper_id', $paper_id)
                ->update($abstractUpdate);
        });



        return redirect()->route('submissions.show', $paper_id)
            ->with(
                'success',
                $request->action === 'submit'
                ? 'Abstract submitted successfully.'
                : 'Draft updated successfully.'
            );

    }


    public function withdraw(Request $request, $paper_id)
    {
        $participantId = session('user_id');

        $paper = DB::table('paper')
            ->join('paper_author', 'paper_author.paper_id', '=', 'paper.paper_id')
            ->join('abstract_submission', 'abstract_submission.paper_id', '=', 'paper.paper_id')
            ->where('paper.paper_id', $paper_id)
            ->where('paper_author.participant_id', $participantId)
            ->where('paper_author.is_corresponding_author', 1)
            ->select('paper.paper_id', 'abstract_submission.abstract_status')
            ->first();

        if (!$paper || $paper->abstract_status !== 'Submitted') {
            abort(403);
        }

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        DB::transaction(function () use ($request, $paper_id, $participantId) {

            DB::table('withdrawal_request')->insert([
                'withdrawal_stage' => 'Abstract',
                'reason' => $request->reason,
                'request_date' => now(),
                'paper_id' => $paper_id,
                'participant_id' => $participantId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('abstract_submission')
                ->where('paper_id', $paper_id)
                ->update([
                    'abstract_status' => 'Withdrawn',
                    'updated_at' => now(),
                ]);
        });

        return redirect()->route('submissions.index')
            ->with('success', 'Abstract withdrawn successfully.');
    }


    public function uploadFullPaper(Request $request, $paper_id)
    {
        $participantId = session('user_id');

        // Ownership + stage check (corresponding author only)
        $paper = DB::table('paper')
            ->join('paper_author', 'paper_author.paper_id', '=', 'paper.paper_id')
            ->join('abstract_submission', 'abstract_submission.paper_id', '=', 'paper.paper_id')
            ->where('paper.paper_id', $paper_id)
            ->where('paper_author.participant_id', $participantId)
            ->where('paper_author.is_corresponding_author', 1)
            ->select('paper.paper_id', 'paper.conference_id', 'abstract_submission.abstract_status')
            ->first();

        if (!$paper) {
            abort(403, 'You do not have access to this submission.');
        }

        // Gate 1: abstract must be Accepted
        if ($paper->abstract_status !== 'Accepted') {
            abort(403, 'Full paper upload is only allowed after abstract acceptance.');
        }

        // Gate 2: deadline check
        $deadline = DB::table('deadline')
            ->where('conference_id', $paper->conference_id)
            ->select('fullpaper_deadline')
            ->first();

        if ($deadline && $deadline->fullpaper_deadline) {
            if (Carbon::now()->gt(Carbon::parse($deadline->fullpaper_deadline))) {
                abort(403, 'Full paper deadline has passed.');
            }
        }

        // Validate file
        $request->validate([
            'fullpaper_file' => 'required|file|mimes:pdf,doc,docx|max:20480', // 20MB
        ]);

        // Store file in /storage/app/public/fullpapers
        $storedPath = $request->file('fullpaper_file')->store('fullpapers', 'public');

        // Save DB record (insert or update)
        $existing = DB::table('fullpaper_submission')
            ->where('paper_id', $paper_id)
            ->first();

        // if ($existing && in_array($existing->fullpaper_status, ['Accepted', 'Rejected'])) {
        //     abort(403, 'Full paper cannot be re-uploaded after the committee has made a decision.');
        // }

        if ($existing && in_array($existing->fullpaper_status, ['Accepted', 'Rejected', 'Withdrawn'])) {
            abort(403, 'Full paper cannot be uploaded after it is decided or withdrawn.');
        }


        DB::transaction(function () use ($paper_id, $storedPath, $existing) {

            $data = [
                'file_path' => $storedPath,         // e.g. fullpapers/xxx.pdf
                'status' => 'Submitted',            // keep consistent with your schema
                'fullpaper_status' => 'Submitted',  // main status
                'submitted_date' => now(),
                'updated_at' => now(),
            ];

            if ($existing) {
                DB::table('fullpaper_submission')
                    ->where('paper_id', $paper_id)
                    ->update($data);
            } else {
                $data['paper_id'] = $paper_id;
                $data['created_at'] = now();

                DB::table('fullpaper_submission')->insert($data);
            }
        });

        return redirect()->route('submissions.show', $paper_id)
            ->with('success', 'Full paper submitted successfully.');
    }


    public function withdrawFullPaper(Request $request, $paper_id)
    {
        $participantId = session('user_id');

        // Must own the paper as corresponding author
        $paper = DB::table('paper')
            ->join('paper_author', 'paper_author.paper_id', '=', 'paper.paper_id')
            ->where('paper.paper_id', $paper_id)
            ->where('paper_author.participant_id', $participantId)
            ->where('paper_author.is_corresponding_author', 1)
            ->select('paper.paper_id')
            ->first();

        if (!$paper) {
            abort(403, 'You do not have access to this submission.');
        }

        $request->validate([
            'reason' => 'required|string|min:10',
        ]);

        $full = DB::table('fullpaper_submission')
            ->where('paper_id', $paper_id)
            ->select('fullpaper_status')
            ->first();

        if (!$full) {
            abort(403, 'No full paper submitted yet.');
        }

        // Only allow withdrawal if still Submitted (not decided yet)
        if ($full->fullpaper_status !== 'Submitted') {
            abort(403, 'Only submitted full papers can be withdrawn.');
        }

        DB::transaction(function () use ($request, $paper_id, $participantId) {

            DB::table('withdrawal_request')->insert([
                'withdrawal_stage' => 'Full Paper',
                'reason' => $request->reason,
                'request_date' => now(),
                'paper_id' => $paper_id,
                'participant_id' => $participantId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('fullpaper_submission')
                ->where('paper_id', $paper_id)
                ->update([
                    'fullpaper_status' => 'Withdrawn',
                    'updated_at' => now(),
                ]);
        });

        return redirect()->route('submissions.show', $paper_id)
            ->with('success', 'Full paper withdrawn successfully.');
    }


    public function selectPresenter(Request $request, $paper_id)
    {
        $participantId = session('user_id');

        // Must be corresponding author + full paper accepted
        $paper = DB::table('paper')
            ->join('paper_author', 'paper_author.paper_id', '=', 'paper.paper_id')
            ->join('fullpaper_submission', 'fullpaper_submission.paper_id', '=', 'paper.paper_id')
            ->where('paper.paper_id', $paper_id)
            ->where('paper_author.participant_id', $participantId)
            ->where('paper_author.is_corresponding_author', 1)
            ->where('fullpaper_submission.fullpaper_status', 'Accepted')
            ->select('paper.paper_id')
            ->first();

        if (!$paper) {
            abort(403, 'Presenter can only be selected after full paper acceptance.');
        }

        // 🔽 validate AUTHOR EMAIL (not participant_id)
        $request->validate([
            'author_email' => 'required|email',
        ]);

        // 🔽 ensure selected presenter is an author
        $author = DB::table('paper_author')
            ->where('paper_id', $paper_id)
            ->where('author_email', $request->author_email)
            ->first();

        if (!$author) {
            abort(403, 'Selected presenter must be one of the paper authors.');
        }

        // 🔽 check if this author has a participant account (optional)
        $participant = DB::table('participant')
            ->where('email', $author->author_email)
            ->first();

        // Auto-confirm if the corresponding author selected themselves as presenter
        $isCorrespondingAuthor = $participant && $participant->participant_id == $participantId;

        DB::table('presenter_assignment')->updateOrInsert(
            ['paper_id' => $paper_id],
            [
                'participant_id' => $participant?->participant_id, // nullable
                'presenter_name' => $author->author_name,
                'presenter_email' => $author->author_email,
                'presenter_status' => $isCorrespondingAuthor ? 'Confirmed' : 'Pending Confirmation',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return redirect()
            ->route('submissions.show', $paper_id)
            ->with('success', 'Presenter selected successfully.');
    }




    public function payPresenter($paper_id)
    {
        $participantId = session('user_id');

        $assignment = DB::table('presenter_assignment')
            ->where('paper_id', $paper_id)
            ->where('participant_id', $participantId)
            ->first();

        if (!$assignment) {
            abort(403, 'Only the assigned presenter can pay.');
        }

        if ($assignment->presenter_status !== 'Confirmed') {
            return back()->with('error', 'Payment is only allowed after presenter confirmation.');
        }

        DB::table('presenter_assignment')
            ->where('presenter_assignment_id', $assignment->presenter_assignment_id)
            ->update([
                'presenter_status' => 'Paid',
                'updated_at' => now(),
            ]);

        return back()->with('success', 'Presenter fee payment recorded.');
    }



    public function uploadCameraReady(Request $request, $paper_id)
    {
        $participantId = session('user_id');

        // Check if user is the corresponding author
        $paper = DB::table('paper')
            ->join('paper_author', 'paper_author.paper_id', '=', 'paper.paper_id')
            ->join('fullpaper_submission', 'fullpaper_submission.paper_id', '=', 'paper.paper_id')
            ->leftJoin('presenter_assignment', 'presenter_assignment.paper_id', '=', 'paper.paper_id')
            ->where('paper.paper_id', $paper_id)
            ->where('paper_author.participant_id', $participantId)
            ->where('paper_author.is_corresponding_author', 1)
            ->select(
                'paper.paper_id',
                'paper.conference_id',
                'fullpaper_submission.fullpaper_status',
                'presenter_assignment.presenter_status'
            )
            ->first();

        if (!$paper) {
            abort(403, 'Only the corresponding author can submit the camera-ready paper.');
        }

        // Gate 1: Full paper must be accepted
        if ($paper->fullpaper_status !== 'Accepted') {
            abort(403, 'Camera-ready submission is only allowed after full paper acceptance.');
        }

        // Gate 2: Presenter payment must be paid
        if ($paper->presenter_status !== 'Paid') {
            abort(403, 'Presenter fee must be paid before camera-ready submission.');
        }

        // Gate 3: Deadline check
        $deadline = DB::table('deadline')
            ->where('conference_id', $paper->conference_id)
            ->select('cameraready_paper_deadline')
            ->first();

        if ($deadline && $deadline->cameraready_paper_deadline) {
            if (now()->gt($deadline->cameraready_paper_deadline)) {
                abort(403, 'Camera-ready submission deadline has passed.');
            }
        }

        // Validate file
        $request->validate([
            'cameraready_file' => 'required|file|mimes:pdf,doc,docx|max:20480',
        ]);

        // Store file
        $path = $request->file('cameraready_file')
            ->store('cameraready', 'public');

        // Check if already submitted
        $existing = DB::table('cameraready_submission')
            ->where('paper_id', $paper_id)
            ->first();

        if ($existing) {
            abort(403, 'Camera-ready paper has already been submitted and cannot be changed.');
        }

        DB::table('cameraready_submission')->insert([
            'paper_id' => $paper_id,
            'file_path' => $path,
            'status' => 'Submitted',
            'cameraready_status' => 'Submitted',
            'submitted_date' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('submissions.show', $paper_id)
            ->with('success', 'Camera-ready paper submitted successfully.');
    }

    public function indexByConference($conference_id)
    {
        $participantId = session('user_id');

        // Ensure conference exists
        $conference = DB::table('conference')
            ->where('conference_id', $conference_id)
            ->first();

        if (!$conference) {
            abort(404);
        }

        // Check if user is a presenter (via registration_type)
        $registration = DB::table('conference_registration')
            ->where('conference_id', $conference_id)
            ->where('participant_id', $participantId)
            ->first();

        $isPresenter = $registration && $registration->registration_type === 'presenter';

        // Get papers where user is corresponding author
        $authorPapers = DB::table('paper')
            ->join('paper_author', 'paper_author.paper_id', '=', 'paper.paper_id')
            ->leftJoin('track', 'track.track_id', '=', 'paper.track_id')
            ->leftJoin('abstract_submission', 'abstract_submission.paper_id', '=', 'paper.paper_id')
            ->leftJoin('fullpaper_submission', 'fullpaper_submission.paper_id', '=', 'paper.paper_id')
            ->leftJoin('cameraready_submission', 'cameraready_submission.paper_id', '=', 'paper.paper_id')
            ->leftJoin('presenter_assignment', 'presenter_assignment.paper_id', '=', 'paper.paper_id')
            ->where('paper.conference_id', $conference_id)
            ->where('paper_author.participant_id', $participantId)
            ->where('paper_author.is_corresponding_author', 1)
            ->select(
                'paper.paper_id',
                'paper.title',
                'paper.track_id',
                DB::raw('COALESCE(track.name, "-") as track_name'),
                'abstract_submission.abstract_status',
                'fullpaper_submission.fullpaper_status',
                'cameraready_submission.file_path as cameraready_file_path',
                'presenter_assignment.participant_id as presenter_id'
            );

        // Get papers where user is assigned presenter
        $presenterPapers = DB::table('paper')
            ->join('presenter_assignment', 'presenter_assignment.paper_id', '=', 'paper.paper_id')
            ->leftJoin('track', 'track.track_id', '=', 'paper.track_id')
            ->leftJoin('abstract_submission', 'abstract_submission.paper_id', '=', 'paper.paper_id')
            ->leftJoin('fullpaper_submission', 'fullpaper_submission.paper_id', '=', 'paper.paper_id')
            ->leftJoin('cameraready_submission', 'cameraready_submission.paper_id', '=', 'paper.paper_id')
            ->where('paper.conference_id', $conference_id)
            ->where('presenter_assignment.participant_id', $participantId)
            ->select(
                'paper.paper_id',
                'paper.title',
                'paper.track_id',
                DB::raw('COALESCE(track.name, "-") as track_name'),
                'abstract_submission.abstract_status',
                'fullpaper_submission.fullpaper_status',
                'cameraready_submission.file_path as cameraready_file_path',
                'presenter_assignment.participant_id as presenter_id'
            );

        // Union and get unique papers
        $papers = $authorPapers->union($presenterPapers)
            ->orderByDesc('paper_id')
            ->get()
            ->unique('paper_id')
            ->values();

        // Compute role + final status
        foreach ($papers as $p) {

            // Role
            $p->role = ($p->presenter_id == $participantId)
                ? 'Presenter'
                : 'Author';

            // Final Status
            if ($p->abstract_status === 'Rejected') {
                $p->final_status = 'Rejected (Abstract)';
            } elseif (!empty($p->fullpaper_status) && $p->fullpaper_status === 'Rejected') {
                $p->final_status = 'Rejected (Full Paper)';
            } elseif (!empty($p->cameraready_file_path)) {
                $p->final_status = 'Accepted (Camera-ready Submitted)';
            } elseif (!empty($p->fullpaper_status) && $p->fullpaper_status === 'Accepted') {
                $p->final_status = 'Accepted (Pending Camera-ready)';
            } elseif ($p->abstract_status === 'Accepted') {
                $p->final_status = 'Abstract Accepted';
            } elseif ($p->abstract_status === 'Submitted') {
                $p->final_status = 'Under Review';
            } else {
                $p->final_status = 'Draft';
            }
        }

        return view('submission.index', compact('conference', 'papers', 'isPresenter'));
    }

}
