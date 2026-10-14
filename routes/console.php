<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// The password is asked for, never passed as an argument, so it stays out of shell history.
Artisan::command('admin:create {email? : Sign-in email} {--name= : Name shown in the admin}', function () {
    $email = $this->argument('email') ?: $this->ask('Email');
    $password = $this->secret('Password (at least 8 characters)');

    $validator = Validator::make(compact('email', 'password'), [
        'email' => ['required', 'email', 'max:255'],
        'password' => ['required', 'string', 'min:8'],
    ]);

    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }

        return 1;
    }

    $user = User::firstOrNew(['email' => $email]);
    $user->name = $this->option('name') ?: ($user->name ?: 'Organiser');
    $user->password = Hash::make($password);
    $user->save();

    $this->info(($user->wasRecentlyCreated ? 'Created' : 'Reset the password for')." {$email} — sign in at ".route('admin.login'));
})->purpose("Create an organiser login for /admin, or reset an existing one's password");
