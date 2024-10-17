<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Section;
use App\Services\UserService;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Exports\OverviewExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceService
{

    private $us;

    public function __construct()
    {
        $this->us = new UserService('student');
    }

    public function create($request): void
    {

        $now = Carbon::now('Asia/Manila');

        $attendance = Attendance::firstOrCreate([
            'user' => $request->id,
            'date' => $now->format('Y-m-d')
        ]);

        $attendance->timeLogs()->create([
            'time' => $now->format('H:i:s')
        ]);
    }

    public function read($request) {}

    public function display($request)
    {
        $model = new Attendance();
        $determinedSort = $this->determineSort();

        $attendances = $model::with(['user.sections', 'timeLogs'])
            ->when($request->date, function ($q) use ($request) {
                $q->where('date', Carbon::parse($request->date)->timezone('Asia/Manila')->format('Y-m-d'));
            })
            ->where(function ($query) use ($model, $request) {
                // Filter based on user attributes
                $query->whereHas('user', function ($q) use ($model, $request) {
                    $q->orWhere('users.first_name', 'LIKE', "%{$request->filter}%");
                    $q->orWhere('users.last_name', 'LIKE', "%{$request->filter}%");
                })
                // Filter based on sections attributes
                ->orWhereHas('user.sections', function ($q) use ($model, $request) {
                    $q->orWhere('users.first_name', 'LIKE', "%{$request->filter}%");
                    $q->orWhere('users.last_name', 'LIKE', "%{$request->filter}%");
                })
                ->orderBy('last_name', 'asc')
                ->orWhereAny($model->getFillable(), 'LIKE', "%{$request->filter}%");
            })
            // Additional filters on the model itself
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

    public function overview($request)
    {
        $sectionWithUsersAndAttendance = Section::with(['users.attendances', 'users.roles'])
        ->whereIn('id', $request->sections)
        ->whereHas('users', function ($q) use ($request) {

            $q->whereHas('attendances', function ($q2) use ($request) {
                $parsedStartDate = Carbon::parse($request->startDate)->timezone('Asia/Manila')->format('Y-m-d');
                $parsedEndDate = Carbon::parse($request->endDate)->timezone('Asia/Manila')->format('Y-m-d');

                $q2
                ->whereBetween('date', [$parsedStartDate, $parsedEndDate]);
            });
        })->get()->toArray();

        return Excel::download(new OverviewExport($sectionWithUsersAndAttendance), 'overview.xlsx');
    }

    private function setTime($timeLogs, $section) {
        $res = collect($attendances)->map(function($i) {
            $i['time_in'] = 'test';
            $i['time_out'] = 'test';

            return $i;
        });

        dd($res);
    }

    public function transformDate() {
        $startDate = Carbon::parse(request()->startDate)->timezone('Asia/Manila')->startOfDay();
        $endDate = Carbon::parse(request()->endDate)->timezone('Asia/Manila')->startOfDay();

        $dateRange = [];
        $currentDate = $startDate->copy();


        while($currentDate->lte($endDate)) {
            $formattedDate = $currentDate->toDateString();
            
            $dateRange[$formattedDate] = [
                'week' => $currentDate->copy()->format('l'),
                'date' => $formattedDate,
                'disabled' => $this->disableDate($currentDate),
                'time_in' => null,
                'time_out' => null
            ];

            $currentDate->addDay();
        }

        return $dateRange;
    }



    public function getStudentAttendance($request)
    {
        $model = new Attendance();

        $attendances = $model::where('user', $request->id)
            ->with(['user.sections', 'timeLogs'])
            ->paginate(10);

        $props = $attendances->toArray();

        return $props;
    }

    public function getCurrentTotalAttended()
    {
        $sections = auth()->user()->sections->pluck('id')->all();

        $count = Attendance::with('users.sections')
            ->where('date', Carbon::now()->timezone('Asia/Manila')->format('Y-m-d'))
            ->whereHas('users.sections', function ($q) use ($sections) {
                $q->whereIn('sections.id', $sections);
            })
            ->get()->count();

        return $count;
    }


    public function getStudent($request)
    {

        $id = (int)base64_decode($request->uuid);

        $request->validate([
            'uuid' => [
                'bail',
                function ($value, $key, $fail) use ($id) {
                    $exists = User::where('id', $id)->exists();

                    if (!$exists) {
                        $fail('QR code is invalid');
                    }
                },
                function ($value, $key, $fail) use ($id) {
                    $student = User::where('id', $id)->with('sections')->first();

                    $exists = auth()->user()->sections->contains('section', $student->sections[0]->section);

                    if (!$exists) {
                        $fail('Student is not one of your sections');
                    }
                }
            ]
        ]);


        return $this->us->get((object)['id' => (int)$id]);
    }

    public function update() {}

    public function delete() {}

    private function determineSort()
    {
        return [
            'sortBy' => request()->sortBy ?? 'id',
            'sortType' => in_array(request()->sortType, [1, null]) ? 'asc' : 'desc'
        ];
    }
}
