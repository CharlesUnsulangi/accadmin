<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChequeController extends Controller
{
    /**
     * Display create cheque book form
     */
    public function create()
    {
        // Get active COA list for dropdown
        $coaList = DB::table('ms_acc_coa')
            ->where('rec_status', 'A')
            ->select('coa_code', 'coa_desc')
            ->orderBy('coa_code')
            ->get()
            ->map(function($coa) {
                return [
                    'code' => $coa->coa_code,
                    'desc' => $coa->coa_desc,
                    'label' => $coa->coa_code . ' - ' . $coa->coa_desc
                ];
            });

        // Get bank list for dropdown
        $bankList = DB::table('ms_bank')
            ->where('rec_status', '1')
            ->select('Bank_Code')
            ->orderBy('Bank_Code')
            ->get()
            ->map(function($bank) {
                return [
                    'code' => $bank->Bank_Code,
                    'name' => $bank->Bank_Code
                ];
            });

        return view('cheque.create', compact('coaList', 'bankList'));
    }

    /**
     * Store new cheque book (API)
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'cheque_code_h' => 'required|string|max:50|unique:ms_acc_cheque_h,cheque_code_h',
                'cheque_desc' => 'nullable|string|max:250',
                'cheque_bank' => 'required|string|max:100',
                'cheque_rek' => 'required|string|max:100',
                'cheque_cabang' => 'nullable|string|max:100',
                'cheque_coacode' => 'nullable|string|max:50',
                'cheque_type' => 'nullable|string|max:50',
                'cheque_startno' => 'required|integer|min:1',
                'cheque_endno' => 'required|integer|min:1',
            ]);

            // Validate that end number >= start number
            if ($validated['cheque_endno'] < $validated['cheque_startno']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor akhir harus lebih besar atau sama dengan nomor awal'
                ], 422);
            }

            $username = Auth::user()->name ?? 'System';

            // Create cheque book header
            DB::table('ms_acc_cheque_h')->insert([
                'cheque_code_h' => $validated['cheque_code_h'],
                'cheque_desc' => $validated['cheque_desc'],
                'cheque_bank' => $validated['cheque_bank'],
                'cheque_rek' => $validated['cheque_rek'],
                'cheque_cabang' => $validated['cheque_cabang'],
                'cheque_coacode' => $validated['cheque_coacode'],
                'cheque_type' => $validated['cheque_type'],
                'cheque_startno' => $validated['cheque_startno'],
                'cheque_endno' => $validated['cheque_endno'],
                'rec_usercreated' => $username,
                'rec_userupdate' => $username,
                'rec_datecreated' => now(),
                'rec_dateupdate' => now(),
                'rec_status' => '1',
            ]);

            // Generate cheque details
            $chequeDetails = [];
            for ($i = $validated['cheque_startno']; $i <= $validated['cheque_endno']; $i++) {
                $chequeDetails[] = [
                    'cheque_code_d' => $validated['cheque_code_h'] . '-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                    'cheque_code_h' => $validated['cheque_code_h'],
                    'cheque_no' => $i,
                    'cheque_status' => 'AVAILABLE',
                    'cheque_value' => 0,
                    'rec_usercreated' => $username,
                    'rec_userupdate' => $username,
                    'rec_datecreated' => now(),
                    'rec_dateupdate' => now(),
                    'rec_status' => '1',
                ];
            }

            // Batch insert cheque details
            DB::table('ms_acc_cheque_d')->insert($chequeDetails);

            $totalCheques = count($chequeDetails);

            return response()->json([
                'success' => true,
                'message' => "Buku cheque berhasil dibuat. Total {$totalCheques} lembar cek telah di-generate.",
                'data' => [
                    'cheque_code_h' => $validated['cheque_code_h'],
                    'total_cheques' => $totalCheques
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
