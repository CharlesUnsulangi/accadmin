<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Coa;
use App\Models\Bank;
use App\Models\Area;
use App\Models\Vendor;

class MasterDataController extends Controller
{
    /**
     * Display master data dashboard
     */
    public function dashboard()
    {
        return view('master.dashboard');
    }

    /**
     * Get statistics for all master data
     */
    public function getStats()
    {
        try {
            $stats = [
                'coa' => [
                    'total' => Coa::count(),
                    'active' => Coa::where('rec_status', '1')->count(),
                ],
                'bank' => [
                    'total' => Bank::count(),
                    'active' => Bank::where('rec_status', '1')->count(),
                ],
                'area' => [
                    'total' => Area::count(),
                    'active' => Area::where('rec_status', '1')->count(),
                ],
                'vendor' => [
                    'total' => Vendor::count(),
                    'active' => Vendor::where('rec_status', '1')->count(),
                ],
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get bank data with filters
     */
    public function getBankData(Request $request)
    {
        try {
            $query = Bank::query();

            // Search
            if ($request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('bank_code', 'like', '%' . $request->search . '%')
                      ->orWhere('bank_desc', 'like', '%' . $request->search . '%')
                      ->orWhere('bank_norek', 'like', '%' . $request->search . '%');
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status !== '') {
                $query->where('rec_status', $request->status);
            }

            // Sorting
            $sortBy = $request->get('sortBy', 'bank_code');
            $sortDirection = $request->get('sortDirection', 'asc');
            $query->orderBy($sortBy, $sortDirection);

            // Pagination
            $perPage = $request->get('perPage', 25);
            $data = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $data->items(),
                'meta' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                ]
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading bank data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new bank
     */
    public function storeBank(Request $request)
    {
        try {
            $validated = $request->validate([
                'bank_code' => 'required|unique:ms_acc_bank,bank_code',
                'bank_desc' => 'required',
                'bank_norek' => 'nullable',
                'bank_coa' => 'nullable',
            ]);

            $bank = Bank::create([
                'bank_code' => $validated['bank_code'],
                'bank_desc' => $validated['bank_desc'],
                'bank_norek' => $validated['bank_norek'] ?? '',
                'bank_coa' => $validated['bank_coa'] ?? '',
                'rec_status' => '1',
                'rec_usercreated' => auth()->user()->name ?? 'system',
                'rec_datecreated' => now(),
                'rec_userupdate' => auth()->user()->name ?? 'system',
                'rec_dateupdate' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bank created successfully',
                'data' => $bank
            ], 201, [], JSON_UNESCAPED_UNICODE);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating bank: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update bank
     */
    public function updateBank(Request $request, $code)
    {
        try {
            $bank = Bank::where('bank_code', $code)->firstOrFail();

            $validated = $request->validate([
                'bank_desc' => 'required',
                'bank_norek' => 'nullable',
                'bank_coa' => 'nullable',
                'rec_status' => 'required|in:0,1',
            ]);

            $bank->update([
                'bank_desc' => $validated['bank_desc'],
                'bank_norek' => $validated['bank_norek'] ?? '',
                'bank_coa' => $validated['bank_coa'] ?? '',
                'rec_status' => $validated['rec_status'],
                'rec_userupdate' => auth()->user()->name ?? 'system',
                'rec_dateupdate' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bank updated successfully',
                'data' => $bank
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating bank: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete bank (soft delete)
     */
    public function deleteBank($code)
    {
        try {
            $bank = Bank::where('bank_code', $code)->firstOrFail();
            
            $bank->update([
                'rec_status' => '0',
                'rec_userupdate' => auth()->user()->name ?? 'system',
                'rec_dateupdate' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Bank deleted successfully'
            ], 200, [], JSON_UNESCAPED_UNICODE);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting bank: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export bank to Excel
     */
    public function exportBank()
    {
        try {
            $banks = Bank::orderBy('bank_code')->get();
            
            // Simple CSV export
            $filename = 'banks_' . date('Y-m-d_His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($banks) {
                $file = fopen('php://output', 'w');
                
                // Header
                fputcsv($file, ['Code', 'Description', 'Account Number', 'COA', 'Status', 'Created By', 'Created At']);
                
                // Data
                foreach ($banks as $bank) {
                    fputcsv($file, [
                        $bank->bank_code,
                        $bank->bank_desc,
                        $bank->bank_norek,
                        $bank->bank_coa,
                        $bank->rec_status == '1' ? 'Active' : 'Inactive',
                        $bank->rec_usercreated,
                        $bank->rec_datecreated,
                    ]);
                }
                
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting bank: ' . $e->getMessage()
            ], 500);
        }
    }
}
