<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductsImport implements ToModel
{
    public function model(array $row)
    {
        return new Product([
            'name' => $row[0],
            'color' => $row[1],
            'size' => $row[2], // Ensure this matches your storage format
            'image' => $row[3],
            'category' => $row[4], // comma-separated IDs if applicable
            'price' => $row[5],
            'status' => $row[6],
        ]);
    }
}
