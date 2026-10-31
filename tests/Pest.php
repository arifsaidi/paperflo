<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function createTestConference(array $attributes = []): \App\Models\Conference
{
    static $cCount = 0;
    $cCount++;
    return \App\Models\Conference::create(array_merge([
        'name' => 'Test Conference ' . $cCount,
        'conference_email' => "conf{$cCount}_" . uniqid() . '@conf.com',
        'conference_phone' => '123456789',
        'secretariat_address' => 'Secretariat Address',
        'conference_date' => now()->addMonths(6),
        'conference_status' => 'draft',
    ], $attributes));
}

function createTestParticipant(array $attributes = []): int
{
    static $pCount = 0;
    $pCount++;
    return \Illuminate\Support\Facades\DB::table('participant')->insertGetId(array_merge([
        'name' => 'Test Participant ' . $pCount,
        'email' => "participant{$pCount}_" . uniqid() . '@test.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'phone_num' => '1234567890',
        'registration_date' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ], $attributes));
}

function createTestSuperAdmin(array $attributes = []): int
{
    static $sCount = 0;
    $sCount++;
    return \Illuminate\Support\Facades\DB::table('super_admin')->insertGetId(array_merge([
        'name' => 'Super Admin ' . $sCount,
        'email' => "superadmin{$sCount}_" . uniqid() . '@test.com',
        'password' => \Illuminate\Support\Facades\Hash::make('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ], $attributes));
}
