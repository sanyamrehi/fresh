<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\category;
use App\Models\product;
use App\Models\size;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use App\Exports\ProductsExport;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $search = $request->input('search', ''); // Default search is an empty string
        $user_id = Auth::id();
        $customerId = auth()->id();
        // dd($customerId);
        $addresses = Address::where('user_id',$customerId)->get();
        // dd($addresses);
        // Explode size and category values from request if available
        $sizes = explode(',', $request->size_size);
        $categories = explode(',', $request->category_category);


        // Fetch products and join related tables (sizes and categories)
//         $products = Product::select('products.*', 'sizes.size as size', 'categories.category as category')
//             ->leftJoin('sizes', 'products.size', '=', 'sizes.id') // Join with sizes table
//             ->leftJoin('categories', 'products.category', '=', 'categories.id') // Join with categories table
//             ->where('products.status', 'Active')
//             ->when($search, function ($query, $search) {
//                 $query->where(function ($subQuery) use ($search) {
//                     $subQuery->where('products.name', 'like', '%' . $search . '%')
//                              ->orWhere('categories.category', 'like', '%' . $search . '%')
//                              ->orWhere('sizes.size', 'like', '%' . $search . '%')
//                              ->orWhere('products.price', 'like', '%' . $search . '%');
//                 });
//             })
//             ->orderBy('products.id', 'desc')
//             ->paginate(5); // Paginate the results with 5 records per page
//             // dd($products);
//         // Join size and category values with commas before passing to the view
//         $products->transform(function ($product) {
//             // Ensure size and category are strings, joined by commas if they are arrays
//             $product->size = is_array($product->size) ? implode(', ', $product->size) : $product->size;
//             $product->category = is_array($product->category) ? implode(', ', $product->category) : $product->category;
//             return $product;
//         });
// dd($products);
$products = Product::select('products.*')
    ->selectRaw('(SELECT GROUP_CONCAT(address.user_id) FROM address WHERE FIND_IN_SET(address.id, products.size)) as addresses')
    ->selectRaw('(SELECT GROUP_CONCAT(sizes.size) FROM sizes WHERE FIND_IN_SET(sizes.id, products.size)) as sizes')
    ->selectRaw('(SELECT GROUP_CONCAT(categories.category) FROM categories WHERE FIND_IN_SET(categories.id, products.category)) as categories')
    ->where('products.status', 'Active')
    ->when($search, function ($query, $search) {
        $query->where(function ($subQuery) use ($search) {
            $subQuery->where('products.name', 'like', '%' . $search . '%')
                     ->orWhere('products.price', 'like', '%' . $search . '%')
                     ->orWhereRaw('(SELECT GROUP_CONCAT(categories.category) FROM categories WHERE FIND_IN_SET(categories.id, products.category)) LIKE ?', ["%{$search}%"])
                     ->orWhereRaw('(SELECT GROUP_CONCAT(sizes.size) FROM sizes WHERE FIND_IN_SET(sizes.id, products.size)) LIKE ?', ["%{$search}%"]);
        });
    })
    ->orderBy('products.id', 'desc')
    ->paginate(6);

// dd($products);

        // If the request is AJAX, return the products in JSON format
        if ($request->ajax()) {
            return response()->json([
                'data' => $products->items(),
                'current_page' => $products->currentPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
                'links' => $products->links('pagination::bootstrap-5')->render(),
            ]);
        }

        // Return the products to the view
        return view('index', compact('products', 'addresses', 'categories', 'sizes')); // Blade file with products table as variable
    }

    public function showadmin(Request $request)
    {
        $search = $request->input('search');

        // Fetch products with optional search
        $products = Product::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                         ->orWhere('category', 'like', "%{$search}%")
                         ->orWhere('size', 'like', "%{$search}%")
                         ->orWhere('price', 'like', "%{$search}%");
        })->paginate(5);

        return view('products/index', compact('products'));//redirects from CRUD index table
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $sizes = size::all();//All sizes available
        $categories =category::all();//All categories available
        return view('create',compact('sizes','categories'));//blade file for create page
    }

    /**
     * Store a newly created resource in storage.
     */
    public function show(Request $request, $id)
    {
        $customerId = auth()->id();

        // ✅ Retrieve the selected address ID from URL parameters
        $selectedAddressId = $request->query('address_id');

        // ✅ Get user addresses
        $addresses = Address::where('user_id', $customerId)->get();

        // ✅ Get product details
        $products = Product::select('products.*')
            ->selectRaw('(SELECT GROUP_CONCAT(sizes.size) FROM sizes WHERE FIND_IN_SET(sizes.id, products.size)) as sizes')
            ->selectRaw('(SELECT GROUP_CONCAT(categories.category) FROM categories WHERE FIND_IN_SET(categories.id, products.category)) as categories')
            ->where('products.id', $id)
            ->where('products.status', 'Active')
            ->first();

        // ✅ Debugging: Check if the selectedAddressId is available
        // dd($selectedAddressId);

        return view('show', compact('products', 'addresses', 'selectedAddressId'));
    }
    public function export()
    {
        $fileName = 'products.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, ['ID', 'Name', 'Color', 'Size', 'Category', 'Price', 'Status']);

            $products = Product::where('status', '!=', 'Deleted')->get();

            foreach ($products as $product) {
                fputcsv($file, [
                    $product->id,
                    $product->name,
                    $product->color,
                    $product->sizes,       // If multiple, you may want to implode
                    $product->categories,  // If multiple, you may want to implode
                    $product->price,
                    $product->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file, 'r');

        $header = true;

        while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
            if ($header) {
                $header = false; // skip header
                continue;
            }

            Product::updateOrCreate(
                ['id' => $row[0]], // Assuming first column is ID
                [
                    'name' => $row[1],
                    'color' => $row[2],
                    'sizes' => $row[3],
                    'categories' => $row[4],
                    'price' => $row[5],
                    'status' => $row[6],
                ]
            );
        }

        fclose($handle);

        return redirect()->back()->with('success', 'Products imported successfully!');
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'name' => 'required|string|unique:products,name',
        ]);

        $input = $request->all();
        // dd($input);
        // Check for uniqueness excluding the current record (if editing)
        $existingProduct = Product::where('name', $request->name)
            ->where('size', $request->size)
            ->when($request->id, function ($query) use ($request) {
                $query->where('id', '!=', $request->id);
            })
            ->first();

        if ($existingProduct) {
            return redirect()->back()->withErrors(['error' => 'A product with the same name and size already exists.']);
        }

        // Handle image upload if present
        if (isset($input['image']) && !empty($input['image'])) {
            if ($logoImage = $request->file('image')) {
                $destinationPath = public_path() . '/fresh_images/';
                $originalName = pathinfo($logoImage->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $logoImage->getClientOriginalExtension();
                $imagePath = $originalName . '.' . $extension;
                $logoImage->move($destinationPath, $imagePath);
            }
        }

        // Set status
        $status = $request->status == 'Active' ? 'Active' : 'InActive';
        $sizes = implode(',',$request->size_size);
        $categories = implode(',',$request->category_category);
        // Create or update the product
        Product::updateOrCreate(
            ['id' => $request->id], // Check by ID for updates
            [
                'name' => $request->name,
                'color' => $request->color,
                'size' => $sizes,
                'image' => $imagePath ?? null,
                'category' => $categories,
                'price' => $request->price,
                'status' => $status,
            ]
        );//store the data in the table where

        // Redirect with success message and shows the index page
        return redirect()->route('admin.showadmin')->with('success', 'Product saved successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Fetch the product
        $products = Product::findOrFail($id);

        // Convert comma-separated stored values into arrays
        $selectedSizes = explode(',', $products->size); // Assuming 'size' is stored as a comma-separated string
        $selectedCategories = explode(',', $products->category); // Assuming 'category' is stored as a comma-separated string

        // Fetch all available sizes and categories
        $sizes = Size::all();
        $categories = Category::all();

        // Pass data to the view
        return view('products.edit', compact('products', 'sizes', 'categories', 'selectedSizes', 'selectedCategories'));
    }


    /**
     * Update the specified resource in storage.
     */

    public function update(Request $request, $id)
    {

        $input = $request->all();
    // Fetch the product along with its related sizes and categories
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|unique:products,name,' . $id,
        ]);

        // dd($input);
        if (isset($input['image']) && !empty($input['image'])) {
            if ($logoImage = $request->file('image')) {
                $destinationPath = public_path() . '/fresh_images/';
                $originalName = pathinfo($logoImage->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $logoImage->getClientOriginalExtension();
                $imagePath = $originalName . '.' . $extension;

                $logoImage->move($destinationPath, $imagePath);
            }
        } else {
            $imagePath = $product->image; // Keep the existing image if no new image is provided
        }

        $status = $request->status == 'Active' ? 'Active' : 'InActive';
        $sizes = is_array($request->size_size) ? implode(',', $request->size_size) : ($request->size_size ?? '');
        $categories = is_array($request->category_category) ? implode(',', $request->category_category) : ($request->category_category ?? '');

        $product->update([
            'name' => $request->name,
            'color' => $request->color,
            'size' => $sizes,
            'image' => $imagePath,
            'category' => $categories,
            'price' => $request->price,
            'status' => $status,
        ]);
        //redirect the success message and display the index page
        return redirect()->route('admin.showadmin')->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete($id)
    {
        product::findOrFail($id)->update(['status' => 'Deleted']);//finds the record and delete


        // Redirect with a success message
        return redirect()->route('admin.showadmin')->with('success', 'product deleted successfully.');
    }
}
