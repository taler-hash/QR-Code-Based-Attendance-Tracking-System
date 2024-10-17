<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Services\UserService;

use Inertia\Inertia;

class StudentController extends Controller
{

    public $us;

    public function __construct()
    {
        $this->us = new UserService('student');
    }

    public function index(Request $request) {
        $props = $this->us->display($request);

        return Inertia::render('Student/Student', $props);
    }

    public function create(CreateStudentRequest $request)
    {
        $this->us->create($request);
    }

    public function get(Request $request) {
        return response()->json($this->us->get($request));
    }

    public function getCount() {
        return response()->json($this->us->getCount());
    }

    public function update(UpdateStudentRequest $request) {
        $this->us->update($request);
    }

    public function delete(Request $request) {
        $this->us->delete($request);
    }
}
