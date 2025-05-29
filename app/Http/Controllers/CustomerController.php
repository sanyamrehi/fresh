<?php

namespace App\Http\Controllers;
use App\Models\customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;


class CustomerController extends Controller
{
    public function showRegisterForm()
    {
        return view('customer.register');//display the register page
    }

    public function showLoginForm()
{

    return view('customer.login');//display the login page
}

    public function register(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . customer::class],
            'phone' => ['required', 'string', 'max:15', 'unique:' . customer::class], // Ensure phone is unique
            'password' => ['required', 'string', 'min:6', 'confirmed'],//ensure password is matches
        ]);

        Customer::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'confirm_password' => Hash::make($request->confirm_password),
        ]);//use to store the value from its table

        return redirect('customer/login')->with('success', 'Registration successful! Please log in.');//return to login page
    }

    public function login(LoginRequest $request): RedirectResponse
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    // Fetch customer from the database
    $customer = Customer::where('email', $request->email)->first();

    // Check if customer exists
    if (!$customer) {
        return back()->withErrors(['email' => 'No account found with this email']);
    }

    // Check if password matches
    if (!Hash::check($request->password, $customer->password)) {
        return back()->withErrors(['password' => 'Please enter the correct password'])->withInput();
    }

    // Authenticate user
    Auth::login($customer);

    // Store customer details in session
    $request->session()->put('customer', $customer);

    // Regenerate session ID for security
    $request->session()->regenerate();

    return redirect('product/index')->with('success', 'Login successful!');//redirects the success message and display the product page
}
// Redirect to Google
public function redirectToGoogle()
{
    return Socialite::driver('google')->redirect();
}

// Handle Google callback
public function handleGoogleCallback(Request $request)
{
    try {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Check if customer already exists
        $customer = Customer::where('email', $googleUser->getEmail())->first();

        if (!$customer) {
            // Create a new customer
            $customer = Customer::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(uniqid()), // Temporary random password
            ]);
        }

        // Login the customer
        Auth::login($customer);

        // Store in session
        $request->session()->put('customer', $customer);
        $request->session()->regenerate();

        return redirect('product/index')->with('success', 'Login successful via Google!');
    } catch (\Exception $e) {
        return redirect('customer/login')->with('error', 'Google login failed. Try again.');
    }
}


}

