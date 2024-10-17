<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\UserService;
use App\Http\Requests\CreateTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;

class TeacherController extends Controller
{

    public $us;

    public function __construct()
    {
        $this->us = new UserService('teacher');
    }

    public function index(Request $request)
    {
        $props = $this->us->display($request);

        return Inertia::render('Teacher/Teacher', $props);
    }

    public function create(CreateTeacherRequest $request)
    {
        $this->us->create($request);
    }

    public function read( $request) {
        
    }

    public function getCount() {
        return response()->json($this->us->getCount());
    }

    public function update(UpdateTeacherRequest $request) {
        $this->us->update($request);
    }

    public function delete(Request $request) {
        $this->us->delete($request);
    }
}
