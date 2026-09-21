<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

// Helper: participant session with a notification
function participantWithNotification(): array
{
    $id = DB::table('participant')->insertGetId([
        'name'              => 'Notif User',
        'email'             => 'notif@test.com',
        'password'          => Hash::make('pass'),
        'registration_date' => now(),
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);

    DB::table('notification')->insert([
        'participant_id' => $id,
        'message'        => 'Your abstract was accepted.',
        'status'         => 'unread',
        'created_at'     => now(),
        'updated_at'     => now(),
    ]);

    $notifId = DB::table('notification')
        ->where('participant_id', $id)
        ->value('notification_id');

    return ['user_id' => $id, 'user_type' => 'participant', '_id' => $id, '_notif_id' => $notifId];
}

// --- Get Notifications (JSON) ---

it('participant can get their notifications as JSON', function () {
    $session = participantWithNotification();

    $response = $this->withSession($session)->get('/notifications');

    $response->assertStatus(200);
    $response->assertJsonStructure(['notifications', 'unread_count']);
});

it('non-participant gets empty JSON', function () {
    $this->withSession(['user_id' => 1, 'user_type' => 'conference_admin'])
        ->get('/notifications')
        ->assertStatus(403);
});

// --- Mark Single Notification as Read ---

it('participant can mark a notification as read', function () {
    $session = participantWithNotification();
    $notifId = $session['_notif_id'];

    $response = $this->withSession($session)
        ->post("/notifications/{$notifId}/read");

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);
    $this->assertDatabaseHas('notification', ['notification_id' => $notifId, 'status' => 'read']);
});

// --- Mark All Notifications as Read ---

it('participant can mark all notifications as read', function () {
    $session = participantWithNotification();

    $response = $this->withSession($session)
        ->post('/notifications/read-all');

    $response->assertStatus(200);
    $response->assertJson(['success' => true]);

    $unread = DB::table('notification')
        ->where('participant_id', $session['_id'])
        ->where('status', 'unread')
        ->count();

    $this->assertEquals(0, $unread);
});
