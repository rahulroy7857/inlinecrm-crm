<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ChangePasswordController extends Controller
{
    public function index()
    {
        return view('admin.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) {
                if (!Hash::check($value, auth()->guard('admin')->user()->password)) {
                    $fail('The current password is incorrect.');
                }
            }],
            'new_password' => [
                'required',
                'different:current_password',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'confirmed'
            ],
            'new_password_confirmation' => 'required'
        ], [
            'new_password.different' => 'The new password must be different from the current password.',
            'new_password.confirmed' => 'The password confirmation does not match.'
        ]);

        $user = auth()->guard('admin')->user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        // Log the password change
        ActivityLogger::log(
            "Password changed for user: {$user->name}",
            'Update',
            $user,
            ['user_id' => $user->id]
        );

        return redirect()->back()->with('success', 'Password changed successfully!');
    }
}