<?php

namespace App\Http\Controllers;

use App\Models\size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        // dd($request);
        $sizes = size::where('status', 'Active')
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('size', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('id', 'desc') // Order by creation date in descending order
            ->paginate(5); // Paginate the results with 5 records per page

        if ($request->ajax()) {
            return response()->json([
                'data' => $sizes->items(),
                'current_page' => $sizes->currentPage(),
                'per_page' => $sizes->perPage(),
                'total' => $sizes->total(),
                'links' => $sizes->links('pagination::bootstrap-5')->render(), // Bootstrap pagination links
            ]);
        }

        return view('size/index', compact('sizes'));//index page and blade file
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('size.create');//blade file and page of size
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $input = $request->all();
        // Validate the request
    $request->validate([
        'size' => 'required|string|unique:sizes,size',
    ]);
        if($request->status == 'Active'){
            $status = 'Active';
        }else{
            $status = 'InActive';
        }//display the record status

        size::create([
            'size' => $request->size,
            'status' => $status,
        ]);//new record is created in database, request arrow name and all field come from blade file form input name

        return redirect()->route('size.index')->with('success','product created successfully');//stored data is displayed in index page

    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $sizes = size::findOrFail($id);//searches the id or data where available or not

        return view('size.edit',compact('sizes'));//edit page for updation
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $sizes = size::findOrFail($id);//find or fail the sizes id

        $input = $request->all();
        // Validate the request
    $request->validate([
        'size' => 'required|string|unique:sizes,size,'. $id,
    ]);

        if($request->status == 'Active'){
            $status = 'Active';
        }else{
            $status = 'InActive';
        }//display the record status

        $sizes->update([
            'size' => $request->size,
            'status' => $status,
        ]);

        return redirect()->route('size.index')->with('success','product created successfully');//stored data is displayed in index page

    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete( $id)
    {
        size::findOrFail($id)->update(['status' => 'Deleted']);//finds the record and delete


        // Redirect with a success message
        return redirect()->route('size.index')->with('success', 'product deleted successfully.');

    }
}
