<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    /**
     * Display application management page
     */
    public function index()
    {
        return view('applications.index');
    }

    /**
     * Display application detail page
     */
    public function show($id)
    {
        try {
            // Get application
            $application = DB::table('ms_admin_it_aplikasi')
                ->whereRaw("CAST(ms_admin_it_aplikasi_id AS VARCHAR(MAX)) = ?", [$id])
                ->first();

            if (!$application) {
                abort(404, 'Application not found');
            }

            // Get topics
            $topics = ApplicationTopic::getByApplication($id);

            return view('applications.show', compact('application', 'topics'));

        } catch (\Exception $e) {
            abort(500, 'Error loading application: ' . $e->getMessage());
        }
    }

    /**
     * Get all applications data
     */
    public function getData(Request $request)
    {
        try {
            $query = DB::table('ms_admin_it_aplikasi')
                ->select(
                    'ms_admin_it_aplikasi_id',
                    'apps_desc',
                    'id',
                    'user_created',
                    'time_created',
                    'cek_non_aktif',
                    'aplikasi_note'
                );

            // Search
            if ($request->has('search') && $request->search != '') {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->whereRaw("CAST(apps_desc AS VARCHAR(MAX)) LIKE ?", ["%{$search}%"])
                      ->orWhereRaw("CAST(aplikasi_note AS VARCHAR(MAX)) LIKE ?", ["%{$search}%"]);
                });
            }

            // Filter by status
            if ($request->has('status') && $request->status != '') {
                if ($request->status === 'active') {
                    $query->where(function($q) {
                        $q->where('cek_non_aktif', 0)
                          ->orWhereNull('cek_non_aktif');
                    });
                } else {
                    $query->where('cek_non_aktif', 1);
                }
            }

            // Sort
            $sortBy = $request->get('sort_by', 'apps_desc');
            $sortDir = $request->get('sort_dir', 'asc');
            $query->orderBy($sortBy, $sortDir);

            $applications = $query->get();

            return response()->json([
                'success' => true,
                'data' => $applications,
                'total' => $applications->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading applications: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new application
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'apps_desc' => 'required|string|max:50',
                'framework' => 'nullable|string|max:50',
                'aplikasi_note' => 'nullable|string'
            ]);

            // Check if name already exists
            $exists = DB::table('ms_admin_it_aplikasi')
                ->whereRaw("CAST(apps_desc AS VARCHAR(MAX)) = ?", [$request->apps_desc])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application name already exists'
                ], 400);
            }

            // Generate ID
            $appId = Application::generateId();

            // Insert (id is auto-increment)
            DB::table('ms_admin_it_aplikasi')->insert([
                'ms_admin_it_aplikasi_id' => $appId,
                'apps_desc' => $request->apps_desc,
                'framework' => $request->framework,
                'user_created' => Auth::user()->name ?? 'SYSTEM',
                'time_created' => now()->format('H:i:s'),
                'cek_non_aktif' => 0,
                'aplikasi_note' => $request->aplikasi_note
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application created successfully',
                'data' => [
                    'ms_admin_it_aplikasi_id' => $appId,
                    'apps_desc' => $request->apps_desc
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update application
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'apps_desc' => 'required|string|max:50',
                'framework' => 'nullable|string|max:50',
                'aplikasi_note' => 'nullable|string'
            ]);

            // Check if exists
            $exists = DB::table('ms_admin_it_aplikasi')
                ->whereRaw("CAST(ms_admin_it_aplikasi_id AS VARCHAR(MAX)) = ?", [$id])
                ->exists();

            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            // Check if name is unique (excluding current record)
            $nameExists = DB::table('ms_admin_it_aplikasi')
                ->whereRaw("CAST(apps_desc AS VARCHAR(MAX)) = ?", [$request->apps_desc])
                ->whereRaw("CAST(ms_admin_it_aplikasi_id AS VARCHAR(MAX)) != ?", [$id])
                ->exists();

            if ($nameExists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application name already exists'
                ], 400);
            }

            // Update
            DB::table('ms_admin_it_aplikasi')
                ->whereRaw("CAST(ms_admin_it_aplikasi_id AS VARCHAR(MAX)) = ?", [$id])
                ->update([
                    'apps_desc' => $request->apps_desc,
                    'framework' => $request->framework,
                    'aplikasi_note' => $request->aplikasi_note
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Application updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating application: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle application status (Active/Inactive)
     */
    public function toggleStatus(Request $request, $id)
    {
        try {
            $app = DB::table('ms_admin_it_aplikasi')
                ->whereRaw("CAST(ms_admin_it_aplikasi_id AS VARCHAR(MAX)) = ?", [$id])
                ->first();

            if (!$app) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            $newStatus = ($app->cek_non_aktif ?? 0) ? 0 : 1;

            DB::table('ms_admin_it_aplikasi')
                ->whereRaw("CAST(ms_admin_it_aplikasi_id AS VARCHAR(MAX)) = ?", [$id])
                ->update([
                    'cek_non_aktif' => $newStatus
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Application status updated',
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get application statistics
     */
    public function getStats()
    {
        try {
            $total = DB::table('ms_admin_it_aplikasi')->count();
            $active = DB::table('ms_admin_it_aplikasi')
                ->where(function($q) {
                    $q->where('cek_non_aktif', 0)
                      ->orWhereNull('cek_non_aktif');
                })
                ->count();
            $inactive = DB::table('ms_admin_it_aplikasi')
                ->where('cek_non_aktif', 1)
                ->count();

            $stats = [
                'total' => $total,
                'active' => $active,
                'inactive' => $inactive
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
     * Get topics for specific application
     */
    public function getTopics($id)
    {
        try {
            $topics = ApplicationTopic::getByApplication($id);

            return response()->json([
                'success' => true,
                'data' => $topics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading topics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new topic for application
     */
    public function storeTopic(Request $request, $id)
    {
        try {
            $request->validate([
                'topic_desc' => 'required|string|max:50',
                'value_priority' => 'nullable|integer'
            ]);

            // Check if application exists
            $exists = DB::table('ms_admin_it_aplikasi')
                ->whereRaw("CAST(ms_admin_it_aplikasi_id AS VARCHAR(MAX)) = ?", [$id])
                ->exists();

            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Application not found'
                ], 404);
            }

            // Insert topic (ms_admin_it_topic is IDENTITY, auto-increment)
            DB::table('ms_admin_it_aplikasi_topic')->insert([
                'topic_desc' => $request->topic_desc,
                'value_priority' => $request->value_priority ?? 0,
                'ms_admin_it_aplikasi_id' => $id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Topic created successfully',
                'data' => [
                    'topic_desc' => $request->topic_desc
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating topic: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update topic
     */
    public function updateTopic(Request $request, $id, $topicId)
    {
        try {
            $request->validate([
                'topic_desc' => 'required|string|max:50',
                'value_priority' => 'nullable|integer'
            ]);

            // Check if topic exists
            $exists = DB::table('ms_admin_it_aplikasi_topic')
                ->where('ms_admin_it_topic', $topicId)
                ->whereRaw("CAST(ms_admin_it_aplikasi_id AS VARCHAR(MAX)) = ?", [$id])
                ->exists();

            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Topic not found'
                ], 404);
            }

            // Update topic
            DB::table('ms_admin_it_aplikasi_topic')
                ->where('ms_admin_it_topic', $topicId)
                ->whereRaw("CAST(ms_admin_it_aplikasi_id AS VARCHAR(MAX)) = ?", [$id])
                ->update([
                    'topic_desc' => $request->topic_desc,
                    'value_priority' => $request->value_priority ?? 0
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Topic updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating topic: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete topic
     */
    public function deleteTopic($id, $topicId)
    {
        try {
            // Check if topic exists
            $exists = DB::table('ms_admin_it_aplikasi_topic')
                ->where('ms_admin_it_topic', $topicId)
                ->whereRaw("CAST(ms_admin_it_aplikasi_id AS VARCHAR(MAX)) = ?", [$id])
                ->exists();

            if (!$exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Topic not found'
                ], 404);
            }

            // Delete topic
            DB::table('ms_admin_it_aplikasi_topic')
                ->where('ms_admin_it_topic', $topicId)
                ->whereRaw("CAST(ms_admin_it_aplikasi_id AS VARCHAR(MAX)) = ?", [$id])
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Topic deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting topic: ' . $e->getMessage()
            ], 500);
        }
    }
}
