<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ConferenceRequestController;
use App\Http\Controllers\ConferenceSetupController;
use App\Http\Controllers\ParticipantConferenceController;
use App\Http\Controllers\ConferenceRegistrationController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\CommitteePaperController;
use App\Http\Controllers\CommitteeDashboardController;
use App\Http\Controllers\ReviewAssignmentController;
use App\Http\Controllers\ReviewerReviewController;
use App\Http\Controllers\CommitteeReviewController;
use App\Http\Controllers\CommitteeReviewerController;
use App\Http\Controllers\ReviewerProfileController;
use App\Http\Controllers\ParticipantAuthController;
use App\Http\Controllers\ParticipantProfileController;
use App\Http\Controllers\ConferenceTrackController;
use App\Http\Controllers\ConferenceDeadlineController;
use App\Http\Controllers\ConferenceFeeController;
use App\Http\Controllers\CommitteeParticipantController;
use App\Http\Controllers\CommitteePaymentController;
use App\Http\Controllers\CommitteeProfileController;
use App\Http\Controllers\CommitteeAdminController;
use App\Http\Controllers\NotificationController;



Route::get('/', [ParticipantConferenceController::class, 'index'])->name('home');
Route::get('/conferences', [ParticipantConferenceController::class, 'index'])->name('participant.conferences');
Route::get('/conferences/{conference_id}', [ParticipantConferenceController::class, 'show'])->name('participant.conference.show');

// Participant register
Route::get('/register', [ParticipantAuthController::class, 'showRegister'])
    ->name('participant.register.form');

Route::post('/register', [ParticipantAuthController::class, 'register'])
    ->name('participant.register.do');

// Auth routes
Route::get('/login', [LoginController::class, 'show'])->name('login.form');
Route::get('/committee/login', [LoginController::class, 'showCommitteeLogin'])->name('committee.login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1')->name('login.do');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');



// ===== Public: Conference Request Submission =====
Route::get('/conference-request', [ConferenceRequestController::class, 'create'])->name('conference.request.create');
Route::post('/conference-request', [ConferenceRequestController::class, 'store'])->name('conference.request.store');

Route::middleware(['session.auth'])->group(function () {

    // ===== Participant Profile =====
    Route::get('/participant/profile', [ParticipantProfileController::class, 'show'])->name('participant.profile.show');
    Route::post('/participant/profile', [ParticipantProfileController::class, 'update'])->name('participant.profile.update');
    Route::get('/participant/profile/password', [ParticipantProfileController::class, 'editPassword'])->name('participant.profile.password.edit');
    Route::post('/participant/profile/password', [ParticipantProfileController::class, 'updatePassword'])->name('participant.profile.password.update');

    // ===== Demo RBAC Routes =====
    Route::get('/superadmin/requests', function () {
        return view('demo.superadmin_requests');
    })->middleware('perm:APPROVE_CONFERENCE');

    Route::get('/manager/admin-assignment', function () {
        return view('demo.manager_assignment');
    })->middleware('perm:MANAGE_COMMITTEE');


    // ===== Conference Request =====

    Route::middleware('perm:APPROVE_CONFERENCE')->group(function () {
        Route::get(
            '/superadmin/conference-requests',
            [ConferenceRequestController::class, 'index']
        )->name('conference.request.index');

        Route::get(
            '/superadmin/conference-requests/{id}',
            [ConferenceRequestController::class, 'show']
        )->name('conference.request.show');

        Route::post(
            '/superadmin/conference-requests/{id}/approve',
            [ConferenceRequestController::class, 'approve']
        )->name('conference.request.approve');

        Route::post(
            '/superadmin/conference-requests/{id}/reject',
            [ConferenceRequestController::class, 'reject']
        )->name('conference.request.reject');
    });


    // ===== Conference Setup & Management =====
    Route::middleware('perm:MANAGE_CONFERENCE')->group(function () {
        // Detailed Info
        Route::get('/conference/{conference_id}/setup', [ConferenceSetupController::class, 'edit'])->name('conference.setup.edit');
        Route::post('/conference/{conference_id}/setup', [ConferenceSetupController::class, 'update'])->name('conference.setup.update');

        // Contents
        Route::get('/conference/{conference_id}/contents', [ConferenceSetupController::class, 'editContents'])->name('conference.contents.edit');
        Route::post('/conference/{conference_id}/contents', [ConferenceSetupController::class, 'updateContents'])->name('conference.contents.update');

        // Status
        Route::get('/conference/{conference_id}/status', [ConferenceSetupController::class, 'editStatus'])->name('conference.status.edit');
        Route::post('/conference/{conference_id}/activate', [ConferenceSetupController::class, 'activate'])->name('conference.activate');

        // Tracks (CRUD)
        Route::get('/conference/{conference_id}/tracks', [ConferenceTrackController::class, 'index'])->name('conference.tracks.index');
        Route::post('/conference/{conference_id}/tracks', [ConferenceTrackController::class, 'store'])->name('conference.tracks.store');
        Route::post('/conference/{conference_id}/tracks/{track_id}/delete', [ConferenceTrackController::class, 'destroy'])->name('conference.tracks.destroy');

        // Deadlines
        Route::get('/conference/{conference_id}/deadlines', [ConferenceDeadlineController::class, 'index'])->name('conference.deadlines.index');
        Route::post('/conference/{conference_id}/deadlines', [ConferenceDeadlineController::class, 'update'])->name('conference.deadlines.update');

        // Fees
        Route::get('/conference/{conference_id}/fees', [ConferenceFeeController::class, 'index'])->name('conference.fees.index');
        Route::post('/conference/{conference_id}/fees', [ConferenceFeeController::class, 'update'])->name('conference.fees.update');
        Route::post('/conference/{conference_id}/fees/{fee_id}/delete', [ConferenceFeeController::class, 'destroy'])->name('conference.fees.destroy');

        // Participants
        Route::get('/conference/{conference_id}/participants', [CommitteeParticipantController::class, 'index'])->name('committee.participants.index');
        Route::get('/conference/{conference_id}/participants/{participant_id}', [CommitteeParticipantController::class, 'show'])->name('committee.participants.show');

        // Payments
        Route::get('/conference/{conference_id}/payments', [CommitteePaymentController::class, 'index'])->name('committee.payments.index');

        // My Account
        Route::get('/conference/{conference_id}/my-account', [CommitteeProfileController::class, 'show'])->name('committee.profile.show');
        Route::post('/conference/{conference_id}/my-account', [CommitteeProfileController::class, 'update'])->name('committee.profile.update');
        Route::get('/conference/{conference_id}/my-account/password', [CommitteeProfileController::class, 'editPassword'])->name('committee.profile.password.edit');
        Route::post('/conference/{conference_id}/my-account/password', [CommitteeProfileController::class, 'updatePassword'])->name('committee.profile.password.update');
    });





    // ===== Conference Registration =====
    Route::post(
        '/conferences/{conference_id}/register',
        [ConferenceRegistrationController::class, 'store']
    )->name('conference.register');

    Route::get(
        '/conferences/{conference_id}/my-registration',
        [ConferenceRegistrationController::class, 'show']
    )->name('participant.registration.show');

    Route::post(
        '/conferences/{conference_id}/pay-listener',
        [ConferenceRegistrationController::class, 'payListener']
    )->name('listener.pay');



    // ===== Notifications =====
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification_id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');


    // ===== Submission List =====

    Route::get('/conferences/{conference_id}/my-submissions/create', [SubmissionController::class, 'create'])
        ->name('submissions.create');

    Route::post('/conferences/{conference_id}/my-submissions', [SubmissionController::class, 'store'])
        ->name('submissions.store');

    Route::get('/my-submissions/{paper_id}', [SubmissionController::class, 'show'])
        ->name('submissions.show');

    Route::post('/my-submissions/{paper_id}/update', [SubmissionController::class, 'update'])
        ->name('submissions.update');

    Route::post(
        '/my-submissions/{paper_id}/withdraw',
        [SubmissionController::class, 'withdraw']
    )->middleware('perm:SUBMIT_PAPER')
        ->name('submissions.withdraw');


    //== Committee Papers ==
    Route::get(
        '/conference/{conference_id}/papers',
        [CommitteePaperController::class, 'index']
    )
        ->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.papers.index');

    Route::get(
        '/conference/{conference_id}/papers/{paper_id}',
        [CommitteePaperController::class, 'show']
    )
        ->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.papers.show');

    Route::post(
        '/conference/{conference_id}/papers/{paper_id}/abstract-decision',
        [CommitteePaperController::class, 'decideAbstract']
    )
        ->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.papers.decide.abstract');

    //== Full Paper ==
    Route::post(
        '/my-submissions/{paper_id}/fullpaper-upload',
        [SubmissionController::class, 'uploadFullPaper']
    )->name('submissions.fullpaper.upload');


    //== Full Paper Decision ==
    Route::post(
        '/conference/{conference_id}/papers/{paper_id}/fullpaper-decision',
        [CommitteePaperController::class, 'decideFullPaper']
    )
        ->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.papers.decide.fullpaper');


    //== Full Paper Withdraw ==
    Route::post(
        '/my-submissions/{paper_id}/fullpaper-withdraw',
        [SubmissionController::class, 'withdrawFullPaper']
    )->name('submissions.fullpaper.withdraw');


    //== Select Presenter ==
    Route::post(
        '/my-submissions/{paper_id}/select-presenter',
        [SubmissionController::class, 'selectPresenter']
    )->name('submissions.presenter.select');


    //== Presenter Payment ==
    Route::post(
        '/presenter/pay/{paper_id}',
        [SubmissionController::class, 'payPresenter']
    )->name('presenter.pay');


    //== Camera Ready ==
    Route::post(
        '/my-submissions/{paper_id}/camera-ready',
        [SubmissionController::class, 'uploadCameraReady']
    )->name('submissions.cameraready.upload');

    //== My Submissions ==
    Route::get(
        '/conference/{conference_id}/my-submissions',
        [SubmissionController::class, 'indexByConference']
    )->name('conference.submissions.index');

    Route::get(
        '/conference/{conference_id}/my-submissions/create',
        [SubmissionController::class, 'create']
    )->name('conference.submissions.create');


    //== Committee Dashboard ==
    Route::get(
        '/conference/{conference_id}/committee/dashboard',
        [CommitteeDashboardController::class, 'show']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.dashboard');


    //== Committee Team Management ==
    Route::get(
        '/conference/{conference_id}/committee-team',
        [CommitteeAdminController::class, 'index']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.admins.index');

    Route::get(
        '/conference/{conference_id}/committee-team/{admin_id}',
        [CommitteeAdminController::class, 'show']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.admins.show');

    Route::post(
        '/conference/{conference_id}/committee-team/{admin_id}/update-name',
        [CommitteeAdminController::class, 'updateName']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.admins.updateName');

    Route::post(
        '/conference/{conference_id}/committee-team/invite-manager',
        [CommitteeAdminController::class, 'inviteManager']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.admins.invite.manager');

    Route::post(
        '/conference/{conference_id}/committee-team/store',
        [CommitteeAdminController::class, 'store']
    )->middleware('perm:MANAGE_COMMITTEE')
        ->name('committee.admins.store');

    Route::delete(
        '/conference/{conference_id}/committee-team/{admin_id}',
        [CommitteeAdminController::class, 'destroy']
    )->middleware('perm:MANAGE_COMMITTEE')
        ->name('committee.admins.destroy');

    Route::post(
        '/conference/{conference_id}/committee-team/{admin_id}/access-level',
        [CommitteeAdminController::class, 'updateAccessLevel']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.admins.updateAccessLevel');




    //== Committee: assign reviewers ==
    // Route::get(
    //     '/conference/{conference_id}/review-assignments',
    //     [ReviewAssignmentController::class, 'index']
    // )->middleware('perm:MANAGE_CONFERENCE')
    //     ->name('committee.review.assignments.index');


    //== Reviewer: my reviews + submit review ==
    Route::get(
        '/reviewer/my-reviews',
        [ReviewerReviewController::class, 'index']
    )->middleware('perm:REVIEW_PAPER')
        ->name('reviewer.reviews.index');

    Route::get(
        '/reviewer/my-reviews/{review_id}',
        [ReviewerReviewController::class, 'show']
    )->middleware('perm:REVIEW_PAPER')
        ->name('reviewer.reviews.show');

    Route::post(
        '/reviewer/my-reviews/{review_id}',
        [ReviewerReviewController::class, 'submit']
    )->middleware('perm:REVIEW_PAPER')
        ->name('reviewer.reviews.submit');


    //== Committee Review == 
    Route::get(
        '/conference/{conference_id}/papers/{paper_id}/reviews',
        [CommitteeReviewController::class, 'paperReviews']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.papers.reviews');

    Route::get(
        '/conference/{conference_id}/papers/{paper_id}/reviews/{review_id}',
        [CommitteeReviewController::class, 'reviewDetails']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.review.details');


    //== Committee: view all reviewers ==
    Route::get(
        '/conference/{conference_id}/reviewers',
        [CommitteeReviewerController::class, 'allReviewers']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.reviewers.all');



    //== Committee: assign reviewers ==
    Route::post(
        '/conference/{conference_id}/papers/{paper_id}/assign-reviewer/{reviewer_id}',
        [CommitteeReviewController::class, 'assignReviewer']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.review.assign');





    //== Committee: appoint reviewer ==
    Route::post(
        '/conference/{conference_id}/reviewers/{reviewer_id}/appoint',
        [CommitteeReviewerController::class, 'appoint']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.reviewers.appoint');


    // == Committee: invite new reviewer (create reviewer account) ==
    Route::get(
        '/conference/{conference_id}/reviewers/invite',
        [CommitteeReviewerController::class, 'create']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.reviewers.create');

    Route::post(
        '/conference/{conference_id}/reviewers/invite',
        [CommitteeReviewerController::class, 'store']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.reviewers.store');


    // == Committee: reviewer detail ==
    Route::get(
        '/conference/{conference_id}/reviewers/{reviewer_id}',
        [CommitteeReviewerController::class, 'show']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.reviewers.show');


    //== Committee: remove reviewer from conference ==
    Route::post(
        '/conference/{conference_id}/reviewers/{reviewer_id}/remove',
        [CommitteeReviewerController::class, 'remove']
    )->middleware('perm:MANAGE_CONFERENCE')
        ->name('committee.reviewers.remove');


    //== Reviewer Profile ==
    Route::get('/reviewer/profile', [ReviewerProfileController::class, 'show'])
        ->middleware('perm:REVIEW_PAPER')
        ->name('reviewer.profile.show');

    Route::post('/reviewer/profile', [ReviewerProfileController::class, 'update'])
        ->middleware('perm:REVIEW_PAPER')
        ->name('reviewer.profile.update');

    //== Reviewer Password ==
    Route::get('/reviewer/profile/password', [ReviewerProfileController::class, 'editPassword'])
        ->middleware('perm:REVIEW_PAPER')
        ->name('reviewer.profile.password.edit');

    Route::post('/reviewer/profile/password', [ReviewerProfileController::class, 'updatePassword'])
        ->middleware('perm:REVIEW_PAPER')
        ->name('reviewer.profile.password.update');

    //== Presenter Confirmation ==
    Route::post(
        '/conferences/{conference_id}/confirm-presenter',
        [ConferenceRegistrationController::class, 'confirmPresenter']
    )->name('presenter.confirm');




});

