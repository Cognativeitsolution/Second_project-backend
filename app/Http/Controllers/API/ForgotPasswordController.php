<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{
    public function checkEmail(Request $request) {
        $details = $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::select('id', 'name', 'email', 'verify_code')->where('email', $details['email'])->first();

        // Check if user with this email exists
        if (!is_null($user)) {
            // Generate verification code
            $code = random_int(10000, 99999);

            $user->update(['verify_code' => $code]);

            // Build details array
            $details['name'] = $user->name;
            $details['verify_code'] = $code;

            // Send verification code
            Mail::send('emails.forgot_password', $details, function($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Employment Agency Tool - Password Reset');
            });

            return [
                'success' => true,
                'message' => 'Please check your email to enter verification code to reset your password.'
            ];
        }

        else {
            return [
                'success' => false,
                'message' => 'Email is not available.'
            ];
        }
    }

    public function forgotPassword(Request $request) {
        $request->validate([
            'verify_code' => 'required',
            'password' => 'required|min:6|max:15',
            'c_password' => 'required|same:password|min:6|max:15'
        ]);

        $input = $request->all();

        $user = User::select('id', 'name', 'email')->where('verify_code', $input['verify_code'])->first();

        if (!is_null($user)) {
            $user->update([
                'password' => bcrypt($input['password']),
                'verify_code' => NULL
            ]);

            $details['name'] = $user->name;

            Mail::send('emails.password_reset', $details , function($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Employment Agency Tool - Password Reset Successful');
            });

            return [
                'success' => true,
                'message' => 'Your password has been reset successfully.'
            ];
        }

        else {
            return [
                'success' => false,
                'message' => 'Invalid verification code'
            ];
        }
    }
}
