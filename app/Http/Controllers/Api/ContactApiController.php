<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\UserMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Mail\ContactMail;

class ContactApiController extends Controller
{
    // public function showForm()
    // {
    //     return view('contact.form');//display the contact form
    // }

    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required', // array or string
            'phone' => 'required|digits_between:10,15',
            'description' => 'required|string'
        ]);

        $userEmails = is_array($request->email) ? $request->email : [$request->email];
        $adminEmail = 'sanyamrehi@gmail.com';

        $baseDetails = $request->only(['name', 'email', 'phone', 'description']);

        // 1. Send mail to all users
        foreach ($userEmails as $userEmail) {
            $detailsForUser = $baseDetails;
            $detailsForUser['email'] = $userEmail;
            Mail::to($userEmail)->send(new ContactMail($detailsForUser));
        }

        // 2. Send mail to admin with their email shown
        $adminDetails = $baseDetails;
        $adminDetails['email'] = $adminEmail;
        Mail::to($adminEmail)->send(new UserMail($adminDetails));

        return response()->json([
            'status' => true,
            'message' => 'Mails sent to users and admin successfully.',
        ]);
    }

}
