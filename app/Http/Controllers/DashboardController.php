<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers        = User::count();
        $totalSubjects     = Subject::count();
        $subjectBySemester = Subject::select('semester', DB::raw('count(*) as count'))
                                ->groupBy('semester')
                                ->pluck('count', 'semester');
        $subjectByUnits    = Subject::select('units', DB::raw('count(*) as count'))
                                ->groupBy('units')
                                ->orderBy('units')
                                ->pluck('count', 'units');
        $usersPerMonth     = User::select(
                                DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"),
                                DB::raw('count(*) as count'))
                                ->where('created_at', '>=', now()->subMonths(6))
                                ->groupBy(DB::raw("DATE_FORMAT(created_at, '%b %Y')"))
                                ->orderBy(DB::raw("MIN(created_at)"))
                                ->pluck('count', 'month');
        $subjectsPerMonth  = Subject::select(
                                DB::raw("DATE_FORMAT(created_at, '%b %Y') as month"),
                                DB::raw('count(*) as count'))
                                ->where('created_at', '>=', now()->subMonths(6))
                                ->groupBy(DB::raw("DATE_FORMAT(created_at, '%b %Y')"))
                                ->orderBy(DB::raw("MIN(created_at)"))
                                ->pluck('count', 'month');

        return view('dashboard.index', compact(
            'totalUsers', 'totalSubjects', 'subjectBySemester',
            'subjectByUnits', 'usersPerMonth', 'subjectsPerMonth'
        ));
    }
}