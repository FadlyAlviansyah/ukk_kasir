<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();

        return view('pages.product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "name" => "required",
            "image" => "required|image|file",
            "price" => "required|integer|min:0",
            "stock" => "required|integer|min:0",
        ]);

        $validatedData["image"] = $request->file("image")->store('images');

        Product::create($validatedData);

        return redirect()->route('product.home')->with('added','added');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product, $id)
    {
        $product = Product::findOrFail($id);

        return view('pages.product.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, $id)
    {
        $validatedData = $request->validate([
            "name" => "required",
            "image" => "nullable|image|file",
            "price" => "required|integer|min:0",
        ]);

        if($request->image) {
            Storage::delete($request->oldImage);
            $validatedData["image"] = $request->file("image")->store('images');
        }

        Product::where('id', $id)->update($validatedData);

        return redirect()->route('product.home')->with('updated','updated');
    }

    public function updateStock(Request $request, $id)
    {
        $validatedData = $request->validate([
            "stock" => "required|integer|min:0"
        ]);

        Product::where('id', $id)->update([
            "stock" => $validatedData["stock"]
        ]);

        return redirect()->route('product.home')->with('updated', 'updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, $id)
    {
        $product = Product::findOrFail($id);

        if ($product->transaction()->count() > 0) {
            return redirect()->back()->with('error','error');
        }

        Storage::delete($product->image);
        $product->delete();

        return redirect()->route('product.home')->with('deleted','deleted');
    }

    public function exportExcel()
    {

        $products = Product::all();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['ID Produk', 'Nama Produk', 'Harga Produk', 'Stock Produk', 'Tanggal Ditambahkan'];
        $columnLetter = 'A';

        foreach ($headers as $header) {
            $sheet->setCellValue($columnLetter . 1, $header);
            $columnLetter++;
        }

        $rowNumber = 2;
        foreach($products as $product) {
            $sheet->setCellValue('A' . $rowNumber, $product->id);
            $sheet->setCellValue('B' . $rowNumber, $product->name);
            $sheet->setCellValue('C' . $rowNumber, 'Rp. ' . number_format($product->price,0,'.',''));
            $sheet->setCellValue('D' . $rowNumber, $product->stock);
            $sheet->setCellValue('E' . $rowNumber, $product->created_at->translatedFormat('d F Y'));
            $rowNumber++;
        }

        $filePath = 'exports/laporan_produk.xlsx';
        $storagePath = storage_path('app/' . $filePath);
        $directory = dirname($storagePath);

        if(!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($storagePath);
        
        return response()->download($storagePath)->deleteFileAfterSend(true);
    }
}
