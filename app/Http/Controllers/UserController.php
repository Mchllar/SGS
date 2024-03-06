<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\SendOtpMail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Mail\SendResetLinkEmail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    // Show login form
    public function login()
    {
        return view("auth.login");
    }
    
    // Show registration form
    public function register()
    {
        return view("auth.register");
    }

    // Show 2FA verification form for registration
    public function regOTP()
    {
        return view("auth.verify-2fa");
    }

    // Show OTP verification form for login
    public function logOTP()
    {
        return view("auth.logOTP");
    }

    // Show forgot password form
    public function showForgotPasswordForm()
    {
        return view('auth.forgot_password');
    }

    // Show reset password form
    public function showResetPasswordForm($token)
    {
        return view('auth.reset_password', ['token' => $token]);
    }

    // Show email sent notification
    public function showemailwassent()
    {
        return view("auth.emailsent");
    }

    // Show landing page based on user role
    public function showLandingPage()
    {
        $role = auth()->user()->role;
    
        switch ($role) {
            case 'staff':
                return view('staff.landing');
            case 'supervisor':
                return view('supervisor.landing');
            default:
                return view('student.landing');
        }
    }

    // Logout
    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
     
        return redirect('/')->with('message', 'You have been logged out successfully!');
    }

    // Register users
    public function store(Request $request)
    {
        // Validate registration form fields
        $formFields = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email', Rule::unique('users', 'email')],
            'password' => ['required', 'min:6'],
            'profile' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust max file size as needed
            'role' => ['required', Rule::in(['student', 'supervisor', 'staff'])],
        ]);

        // Handle different roles
switch ($formFields['role']) {
    case 'student':
        // Validate and store student-specific fields
        $studentFields = $request->validate([
            'student_number' => 'required',
            'phone_number' => 'required',
            'date_of_birth' => 'required|date',
            'gender' => 'required',
            'nationality' => 'required',
            'religion' => 'required',
            'school' => 'required',
            'programme' => 'required',
            'intake' => 'required',
            'previous_school' => 'required',
            'status' => 'required',
        ]);

        // Merge student-specific fields into the main form fields
        $formFields = array_merge($formFields, $studentFields);
        break;
    case 'supervisor':
        // Validate and store supervisor-specific fields
        $supervisorFields = $request->validate([
            'curriculum_vitae' => 'required',
            'contract' => 'required',
        ]);

        // Merge supervisor-specific fields into the main form fields
        $formFields = array_merge($formFields, $supervisorFields);
        break;
    case 'staff':
        // No specific fields for staff, so no validation needed
        break;
}


        //get profile image
        if ($request->hasFile('profile')) {
            $formFields['profile'] = $request->file('profile')->store('profiles', 'public');
        }
    
<<<<<<< HEAD
        // Hash Password
        $formFields['password'] = bcrypt($formFields['password']);

        $formFields['role'] = $request->input('role');
=======
    public function conferenceReview(){
        return view('student.conference_review');
    }

    public function thesisCorrection(){
        return view('student.thesis_correction');
    }

    public function thesisSubmission(){
        return view('student.thesis_submission');
    }

    public function noticeSubmission(){
        return view ('student.notice'); 
    }
>>>>>>> 2a8a8ab385e5d55a75b9137f1a07c6de61325a7f
    
        // Generate OTP
        $otp = rand(100000, 999999);
    
        // Store user details and OTP in session for OTP verification
        session([
            'user_details' => $formFields, 
            'otp_code' => $otp, 
            'email' => $formFields['email'], 
            'otp_created_at' => now()
        ]);
    
        // Send OTP to user's email
        Mail::to($formFields['email'])->send(new SendOtpMail($otp));
    
        // Redirect to OTP verification page
        return redirect('/verify-registration-otp');
    }

    // Verify registration OTP
    public function verifyRegistrationOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $email = session('email');
        $otp_code = session('otp_code');
        $otp_created_at = session('otp_created_at');

        if ($email && $otp_code && $otp_created_at) {
            if ($request->otp == $otp_code) {
                // OTP is valid, complete the registration process
                if (Carbon::parse($otp_created_at)->addMinutes(2)->isPast()) {
                    return redirect('/verify-registration-otp')->with('error', 'OTP has expired. Please resend.');
                } else {
                    // Create User
                    $user = User::create($request->session()->get('user_details'));

                    // Clear the OTP code
                    $user->otp_code = null;
                    $user->save();

                    // Log the user in
                    auth()->login($user);

                    // Clear the session data
                    $request->session()->forget(['email', 'otp_code', 'otp_created_at', 'user_details']);

                    return redirect('/')->with('message', 'Registration successful!');
                }
            } else {
                // Invalid OTP
                return redirect('/verify-registration-otp')->with('error', 'Invalid OTP.');
            }
        } else {
            // Session data missing
            return redirect('/verify-registration-otp')->with('error', 'Session data missing.');
        }
    }

    // Authenticate user
    public function authenticate(Request $request)
    {
        $formFields = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        // Retrieve the user instance directly from the database
        $user = User::where('email', $formFields['email'])->first();

        // Check if the user exists and the password is correct
        if ($user && Hash::check($formFields['password'], $user->password)) {
            // Generate OTP
            $otp = rand(100000, 999999);
            $user->otp_code = $otp;
            $user->otp_created_at = now();
            $user->save();

            // Store email in session for OTP verification
            session(['email' => $formFields['email']]);

            // Send OTP to user's email
            Mail::to($user->email)->send(new SendOtpMail($otp));

            // Redirect to OTP verification page
            return redirect('/verify-login-otp');
        } else {
            return redirect('/login')->with('error', 'Wrong credentials!!')->with('showResetLink', true);
        }
    }

    // Verify login OTP
    public function verifyLoginOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $email = session('email');
        $user = User::where('email', $email)->first();

        if ($user && $request->otp == $user->otp_code) {
            // OTP is valid, complete the login process

            // Check if 2 mins
            if (Carbon::parse($user->otp_created_at)->addMinutes(2)->isPast()) {
                return redirect('/verify-login-otp')->with('error', 'OTP has expired. Please resend.');
            } else {
                $user->otp_code = null; // Clear the OTP code
                $user->save();

                // Log the user in
                auth()->login($user);

                // Clear the email from the session
                $request->session()->forget('email');

                return redirect('/')->with('message', 'You are now logged in!');
            }
        } else {
            // OTP is invalid, redirect back with an error message
            return redirect('/verify-login-otp')->with('error', 'Invalid OTP.');
        }
    }

    // Resend login OTP
    public function resendOtp()
    {
        $email = session('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            // Generate a new OTP and store the generation time
            $otp = rand(100000, 999999);
            $user->otp_code = $otp;
            $user->otp_created_at = now();
            $user->save();

            // Send the new OTP to the user's email
            Mail::to($user->email)->send(new SendOtpMail($otp));

            return redirect('/verify-login-otp')->with('message', 'A new OTP has been sent to your email.');
        } else {
            return redirect('/verify-login-otp')->with('error', 'Error resending OTP. Please try again.');
        }
    }

    // Resend registration OTP
    public function resendRegOtp()
    {
        $email = session('email');
        $userDetails = session('user_details');

        if ($email && $userDetails) {
            // Generate a new OTP and store the generation time
            $otp = rand(100000, 999999);

            // Update the OTP and OTP creation time in the session
            session(['otp_code' => $otp, 'otp_created_at' => now()]);

            // Send the new OTP to the user's email
            Mail::to($email)->send(new SendOtpMail($otp));

            return redirect('/verify-registration-otp')->with('message', 'A new OTP has been sent to your email.');
        } else {
            return redirect('/verify-registration-otp')->with('error', 'Error resending OTP. Please try again.');
        }
    }
}