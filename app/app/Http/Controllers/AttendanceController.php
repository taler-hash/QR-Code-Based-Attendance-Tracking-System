<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\AttendanceService;

class AttendanceController extends Controller
{
    public $as;

    public function __construct()
    {
        $this->as = new AttendanceService();
    }

    public function index(Request $request) {
        $props = $this->as->display($request);

        // dd($props);

        return Inertia::render('Attendance/Attendance', $props);
    }

    public function create(Request $request) {
        $this->as->create($request);
    }

    //Tiwasa kuhaon sa ang student usa i insert ug mag scan na sa qr
    public function getStudent(Request $request, AttendanceService $as) {
        $student = $as->getStudent($request);

        return response()->json($student);
    }
}
