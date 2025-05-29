<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        // ✅ Validate request data
        $request->validate([
            'selected_address_id' => 'nullable|exists:address,id', // Table name should match actual table
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|digits:6',
        ]);

        // ✅ Get authenticated user ID
        $customerId = auth()->id();

        if (!$customerId) {
            return redirect()->back()->with('error', 'User not authenticated.');
        }

        $selectedAddressId = null;

        try {
            // ✅ If new address data is provided, create a new address
            if ($request->filled(['address', 'city', 'state', 'pincode'])) {
                $newAddress = Address::create([
                    'user_id' => $customerId,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'pincode' => $request->pincode,
                ]);

                $selectedAddressId = $newAddress->id;
            } elseif ($request->filled('selected_address_id')) {
                // ✅ Use existing address from dropdown
                $selectedAddressId = $request->selected_address_id;
            }

            // ✅ Ensure we have a valid address
            if (!$selectedAddressId) {
                return redirect()->back()->with('error', 'Please provide or select a valid address.');
            }

            // ✅ Store selected address in session
            session(['selected_address_id' => $selectedAddressId]);

            return redirect()->route('order.index')->with('success', 'Address saved successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to save address: ' . $e->getMessage());
        }
    }

    public function show(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('customer/login')->with('error', 'Please log in to view addresses.');
        }

        $customerId = auth()->id();
        $addresses = Address::where('user_id', $customerId)->get();

        $selectedAddressId = session('selected_address_id', $addresses->first()->id ?? null);

        if ($request->has('address_id')) {
            $selectedAddressId = $request->input('address_id');
            session(['selected_address_id' => $selectedAddressId]);
        }

        $selectedAddress = Address::where('user_id', $customerId)
                                  ->where('id', $selectedAddressId)
                                  ->first();

        return view('address.show', compact('addresses', 'selectedAddress', 'selectedAddressId'));
    }
}
