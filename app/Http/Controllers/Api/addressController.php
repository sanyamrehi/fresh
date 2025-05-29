<?php

namespace App\Http\Controllers\Api;
use App\Models\Address;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class addressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function store(Request $request)
    {
        // ✅ Validate request data
        $request->validate([
            'selected_address_id' => 'nullable|exists:address,id',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|digits:6',
        ]);

        // ✅ Get authenticated user ID
        $customerId = $request->input('user_id');

        if (!$customerId) {
            return response()->json(['status' => false, 'message' => 'User not authenticated'], 401);
        }

        $selectedAddressId = null;
        $newAddress = null;

        try {
            // ✅ If a new address is provided, create it
            if ($request->filled(['address', 'city', 'state', 'pincode'])) {
                $newAddress = Address::create([
                    'user_id' => $customerId,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'pincode' => $request->pincode,
                ]);

                $selectedAddressId = $newAddress->id; // ✅ Use new address ID
            } else {
                $selectedAddressId = $request->selected_address_id; // ✅ Use selected address from request
            }

            if (!$selectedAddressId) {
                return response()->json(['status' => false, 'message' => 'No valid address provided'], 400);
            }

            // ✅ Store selected address in session (not needed for API but can be used for future reference)
            session(['selected_address_id' => $selectedAddressId]);

            return response()->json([
                'status' => true,
                'message' => 'Address saved successfully!',
                'address_id' => $selectedAddressId,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to save address: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch all addresses of the logged-in user.
     */
    public function index(Request $request)
    {
        $customerId = $request->input('user_id');
        // Get user_id from request or use authenticated user

        if (!$customerId) {
            return response()->json(['status' => false, 'message' => 'User not authenticated'], 401);
        }

        // ✅ Fetch addresses for the specified customer
        $addresses = Address::where('user_id', $customerId)->get();

        if ($addresses->isEmpty()) {
            return response()->json(['status' => false, 'message' => 'No addresses found for this customer'], 404);
        }

        return response()->json([
            'status' => true,
            'addresses' => $addresses
        ], 200);
    }

    /**
     * Show a single selected address.
     */
    public function show($id)
    {
        $customerId = auth()->id();

        if (!$customerId) {
            return response()->json(['status' => false, 'message' => 'User not authenticated'], 401);
        }

        $address = Address::where('user_id', $customerId)->where('id', $id)->first();

        if (!$address) {
            return response()->json(['status' => false, 'message' => 'Address not found'], 404);
        }

        return response()->json([
            'status' => true,
            'address' => $address
        ], 200);
    }

    /**
     * Delete an address by ID.
     */
    public function destroy($id)
    {
        $customerId = auth()->id();

        if (!$customerId) {
            return response()->json(['status' => false, 'message' => 'User not authenticated'], 401);
        }

        $address = Address::where('user_id', $customerId)->where('id', $id)->first();

        if (!$address) {
            return response()->json(['status' => false, 'message' => 'Address not found'], 404);
        }

        $address->delete();

        return response()->json([
            'status' => true,
            'message' => 'Address deleted successfully!',
        ], 200);
    }
}
