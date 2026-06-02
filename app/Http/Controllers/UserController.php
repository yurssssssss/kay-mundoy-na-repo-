<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // ── Index ─────────────────────────────────────────────────
   public function index(Request $request)
{
    $search  = $request->get('search', '');
    $perPage = $request->get('per_page', 10);

    $users = User::when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        })
        ->latest()
        ->paginate($perPage)
        ->withQueryString();

    $totalUsers        = User::count();
    $totalSubjects     = Subject::count();
    $subjectBySemester = Subject::select('semester', DB::raw('count(*) as count'))
                            ->groupBy('semester')->pluck('count', 'semester');
    $subjectByUnits    = Subject::select('units', DB::raw('count(*) as count'))
                            ->groupBy('units')->orderBy('units')->pluck('count', 'units');
    $usersPerMonth     = User::select(
                            DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"),
                            DB::raw('count(*) as count'))
                            ->where('created_at', '>=', now()->subMonths(6))
                            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%b %Y')"))
                            ->orderBy('created_at')->pluck('count', 'month');
    $subjectsPerMonth  = Subject::select(
                            DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"),
                            DB::raw('count(*) as count'))
                            ->where('created_at', '>=', now()->subMonths(6))
                            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%b %Y')"))
                            ->orderBy('created_at')->pluck('count', 'month');

   return view('users.index', compact('users', 'search', 'perPage'));
    
}

    // ── Store ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => ['required', Password::min(8)],
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')
            ->with('toast_success', 'User "' . $request->name . '" added successfully!');
    }

    // ── Edit (JSON) ───────────────────────────────────────────
    public function edit(User $user)
    {
        return response()->json($user->only('id', 'name', 'email'));
    }

    // ── Update ────────────────────────────────────────────────
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', Password::min(8)],
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('toast_success', 'User "' . $user->name . '" updated successfully!');
    }

    // ── Destroy ───────────────────────────────────────────────
    public function destroy(User $user)
    {
        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('toast_success', 'User "' . $name . '" deleted successfully!');
    }
}
