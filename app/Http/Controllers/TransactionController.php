<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with('user', 'member', 'transactionDetail.product')->orderBy("created_at","desc")->get();

        return view("pages.transaction.index", compact("transactions"));
    }

    public function create()
    {
        $products = Product::orderBy("stock", "desc")->get();

        return view('pages.transaction.create', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'phone_number' => 'nullable|max:15',
            'total' => 'required|integer',
            'amount_paid' => 'required|integer',
            'items' => 'required', 
            'items.*.product_id' => 'nullable|exists:products,id', 
            'items.*.quantity' => 'required|integer|min:1', 
            'items.*.subtotal' => 'required|integer',
        ]);

        $amountPaid = preg_replace('/[^0-9]/', '', $request->amount_paid);
        $amountPaid = (int)$amountPaid;
        $amountChange = $amountPaid - $validatedData['total'];
        $items = is_string($request->items) ? json_decode($request->items, true) : $request->items;

        if ($validatedData['phone_number']) {
            return redirect()->route('transaction.createMember')->with([
                'phone_number' => $validatedData['phone_number'],
                'items' => $items,
                'total' => $validatedData['total'],
                'amount_paid' => $amountPaid,
                'amount_change' => $amountChange,
            ]);
        }

        $transaction = Transaction::create([
            'user_id' => $validatedData['user_id'],
            'total' => $validatedData['total'],
            'amount_paid' => $amountPaid,
            'amount_change' => $amountChange,
        ]);

        foreach ($items as $item) {
            $product = Product::find($item['id']);

            if ($product) {
                $product->stock -= $item['quantity'];
                $product->save();
            }

            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity']
            ]);
        };

        $data = compact('transaction', 'items');

        return redirect()->route('transaction.detailPrint', $transaction->id)->with('data', $data);
    }

    public function createMember(Request $request)
    {
        $phoneNumber = $request->session()->get('phone_number', null);
        $items = $request->session()->get('items', []);
        $total = $request->session()->get('total', 0);
        $amountPaid = $request->session()->get('amount_paid', 0);
        $amountChange = $request->session()->get('amount_change', 0);
        $pointsEarned = $total / 100;
        $memberName = '';
        $isNewMember = true;
        $points = $pointsEarned;

        $member = Member::where('phone_number', $phoneNumber)->first();
        if ($member) {
            $points = $member['points'] + $pointsEarned;
            $memberName = $member->name;
            $isNewMember = false;
        }

        $request->session()->forget(['items', 'total', 'amount_paid', 'amount_change']);
    
        return view('pages.transaction.create-member', compact('items', 'total', 'amountPaid', 'amountChange', 'pointsEarned', 'points', 'phoneNumber', 'memberName', 'isNewMember'));
    }

    public function storeMember(Request $request) 
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'phone_number' => 'required',
            'name' => 'required',
            'points_earned' => 'required|integer',
            'points_used' => 'nullable|integer|min:0',
            'items' => 'required',
            'total' => 'required|integer',
            'amount_paid' => 'required|integer',
            'amount_change' => 'required|integer',
        ]);

        $member = Member::where('phone_number', $validatedData['phone_number'])->first();
        if (!$member) {
            $member = Member::create([
                'name' => $validatedData['name'],
                'phone_number' => $validatedData['phone_number'],
                'points' => 0,
            ]);
        }

        $transaction = Transaction::create([
            'user_id' => $validatedData['user_id'],
            'member_id' => $member ? $member->id : null,
            'points_used' => $validatedData['points_used'] ?? 0,
            'points_earned' => $validatedData['points_earned'] ?? 0,
            'total' => $validatedData['total'],
            'amount_paid' => $validatedData['amount_paid'],
            'amount_change' => $validatedData['amount_change'],
        ]);

        if ($validatedData['points_used']) {
            $member->points = 0;
        } else {
            $member->points += $validatedData['points_earned'];
        }

        $member->save();
        
        $items = json_decode($validatedData['items'], true);

        foreach ($items as $item) {
            $product = Product::find($item['id']);
            if ($product) {
                $product->stock -= $item['quantity'];
                $product->save();
            }
            
            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'subtotal' => $item['price'] * $item['quantity']
            ]);
        }

        $data = compact('transaction', 'items', 'member');

        return redirect()->route('transaction.detailPrint', $transaction->id)->with('data', $data);
    }

    public function detailPrint($id)
    {
        $transaction = Transaction::with('user', 'member', 'transactionDetail.product')->findOrFail($id);

        return view('pages.transaction.detail-print', compact('transaction'));
    }

    public function print($id)
    {
        $transaction = Transaction::with('user','member', 'transactionDetail.product')->findOrFail($id);

        $pdf = Pdf::loadView('pages.transaction.print', compact('transaction'));

        return $pdf->download('Bukti_Transaksi_' . $transaction->id . '.pdf');
    }      

    public function exportExcel()
    {

        $transactions = Transaction::with('user','member','transactionDetail.product')->orderBy('created_at', 'desc')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['ID Transaksi', 'Status Pelanggan', 'Nama Pelanggan', 'No. HP Pelanggan','Poin Pelanggan', 'Produk', 'Total Harga', 'Total Diskon', 'Total Bayar', 'Total Kembalian', 'Tanggal Transaksi'];
        $columnLetter = 'A';

        foreach ($headers as $header) {
            $sheet->setCellValue($columnLetter . 1, $header);
            $columnLetter++;
        }

        $rowNumber = 2;
        foreach($transactions as $transaction) {
            $sheet->setCellValue('A' . $rowNumber, $transaction->id);
            $sheet->setCellValue('B' . $rowNumber, $transaction->member ? 'Member' : 'Non Member');
            $sheet->setCellValue('C' . $rowNumber, $transaction->member ? $transaction->member->name : '-');
            $sheet->setCellValue('D' . $rowNumber, $transaction->member ? $transaction->member->phone_number : '-');
            $sheet->setCellValue('E' . $rowNumber, $transaction->member ? $transaction->member->points : '-');

            $items = [];
            foreach($transaction->transactionDetail as $detail) {
                $productName = $detail->product->name;
                $quantity = $detail->quantity;
                $subtotal = 'Rp. ' . number_format($detail->subtotal, 0, ',', '.');
                $items[] = "($productName) {($quantity) : ($subtotal)}";
            }
            $sheet->setCellValue('G' . $rowNumber, implode(',', $items));

            $sheet->setCellValue('H' . $rowNumber, number_format($transaction->total, 0, ',', '.'));
            $sheet->setCellValue('I' . $rowNumber, number_format($transaction->points_used, 0, ',', '.'));
            $sheet->setCellValue('J' . $rowNumber, number_format($transaction->amount_paid, 0, ',', '.'));
            $sheet->setCellValue('K' . $rowNumber, number_format($transaction->amount_change, 0, ',', '.'));
            $sheet->setCellValue('L' . $rowNumber, $transaction->created_at->translatedFormat('d F Y H:i'));
            $rowNumber++;
        }

        $filePath = 'exports/laporan_penjualan.xlsx';
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
