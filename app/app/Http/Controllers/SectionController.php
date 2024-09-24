<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateSectionRequest;
use App\Http\Requests\UpdateSectionRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\SectionService;
use App\Models\Section;

class SectionController extends Controller
{
    public function index(Request $request, SectionService $ss)
    {
        $sections = $ss->display($request);

        return Inertia::render('Section/Section', $sections);
    }

    public function get(SectionService $ss)
    {
        $sections = $ss->get();
        return response()->json($sections);
    }

    public function show(Request $request, SectionService $ss)
    {
        $section = $ss->show($request);
        return response()->json($section);
    }

    public function create(CreateSectionRequest $request, SectionService $ss)
    {
        $ss->create($request);
    }

    public function read()
    {
        Inertia::render('Section/Read');
    }

    public function update(UpdateSectionRequest $request, SectionService $ss)
    {
        $ss->update($request);
    }

    public function delete(Request $request, SectionService $ss)
    {
        $ss->delete($request);
    }
}
