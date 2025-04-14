<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();

        return view('pages.user.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.user.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "name" => "required",
            "email" => "required|email",
            "role" => "required|in:admin,cashier",
            "password" => "required"
        ]);

        $validatedData["password"] = Hash::make($validatedData["password"]);

        User::create($validatedData);

        return redirect()->route('user.home')->with('added','added');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user, $id)
    {
        $user = User::findOrFail($id);

        return view('pages.user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user, $id)
    {
        $validatedData = $request->validate([
            "name" => "required",
            "email" => "required|email",
            "role"=> "required|in:admin,cashier",
        ]);

        if($request->password) {
            $validatedData["password"] = Hash::make($request->password);
        }

        User::where('id', $id)->update($validatedData);

        return redirect()->route('user.home')->with('updated','updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, $id)
    {
        $user = User::findOrFail($id);

        if ($user->transaction()->count() > 0) {
            return redirect()->back()->with('error','error');
        }
        
        $user->delete();

        return redirect()->route('user.home')->with('deleted','deleted');
    }

    public function exportExcel()
    {

        $users = User::all();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['ID User', 'Nama User', 'Email User', 'Tanggal Bergabung'];
        $columnLetter = 'A';

        foreach ($headers as $header) {
            $sheet->setCellValue($columnLetter . 1, $header);
            $columnLetter++;
        }

        $rowNumber = 2;
        foreach($users as $user) {
            $sheet->setCellValue('A' . $rowNumber, $user->id);
            $sheet->setCellValue('B' . $rowNumber, $user->name);
            $sheet->setCellValue('C' . $rowNumber, $user->email);
            $sheet->setCellValue('D' . $rowNumber, $user->created_at->translatedFormat('d F Y'));
            $rowNumber++;
        }

        $filePath = 'exports/laporan_user.xlsx';
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
