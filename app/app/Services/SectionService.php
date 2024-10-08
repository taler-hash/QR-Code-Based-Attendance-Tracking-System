<?php

namespace App\Services;

use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Random\Engine\Secure;

class SectionService
{

    public function display($request)
    {
        $model = new Section();
        $determinedSort = $this->determineSort();

        $section = $model::whereAny($model->getFillable(), 'LIKE', "%{$request->filter}%")
            ->orderBy($determinedSort['sortBy'], $determinedSort['sortType'])
            ->paginate($request->rows ?? 10);

        $props = $section->toArray();

        $props['filter'] = $request->filter;
        $props['sortBy'] = $request->sortBy;
        $props['sortType'] = (int)$request->sortType;
        $props['page'] = (int)$request->page;
        $props['id'] = (int)$request->id;

        return $props;
    }

    public function get()
    {
        return Section::select('id', 'section')
        ->when(!auth()->user()->hasRole('admin'), function($q) {
            $q->whereIn('id', auth()->user()->sections->pluck('id'));
        })
        ->get();
    }

    public function studentsUnderSectionOf($request) {
        return Section::whereIn('id', $request->sections)
        ->with([
            'users' => function($q) {
                $q->role('student');
            }
        ])
        ->get();
    }

    public function show($request)
    {
        $request->validate([
            'id' => 'required'
        ]);

        return Section::find($request->id);
    }

    public function create($request): void
    {

        Section::create($this->form($request));
    }

    public function update($request)
    {
        $section = Section::find($request->id);
        $section->update($this->form($request));
    }

    public function delete($request)
    {
        $section = Section::find($request->id);
        $section->delete();
    }

    private function determineSort()
    {
        return [
            'sortBy' => request()->sortBy ?? 'id',
            'sortType' => in_array(request()->sortType, [1, null]) ? 'asc' : 'desc'
        ];
    }

    private function form($request)
    {
        $form = $request->all();
        $form['time_in'] = Carbon::parse($form['time_in'])->Timezone('Asia/Manila');
        $form['time_out'] = Carbon::parse($form['time_out'])->Timezone('Asia/Manila');

        return $form;
    }
}
