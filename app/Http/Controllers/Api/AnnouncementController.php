<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Traits\ApiResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class AnnouncementController extends Controller
{
    use ApiResponse;
public function getActive(Request $request)
{
    try {
        $userRole = $request->user()->roles()->first()->name ?? null;
        
        // Start with base query
        $query = Announcement::query()
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expired_at')
                  ->orWhere('expired_at', '>', now());
            })
            ->where(function ($q) use ($userRole) {
                $q->whereRaw('COALESCE(JSON_LENGTH(roles), 0) = 0') // check for NULL or empty array
                  ->orWhereRaw('JSON_CONTAINS(roles, ?)', ['"' . $userRole . '"']);
            })
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');

        $announcements = $query->get();

        Log::debug('Query debug:', [
            'sql' => $query->toSql(),
            'bindings' => $query->getBindings(),
            'result_count' => $announcements->count(),
            'results' => $announcements->toArray()
        ]);

        return $this->successResponse($announcements, 'Successfully fetched');
    } catch (\Exception $e) {
        Log::error('Error fetching announcements', [
            'error' => $e->getMessage()
        ]);
        return $this->errorResponse($e->getMessage(), 400);
    }
}
}