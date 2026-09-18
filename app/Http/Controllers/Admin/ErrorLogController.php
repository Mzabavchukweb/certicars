<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ErrorLog;
use Illuminate\Http\Request;

class ErrorLogController extends Controller
{
    public function index(Request $request)
    {
        $q = ErrorLog::query()->with('user')->orderByDesc('id');

        if ($src = $request->query('source')) {
            $q->where('source', 'like', $src . '%');
        }
        if ($request->query('level') === 'error') {
            $q->where('level', 'error');
        }
        if ($search = trim((string) $request->query('q'))) {
            $q->where(function ($w) use ($search) {
                $w->where('message', 'like', '%' . $search . '%')
                  ->orWhere('url', 'like', '%' . $search . '%');
            });
        }

        $logs = $q->paginate(50)->withQueryString();
        $sources = ErrorLog::selectRaw('source, count(*) as n')->groupBy('source')->orderByDesc('n')->get();
        $last24 = ErrorLog::where('created_at', '>=', now()->subDay())->count();

        return view('admin.errors.index', compact('logs', 'sources', 'last24'));
    }

    /** Błąd zgłoszony z przeglądarki (kreator: nieudane wgranie / zapis). */
    public function client(Request $request)
    {
        $data = $request->validate([
            'source'  => 'required|string|max:64',
            'message' => 'required|string|max:1000',
            'context' => 'nullable|array',
            'car_id'  => 'nullable|integer',
        ]);
        ErrorLog::record(
            'client.' . preg_replace('/[^a-z0-9_.-]/i', '', $data['source']),
            $data['message'],
            $data['context'] ?? [],
            'error',
            $data['car_id'] ?? null
        );
        return response()->json(['success' => true]);
    }

    public function clear()
    {
        ErrorLog::query()->delete();
        return redirect()->route('admin.errors.index')->with('success', 'Rejestr błędów wyczyszczony.');
    }
}
