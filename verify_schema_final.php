<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$tables = [
    'conference_admin' => ['admin_id', 'name', 'email', 'password'],
    'conference_admin_assignment' => ['assignment_id', 'assigned_date', 'access_level', 'admin_id', 'conference_id'],
    'notification' => ['notification_id', 'content', 'link', 'status', 'conference_id', 'admin_id', 'reviewer_id', 'participant_id'],
    'super_admin' => ['superadmin_id', 'name', 'email', 'password'],
    'conference_request' => ['request_id', 'conference_title', 'org_name', 'org_type', 'org_address', 'applicant_name', 'applicant_email', 'applicant_phone', 'status', 'submitted_date', 'participant_id', 'superadmin_id'],
    'participant' => ['participant_id', 'name', 'email', 'password', 'institution', 'faculty', 'department', 'address', 'phone_num', 'registration_date'],
    'conference' => ['conference_id', 'name', 'logo', 'chairman_name', 'chairman_email', 'conference_phone', 'conference_email', 'conference_website', 'secretariat_address', 'conference_date', 'publication', 'conference_venue', 'contents', 'organizer_participant_id'],
    'deadline' => ['deadline_id', 'abstract_deadline', 'fullpaper_deadline', 'cameraready_paper_deadline', 'participant_registration_deadline', 'review_deadline', 'listener_payment_deadline', 'presenter_payment_deadline', 'conference_id'],
    'track' => ['track_id', 'name', 'conference_id'],
    'paper' => ['paper_id', 'title', 'conference_id', 'track_id'],
    'paper_author' => ['paper_author_id', 'name', 'author_name', 'author_email', 'is_corresponding_author', 'paper_id', 'participant_id'],
    'abstract_submission' => ['abstract_id', 'abstract_text', 'status', 'abstract_status', 'submitted_date', 'paper_id'],
    'fullpaper_submission' => ['fullpaper_id', 'file_path', 'status', 'fullpaper_status', 'submitted_date', 'paper_id'],
    'cameraready_submission' => ['cameraready_id', 'file_path', 'status', 'cameraready_status', 'submitted_date', 'paper_id'],
    'reviewer' => ['reviewer_id', 'name', 'title', 'email', 'password', 'area', 'institution'],
    'conference_reviewer_assignment' => ['conf_reviewer_id', 'invite_date', 'invite_status', 'reviewer_type', 'reviewer_id', 'conference_id'],
    'review_assignment' => ['review_id', 'score', 'comment', 'review_status', 'fullpaper_id', 'reviewer_id'],
    'conference_registration' => ['registration_id', 'registration_date', 'registration_type', 'registration_category', 'registration_status', 'participant_id', 'conference_id'],
    'listener_payment' => ['listener_payment_id', 'name', 'amount', 'payment_status', 'registration_id'],
    'withdrawal_request' => ['withdrawal_request_id', 'withdrawal_stage', 'reason', 'request_date', 'paper_id', 'participant_id'],
    'presenter_assignment' => ['presenter_assignment_id', 'presenter_status', 'paper_id', 'participant_id'],
    'presenter_payment' => ['presenter_payment_id', 'amount', 'status', 'paid_date', 'presenter_assignment_id'],
    'role' => ['role_id', 'role_name'],
    'permission' => ['permission_id', 'permission_code', 'description'],
    'role_permission' => ['role_id', 'permission_id'],
    'user_role' => ['user_role_id', 'user_type', 'user_id', 'role_id', 'scope_type', 'scope_id', 'assigned_date'],
];

$missing = [];
foreach ($tables as $table => $columns) {
    if (!Schema::hasTable($table)) {
        $missing[] = "Table $table is missing!";
        continue;
    }
    foreach ($columns as $column) {
        if (!Schema::hasColumn($table, $column)) {
            $missing[] = "Table $table is missing column: $column";
        }
    }
}

if (empty($missing)) {
    echo "SUCCESS: All 26 tables and their attributes exist correctly.\n";
} else {
    echo "FAILURE:\n" . implode("\n", $missing) . "\n";
}
