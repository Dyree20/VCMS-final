<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use App\Models\Clamping;
use App\Models\Archive;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ClampingController extends Controller
{   
    public function create()
    {
        return view('dashboards.enforcer-add-clamping');
    }
    public function index(Request $request)
    {
        // Exclude archived clampings (released and cancelled) - they should only appear in archives
        $query = Clamping::whereNotIn('status', ['released', 'cancelled']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('plate_no', 'LIKE', '%' . $search . '%')
                  ->orWhere('ticket_no', 'LIKE', '%' . $search . '%');
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $clampings = $query->orderBy('created_at', 'desc')->get();
        return view('clamping', compact('clampings')); 
    }

    public function store(Request $request)
    {
        Log::info('Photo from request:', [$request->file('photo')]);

        $validated = $request->validate([
            'plate_no' => 'required|string|max:20',
            'vehicle_type'  => 'required|string|max:50',
            'reason'        => 'required|string|max:255',
            'location'      => 'required|string|max:255',
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'fine_amount'   => 'required|numeric|min:0',
        ]);

        // Check for duplicate submission (same plate, location, and reason within last 5 seconds)
        $recentDuplicate = Clamping::where('user_id', Auth::id())
            ->where('plate_no', $validated['plate_no'])
            ->where('location', $validated['location'])
            ->where('reason', $validated['reason'])
            ->where('created_at', '>=', now()->subSeconds(5))
            ->first();

        if ($recentDuplicate) {
            return response()->json([
                'success' => false,
                'message' => 'This clamping record was just added. Please wait a moment.',
                'id' => $recentDuplicate->id
            ], 400);
        }

        $lastClamping = Clamping::orderBy('id', 'desc')->first();
        $nextNumber = $lastClamping ? $lastClamping->id + 1 : 1;

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('clamping_photos', 'public');
        }

        $clamping = Clamping::create([
            'user_id'       => Auth::id(),
            'ticket_no'    => 'TKT-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT),
            'plate_no'      => $validated['plate_no'],
            'vehicle_type'  => $validated['vehicle_type'],
            'reason'        => $validated['reason'],
            'location'      => $validated['location'],
            'date_clamped'  => now(),
            // standardized status values are lowercase
            'status'        => 'pending',
            'photo'         => $photoPath,
            'fine_amount'   => $validated['fine_amount'],
        ]);

        // No need to call save() after create() - it's already saved

        return response()->json([
            'success' => true,
            'message' => 'Clamping added successfully!',
            'id' => $clamping->id,
        ]);
    }

    public function print($id)
    {   
        $clamping = Clamping::findOrFail($id);
        $qrCode = QrCode::size(120)->generate(url('/verify/' . $clamping->id));

        return view('partials.receipt', compact('clamping', 'qrCode'));
    }

    public function verify($id)
    {
        $clamping = Clamping::find($id);

        if (!$clamping) {
            return view('verify.notfound');
        }

        return view('verify.portal', compact('clamping'));
    }
    
    public function show($id)
    {
        return response()->json(Clamping::findOrFail($id));
    }

    // Action endpoints: accept, reject, approve
    public function accept(Request $request, $id)
    {
        $clamping = Clamping::findOrFail($id);
        $clamping->status = 'accepted';
        $clamping->save();

        // record activity (try DB insert, fallback to log file)
        $user = Auth::user();
        try {
            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id' => $user ? $user->id : null,
                    'username' => $user ? ($user->name ?? $user->email ?? 'user') : 'guest',
                    'action' => 'accepted',
                    'clamping_id' => $clamping->id,
                ]);
            }
        } catch (\Exception $ex) {
            Log::info('ActivityLog insert failed:', ['error' => $ex->getMessage(), 'user' => $user ? $user->id : null, 'clamping' => $clamping->id, 'action' => 'accepted']);
        }

        return response()->json(['success' => true, 'status' => $clamping->status, 'actor' => $user ? ($user->name ?? $user->email ?? $user->id) : null]);
    }

    public function reject(Request $request, $id)
    {
        $clamping = Clamping::findOrFail($id);
        $clamping->status = 'rejected';
        $clamping->save();

        $user = Auth::user();
        try {
            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id' => $user ? $user->id : null,
                    'username' => $user ? ($user->name ?? $user->email ?? 'user') : 'guest',
                    'action' => 'rejected',
                    'clamping_id' => $clamping->id,
                ]);
            }
        } catch (\Exception $ex) {
            Log::info('ActivityLog insert failed:', ['error' => $ex->getMessage(), 'user' => $user ? $user->id : null, 'clamping' => $clamping->id, 'action' => 'rejected']);
        }

        return response()->json(['success' => true, 'status' => $clamping->status, 'actor' => $user ? ($user->name ?? $user->email ?? $user->id) : null]);
    }

    public function approve(Request $request, $id)
    {
        $clamping = Clamping::findOrFail($id);
        $clamping->status = 'approved';
        $clamping->save();

        $user = Auth::user();
        try {
            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id' => $user ? $user->id : null,
                    'username' => $user ? ($user->name ?? $user->email ?? 'user') : 'guest',
                    'action' => 'approved',
                    'clamping_id' => $clamping->id,
                ]);
            }
        } catch (\Exception $ex) {
            Log::info('ActivityLog insert failed:', ['error' => $ex->getMessage(), 'user' => $user ? $user->id : null, 'clamping' => $clamping->id, 'action' => 'approved']);
        }

        return response()->json(['success' => true, 'status' => $clamping->status, 'actor' => $user ? ($user->name ?? $user->email ?? $user->id) : null]);
    }

    // Admin management methods
    public function editShow($id)
    {
        $clamping = Clamping::findOrFail($id);
        return view('clampings.show', compact('clamping'));
    }

    public function editForm($id)
    {
        $clamping = Clamping::findOrFail($id);
        return view('clampings.edit', compact('clamping'));
    }

    public function update(Request $request, $id)
    {
        $clamping = Clamping::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,released,cancelled',
        ]);

        $oldStatus = $clamping->status;
        $clamping->status = $validated['status'];
        $clamping->save();

        $user = Auth::user();
        try {
            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id' => $user ? $user->id : null,
                    'username' => $user ? ($user->name ?? $user->email ?? 'user') : 'guest',
                    'action' => 'updated_status',
                    'clamping_id' => $clamping->id,
                ]);
            }
        } catch (\Exception $ex) {
            Log::info('ActivityLog insert failed:', ['error' => $ex->getMessage()]);
        }

        return redirect()->route('clampings')->with('success', "Clamping status updated from {$oldStatus} to {$validated['status']}");
    }

    public function cancel(Request $request, $id)
    {
        $clamping = Clamping::findOrFail($id);
        $clamping->status = 'cancelled';
        $clamping->save();

        $user = Auth::user();
        try {
            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id' => $user ? $user->id : null,
                    'username' => $user ? ($user->name ?? $user->email ?? 'user') : 'guest',
                    'action' => 'cancelled',
                    'clamping_id' => $clamping->id,
                ]);
            }
        } catch (\Exception $ex) {
            Log::info('ActivityLog insert failed:', ['error' => $ex->getMessage()]);
        }

        // Archive the cancelled clamping
        try {
            // Check if already archived
            $existingArchive = Archive::where('clamping_id', $clamping->id)->first();
            if (!$existingArchive) {
                $archiveData = [
                    'clamping_id' => $clamping->id,
                    'user_id' => $clamping->user_id,
                    'ticket_no' => $clamping->ticket_no,
                    'plate_no' => $clamping->plate_no,
                    'vehicle_type' => $clamping->vehicle_type ?? null,
                    'reason' => $clamping->reason,
                    'location' => $clamping->location,
                    'fine_amount' => $clamping->fine_amount,
                    'archived_status' => 'cancelled',
                    'archived_date' => now(),
                    'archived_by' => Auth::id(),
                ];
                
                Log::info('Attempting to create archive in cancel:', $archiveData);
                $archive = Archive::create($archiveData);
                Log::info('Archive created successfully in cancel:', ['archive_id' => $archive->id]);
            } else {
                Log::info('Archive already exists for clamping in cancel:', ['clamping_id' => $clamping->id, 'archive_id' => $existingArchive->id]);
            }
        } catch (\Exception $ex) {
            Log::error('Archive creation failed in cancel:', [
                'error' => $ex->getMessage(),
                'clamping_id' => $clamping->id,
                'file' => $ex->getFile(),
                'line' => $ex->getLine(),
                'trace' => $ex->getTraceAsString()
            ]);
        }

        return redirect()->route('clampings')->with('success', 'Clamping cancelled successfully');
    }

    public function release(Request $request, $id)
    {
        $clamping = Clamping::findOrFail($id);
        
        // Only allow releasing if status is paid
        if ($clamping->status !== 'paid') {
            return redirect()->route('clampings.show', $clamping->id)->with('error', 'Can only release paid violations.');
        }
        
        $clamping->status = 'released';
        $clamping->save();

        $user = Auth::user();
        try {
            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id' => $user ? $user->id : null,
                    'username' => $user ? ($user->name ?? $user->email ?? 'user') : 'guest',
                    'action' => 'released',
                    'clamping_id' => $clamping->id,
                ]);
            }
        } catch (\Exception $ex) {
            Log::info('ActivityLog insert failed:', ['error' => $ex->getMessage()]);
        }

        // Archive the released clamping
        try {
            // Check if already archived
            $existingArchive = Archive::where('clamping_id', $clamping->id)->first();
            if (!$existingArchive) {
                $archiveData = [
                    'clamping_id' => $clamping->id,
                    'user_id' => $clamping->user_id,
                    'ticket_no' => $clamping->ticket_no,
                    'plate_no' => $clamping->plate_no,
                    'vehicle_type' => $clamping->vehicle_type ?? null,
                    'reason' => $clamping->reason,
                    'location' => $clamping->location,
                    'fine_amount' => $clamping->fine_amount,
                    'archived_status' => 'released',
                    'archived_date' => now(),
                    'archived_by' => Auth::id(),
                ];
                
                Log::info('Attempting to create archive:', $archiveData);
                $archive = Archive::create($archiveData);
                Log::info('Archive created successfully:', ['archive_id' => $archive->id]);
            } else {
                Log::info('Archive already exists for clamping:', ['clamping_id' => $clamping->id, 'archive_id' => $existingArchive->id]);
            }
        } catch (\Exception $ex) {
            Log::error('Archive creation failed in release:', [
                'error' => $ex->getMessage(), 
                'clamping_id' => $clamping->id,
                'file' => $ex->getFile(),
                'line' => $ex->getLine(),
                'trace' => $ex->getTraceAsString()
            ]);
        }

        return redirect()->route('clampings')->with('success', 'Clamping #' . $clamping->ticket_no . ' has been released and moved to archives.');
    }

    public function destroy($id)
    {
        $clamping = Clamping::findOrFail($id);
        
        // Archive the clamping before deleting
        try {
            // Check if already archived
            $existingArchive = Archive::where('clamping_id', $clamping->id)->first();
            if (!$existingArchive) {
                $archiveData = [
                    'clamping_id' => $clamping->id,
                    'user_id' => $clamping->user_id,
                    'ticket_no' => $clamping->ticket_no,
                    'plate_no' => $clamping->plate_no,
                    'vehicle_type' => $clamping->vehicle_type ?? null,
                    'reason' => $clamping->reason,
                    'location' => $clamping->location,
                    'fine_amount' => $clamping->fine_amount,
                    'archived_status' => 'cancelled',
                    'archived_date' => now(),
                    'archived_by' => Auth::id(),
                ];
                
                Log::info('Attempting to create archive in destroy:', $archiveData);
                $archive = Archive::create($archiveData);
                Log::info('Archive created successfully in destroy:', ['archive_id' => $archive->id]);
            } else {
                Log::info('Archive already exists for clamping in destroy:', ['clamping_id' => $clamping->id, 'archive_id' => $existingArchive->id]);
            }
        } catch (\Exception $ex) {
            Log::error('Archive creation failed in destroy:', [
                'error' => $ex->getMessage(), 
                'clamping_id' => $clamping->id,
                'file' => $ex->getFile(),
                'line' => $ex->getLine(),
                'trace' => $ex->getTraceAsString()
            ]);
        }

        $user = Auth::user();
        try {
            if (class_exists(ActivityLog::class)) {
                ActivityLog::create([
                    'user_id' => $user ? $user->id : null,
                    'username' => $user ? ($user->name ?? $user->email ?? 'user') : 'guest',
                    'action' => 'deleted',
                    'clamping_id' => $clamping->id,
                ]);
            }
        } catch (\Exception $ex) {
            Log::info('ActivityLog insert failed:', ['error' => $ex->getMessage()]);
        }

        // Delete associated photo if exists
        if ($clamping->photo) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($clamping->photo);
        }

        // Delete the clamping (archive will persist due to set null constraint)
        $clamping->delete();

        return redirect()->route('clampings')->with('success', 'Clamping record deleted successfully');
    }
}
