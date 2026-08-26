<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Factories\UserAccountFactory;
use App\Models\Organisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'organisation_name' => ['required', 'string', 'max:255'],
        ]);

        // 1. Create Organisation
        $organisation = Organisation::create([
            'name' => $request->organisation_name,
            'status' => 'pending_verification',
        ]);

        // 2. Instantiate User via Factory Pattern
        $user = UserAccountFactory::create('Organisation User', [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'organisation_id' => $organisation->id,
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}