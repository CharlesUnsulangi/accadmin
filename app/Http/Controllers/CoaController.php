<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\CoaMain;
use App\Models\CoaSub1;
use App\Models\CoaSub2;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    /**
     * Display COA management page (pure JS version)
     */
    public function index()
    {
        return view('coa.modern');
    }

    /**
     * Display COA management page (Alpine.js version)
     */
    public function alpine()
    {
        return view('coa.alpine');
    }

    /**
     * Display COA management page (Bootstrap version)
     */
    public function bootstrap()
    {
        return view('coa.bootstrap');
    }

    /**
     * Display COA management page (jQuery AJAX version)
     */
    public function jquery()
    {
        return view('coa.jquery');
    }

    /**
     * API: Get all COA data
     */
    public function getData(Request $request)
    {
        $query = Coa::where('rec_status', '1');

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('coa_code', 'like', '%' . $request->search . '%')
                  ->orWhere('coa_desc', 'like', '%' . $request->search . '%')
                  ->orWhere('desc_h1', 'like', '%' . $request->search . '%')
                  ->orWhere('desc_h2', 'like', '%' . $request->search . '%');
            });
        }

        // Filter
        if ($request->filter_h1) {
            $query->where('ms_coa_h1_id', $request->filter_h1);
        }

        // Sort
        $sortBy = $request->sort_by ?? 'coa_code';
        $sortDir = $request->sort_dir ?? 'asc';
        $query->orderBy($sortBy, $sortDir);

        // Paginate
        $perPage = $request->per_page ?? 25;
        $data = $query->paginate($perPage);

        return response()->json($data);
    }

    /**
     * API: Get filters data
     */
    public function getFilters()
    {
        return response()->json([
            'mains' => CoaMain::where('rec_status', '1')
                ->orderBy('coa_main_code')
                ->get(['coa_main_code', 'coa_main_desc']),
            'sub1s' => CoaSub1::where('rec_status', '1')
                ->orderBy('coasub1_code')
                ->get(['coasub1_code', 'coasub1_desc']),
            'sub2s' => CoaSub2::where('rec_status', '1')
                ->orderBy('coasub2_code')
                ->get(['coasub2_code', 'coasub2_desc']),
        ]);
    }

    /**
     * API: Store new COA
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'coa_code' => 'required|string|max:50|unique:ms_acc_coa,coa_code',
            'coa_desc' => 'required|string|max:255',
            'desc_h1' => 'required|string|max:255',
            'ms_coa_h1_id' => 'nullable|string|max:50',
            'desc_h2' => 'nullable|string|max:255',
            'ms_coa_h2_id' => 'nullable|string|max:50',
            'desc_h3' => 'nullable|string|max:255',
            'desc_h4' => 'nullable|string|max:255',
            'desc_h5' => 'nullable|string|max:255',
            'desc_h6' => 'nullable|string|max:255',
        ]);

        $coa = Coa::create(array_merge($validated, [
            'rec_status' => '1',
            'rec_usercreated' => auth()->user()->name ?? 'system',
            'rec_datecreated' => now(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'COA created successfully',
            'data' => $coa
        ]);
    }

    /**
     * API: Update COA
     */
    public function update(Request $request, $code)
    {
        $coa = Coa::where('coa_code', $code)->firstOrFail();

        $validated = $request->validate([
            'coa_desc' => 'required|string|max:255',
            'desc_h1' => 'required|string|max:255',
        ]);

        $coa->update(array_merge($validated, [
            'rec_userupdate' => auth()->user()->name ?? 'system',
            'rec_dateupdate' => now(),
        ]));

        return response()->json([
            'success' => true,
            'message' => 'COA updated successfully',
            'data' => $coa
        ]);
    }

    /**
     * API: Delete COA (soft delete)
     */
    public function destroy($code)
    {
        $coa = Coa::where('coa_code', $code)->firstOrFail();
        
        $coa->update([
            'rec_status' => '0',
            'rec_userupdate' => auth()->user()->name ?? 'system',
            'rec_dateupdate' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'COA deleted successfully'
        ]);
    }

    /**
     * API: Get full hierarchy report
     */
    public function hierarchy(Request $request)
    {
        $query = \DB::table('ms_acc_coa_main as m')
            ->leftJoin('ms_acc_coasub1 as s1', 's1.coasub1_maincode', '=', 'm.coa_main_code')
            ->leftJoin('ms_acc_coasub2 as s2', 's2.coasub2_coasub1code', '=', 's1.coasub1_code')
            ->leftJoin('ms_acc_coa as d', 'd.coa_coasub2code', '=', 's2.coasub2_code')
            ->select(
                'm.coa_main_code', 'm.coa_main_desc',
                's1.coasub1_code', 's1.coasub1_desc',
                's2.coasub2_code', 's2.coasub2_desc',
                'd.coa_code', 'd.coa_desc', 'd.coa_note', 'd.arus_kas_code',
                'd.desc_h1', 'd.desc_h2', 'd.desc_h3', 'd.desc_h4', 'd.desc_h5', 'd.desc_h6'
            )
            ->where('m.rec_status', '1')
            ->whereNotNull('d.coa_code')
            ->where('d.rec_status', '1');

        // Search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('m.coa_main_desc', 'like', '%' . $request->search . '%')
                  ->orWhere('s1.coasub1_desc', 'like', '%' . $request->search . '%')
                  ->orWhere('s2.coasub2_desc', 'like', '%' . $request->search . '%')
                  ->orWhere('d.coa_desc', 'like', '%' . $request->search . '%');
            });
        }

        // Filters
        if ($request->filter_main) {
            $query->where('m.coa_main_code', $request->filter_main);
        }
        if ($request->filter_sub1) {
            $query->where('s1.coasub1_code', $request->filter_sub1);
        }

        $query->orderBy('m.coa_main_code')
              ->orderBy('s1.coasub1_code')
              ->orderBy('s2.coasub2_code')
              ->orderBy('d.coa_code');

        $perPage = $request->per_page ?? 25;
        $data = $query->paginate($perPage);

        return response()->json($data);
    }
}
