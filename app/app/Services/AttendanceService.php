<?php

namespace App\Services;
use App\Models\Attendance;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Support\Carbon;


class AttendanceService {

    private $us;

    public function __construct()
    {
        $this->us = new UserService('student');
    }
    
    public function create($request): void {

        $now = Carbon::now('Asia/Manila');

        $attendance = Attendance::firstOrCreate([
            'user' => $request->id,
            'date' => $now->format('Y-m-d')
        ]);

        $attendance->timeLogs()->create([
            'time' => $now->format('H:i:s')
        ]);
    }
    
    public function read($request) {
        
    }

    public function display($request) {
        $model = new Attendance();
        $determinedSort = $this->determineSort();

        $attendances = $model::with(['user.sections', 'timeLogs'])
            ->whereAny($model->getFillable(), 'LIKE', "%{$request->filter}%")
            ->orderBy($determinedSort['sortBy'], $determinedSort['sortType'])
            ->paginate($request->rows ?? 10);

        $props = $attendances->toArray();

        $props['filter'] = $request->filter;
        $props['sortBy'] = $request->sortBy;
        $props['sortType'] = (int)$request->sortType;
        $props['page'] = (int)$request->page;
        $props['id'] = (int)$request->id;

        return $props;
    }


    public function getStudent($request) {

        $id = base64_decode($request->uuid);
        // $student = User::where('id', $id)->with('sections')->first();
        // $exists = auth()->user()->sections->contains('section', $student->sections[0]->section);

        // dd($exists);


        $request->validate([
            'uuid' => [
                'bail',
                function($value, $key, $fail) use ($id) {
                    $exists = User::where('id', $id)->exists();
    
                    if(!$exists) {
                        $fail('QR code is invalid');
                    }
                },
                function ($value, $key, $fail) use ($id) {
                    $student = User::where('id', $id)->with('sections')->first();
                    
                    $exists = auth()->user()->sections->contains('section', $student->sections[0]->section);
    
                    if(!$exists) {
                        $fail('Student is not one of your sections');
                    }
                }
            ]
        ]);

        return $this->us->get($id);
    }

    public function update() {

    }

    public function delete() {

    }

    private function determineSort()
    {
        return [
            'sortBy' => request()->sortBy ?? 'id',
            'sortType' => in_array(request()->sortType, [1, null]) ? 'asc' : 'desc'
        ];
    }
}