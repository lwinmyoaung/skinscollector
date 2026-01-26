<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('register');
    }

    public function checkLoggedIn()
    {
        if (Auth::check()) {
            return redirect()->route('user.topup');
        } else {
            return view('login');
        }
    }

    public function check(Request $request)
    {
        $request->validate([
            'identity' => 'required',
            'password' => 'required',
        ]);

        $identity = $request->identity;
        $field = filter_var($identity, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = User::where($field, $identity)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'identity' => 'Email/Phone or password incorrect',
            ]);
        }

        auth()->login($user);

        // Redirect to intended url if exists, otherwise game.category
        $redirectUrl = $request->input('redirect');
        if ($redirectUrl) {
            return redirect($redirectUrl);
        }

        return redirect()->route('game.category');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        $identity = $validated['identity'];
        $isEmail = filter_var($identity, FILTER_VALIDATE_EMAIL);

        if ($isEmail) {
            if (User::where('email', $identity)->exists()) {
                return back()->withErrors(['identity' => 'Email already registered.']);
            }
        } else {
            if (User::where('phone', $identity)->exists()) {
                return back()->withErrors(['identity' => 'Phone number already registered.']);
            }
        }

        $userData = [
            'name' => '',
            'password' => $validated['password'],
            'balance' => 0,
            'role' => 'user',
        ];

        if ($isEmail) {
            $userData['email'] = $identity;
            $userData['name'] = explode('@', $identity)[0] ?: 'User';
        } else {
            $userData['phone'] = $identity;
            // Since email is unique and required, we generate a unique fake email
            $userData['email'] = $identity.'@phone.local';
            $userData['name'] = 'User'.substr(preg_replace('/\D/', '', $identity), -4);
        }

        User::create($userData);

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }



    public function wallet()
    {
        $user = auth()->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $transactionsCount = $user->topUps()->count();
        $todayTransactionsCount = $user->topUps()->whereDate('created_at', now()->today())->count();

        return view('user.wallet', compact('transactionsCount', 'todayTransactionsCount'));
    }
}
