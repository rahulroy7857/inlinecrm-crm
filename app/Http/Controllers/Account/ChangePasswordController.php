<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    public function index()
    {
        return view('account.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, auth()->guard('account')->user()->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'new_password' => [
                'required',
                'different:current_password',
                Password::min(8)->mixedCase()->numbers()->symbols(),
                'confirmed',
            ],
        ]);

        $user = auth()->guard('account')->user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        ActivityLogger::log(
            "Password changed for account user: {$user->name}",
            'Update',
            $user,
            ['user_id' => $user->id]
        );

        return redirect()->back()->with('success', 'Password changed successfully.');
    }
}
