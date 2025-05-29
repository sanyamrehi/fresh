<?php

namespace App\Http\Controllers;

use App\Models\category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $search = $request->input('search', '');
        // dd($request);
        $categories = category::where('status', 'Active')
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('category', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'desc') // Order by creation date in descending order
            ->paginate(5); // Paginate the results with 5 records per page

        if ($request->ajax()) {
            return response()->json([
                'data' => $categories->items(),
                'current_page' => $categories->currentPage(),
                'per_page' => $categories->perPage(),
                'total' => $categories->total(),
                'links' => $categories->links('pagination::bootstrap-5')->render(), // Bootstrap pagination links
            ]);
        }

        return view('category.index', compact('categories'));//index page and blade file
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('category.create');//blade file and page
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

            $input = $request->all();

          // Validate the request
    $request->validate([
        'category' => 'required|string|unique:categories,category',
    ]);
            // dd($input);
            if($request->status == 'Active'){
                $status = 'Active';
            }else{
                $status = 'InActive';
            }//display the record status

            category::create([
                'category' => $request->category,
                'status' => $status,
            ]);//new record is created in database, request arrow name and all field come from blade file form input name

            return redirect()->route('category.index')->with('success','product created successfully');//stored data is displayed in index page

    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit( $id)
    {
        $categories = category::findOrFail($id);//searches the id or data where available or not

        return view('category.edit',compact('categories'));//edit page for updation
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        $categories = category::findOrFail($id);

        $input = $request->all();
           // Validate the request
    $request->validate([
        'category' => 'required|string|unique:categories,category,'. $id,
    ]);
        if($request->status == 'Active'){
            $status = 'Active';
        }else{
            $status = 'InActive';
        }//display the record status

        $categories->update([
            'category' => $request->category,
            'status' => $status,
        ]);//new record is updated in database, request arrow name and all field come from blade file form input name

        return redirect()->route('category.index')->with('success','product created successfully');//stored data is displayed in index page

    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete( $id)
    {
        category::findOrFail($id)->update(['status' => 'Deleted']);//finds the record and delete


        // Redirect with a success message
        return redirect()->route('category.index')->with('success', 'product deleted successfully.');
    }
}
