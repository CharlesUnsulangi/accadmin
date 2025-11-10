<?php

namespace App\Http\Controllers;

use App\Services\ClosingService;
use App\Models\MonthlyClosing;
use App\Models\YearlyClosing;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClosingProcessController extends Controller
{
    protected $closingService;

    public function __construct(ClosingService $closingService)
    {
        $this->closingService = $closingService;
    }

    /**
     * Show closing process page
     */
    public function index()
    {
        return view('closing-process');
    }

    /**
     * Preview closing data (tidak disimpan)
     */
    public function preview(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:monthly,yearly,audit',
                'year' => 'required|integer|min:2000|max:2099',
                'month' => 'nullable|integer|min:1|max:12',
            ]);

            $type = $request->type;
            $year = (int) $request->year;
            $month = (int) ($request->month ?? 1);

            \Log::info('Preview request:', ['type' => $type, 'year' => $year, 'month' => $month]);

            $data = match($type) {
                'monthly' => $this->closingService->calculateMonthly($year, $month, false),
                'yearly' => $this->closingService->calculateYearly($year, false),
                'audit' => $this->closingService->calculateAudit(null, $year, $month),
            };

            return response()->json([
                'success' => true,
                'message' => 'Preview berhasil di-generate',
                'data' => $data,
                'summary' => [
                    'total_coa' => count($data),
                    'total_debet' => collect($data)->sum($type === 'audit' ? 'balance_debet' : 'closing_debet'),
                    'total_kredit' => collect($data)->sum($type === 'audit' ? 'balance_kredit' : 'closing_kredit'),
                    'total_balance' => collect($data)->sum($type === 'audit' ? 'balance' : 'closing_balance'),
                    'total_transaksi' => collect($data)->sum('jumlah_transaksi'),
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Preview error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Generate dan simpan closing
     */
    public function generate(Request $request)
    {
        try {
            $request->validate([
                'type' => 'required|in:monthly,yearly',
                'year' => 'required|integer|min:2000|max:2099',
                'month' => 'nullable|integer|min:1|max:12',
            ]);

            $type = $request->type;
            $year = (int) $request->year;
            $month = (int) ($request->month ?? 1);

            \Log::info('Generate request:', ['type' => $type, 'year' => $year, 'month' => $month]);

            // Check if already exists
            if ($type === 'monthly') {
                $existing = MonthlyClosing::where('closing_year', $year)
                    ->where('closing_month', $month)
                    ->where('version_status', 'ACTIVE')
                    ->exists();
                    
                if ($existing) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Closing untuk periode ini sudah ada. Gunakan fitur version control untuk update.'
                    ], 400);
                }

                $this->closingService->calculateMonthly($year, $month, true);
                $periode = Carbon::create($year, $month, 1)->format('F Y');
                $message = "Closing bulanan {$periode} berhasil di-generate!";

            } else {
                $existing = YearlyClosing::where('closing_year', $year)
                    ->where('version_status', 'ACTIVE')
                    ->exists();
                    
                if ($existing) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Closing tahunan untuk tahun ini sudah ada. Gunakan fitur version control untuk update.'
                    ], 400);
                }

                $this->closingService->calculateYearly($year, true);
                $message = "Closing tahunan {$year} berhasil di-generate!";
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Generate error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Lock closing
     */
    public function lock(Request $request)
    {
        $request->validate([
            'type' => 'required|in:monthly,yearly',
            'year' => 'required|integer',
            'month' => 'required_if:type,monthly|integer',
        ]);

        try {
            $this->closingService->lockClosing(
                (int) $request->year,
                (int) $request->month,
                $request->type
            );

            return response()->json([
                'success' => true,
                'message' => 'Closing berhasil di-lock. Data tidak bisa diubah lagi.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get existing closings
     */
    public function getExisting(Request $request)
    {
        $type = $request->type ?? 'monthly';
        $year = $request->year ?? date('Y');
        $month = $request->month ?? date('m');

        if ($type === 'monthly') {
            $closings = MonthlyClosing::where('closing_year', $year)
                ->where('closing_month', $month)
                ->orderBy('version_number', 'desc')
                ->limit(10)
                ->get()
                ->unique('version_number');
        } else {
            $closings = YearlyClosing::where('closing_year', $year)
                ->orderBy('version_number', 'desc')
                ->limit(10)
                ->get()
                ->unique('version_number');
        }

        return response()->json([
            'success' => true,
            'data' => $closings
        ]);
    }

    /**
     * Compare with audit
     */
    public function compareAudit(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer|min:1|max:12',
        ]);

        try {
            $discrepancies = $this->closingService->compareWithAudit(
                (int) $request->year,
                (int) $request->month
            );

            return response()->json([
                'success' => true,
                'has_discrepancies' => count($discrepancies) > 0,
                'data' => $discrepancies
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show closing history page
     */
    public function history()
    {
        return view('closing-history');
    }

    /**
     * Get closing history data
     */
    public function historyData(Request $request)
    {
        try {
            $type = $request->get('type', 'monthly');
            $year = $request->get('year');
            $month = $request->get('month');
            $status = $request->get('status');

            if ($type === 'monthly') {
                $query = MonthlyClosing::query();
                
                if ($year) {
                    $query->where('closing_year', $year);
                }
                if ($month) {
                    $query->where('closing_month', $month);
                }
                if ($status) {
                    $query->where('version_status', $status);
                }

                $data = $query->orderBy('closing_year', 'desc')
                    ->orderBy('closing_month', 'desc')
                    ->orderBy('coa_code')
                    ->get();

            } else {
                $query = YearlyClosing::query();
                
                if ($year) {
                    $query->where('closing_year', $year);
                }
                if ($status) {
                    $query->where('version_status', $status);
                }

                $data = $query->orderBy('closing_year', 'desc')
                    ->orderBy('coa_code')
                    ->get();
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export closing history to Excel
     */
    public function historyExport(Request $request)
    {
        // TODO: Implement Excel export
        return response()->json([
            'success' => false,
            'message' => 'Export Excel belum diimplementasikan'
        ]);
    }
}
