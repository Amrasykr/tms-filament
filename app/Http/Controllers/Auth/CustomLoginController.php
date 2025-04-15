<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\User;

class CustomLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Coba login sebagai admin
        if ($admin = User::where('email', $request->email)->first()) {
            if (Hash::check($request->password, $admin->password)) {
                Auth::guard('admin')->login($admin);
                return redirect()->intended('/admin');
            }
        }

        // Coba login sebagai teacher
        if ($teacher = Teacher::where('email', $request->email)->first()) {
            if (Hash::check($request->password, $teacher->password)) {
                Auth::guard('teacher')->login($teacher);
                return redirect()->intended('/teacher');
            }
        }

        // Coba login sebagai student
        if ($student = Student::where('email', $request->email)->first()) {
            if (Hash::check($request->password, $student->password)) {
                Auth::guard('student')->login($student);
                return redirect()->intended('/student');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        foreach (['admin', 'teacher', 'student'] as $guard) {
            Auth::guard($guard)->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
