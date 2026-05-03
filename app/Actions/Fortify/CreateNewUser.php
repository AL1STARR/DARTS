<?php

namespace App\Actions\Fortify;

use App\Mail\VerifyEmailMail;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'department' => ['required', 'string'],
            'role'       => ['required', 'string'],
            'password'   => $this->passwordRules(),
            'terms'      => ['accepted'],
        ])->validate();

        $user = User::create([
            'name'       => $input['first_name'] . ' ' . $input['last_name'],
            'first_name' => $input['first_name'],
            'last_name'  => $input['last_name'],
            'email'      => $input['email'],
            'department' => $input['department'],
            'role'       => $input['role'],
            'password'   => Hash::make($input['password']),
            'is_admin'   => false,
            'status'     => 'pending',
        ]);

        AuditLog::record('user_requested', $user,
            "New access request submitted by {$user->first_name} {$user->last_name} ({$user->role}, {$user->department})"
        );

        Mail::to($user->email)->send(new VerifyEmailMail($user));

        return $user;
    }
}
