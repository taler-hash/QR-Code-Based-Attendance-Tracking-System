<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Section;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Carbon\CarbonPeriod;

class OverviewExport implements WithMultipleSheets
{

    //tiwasa mag himue ug excel
    public $data;
    public $dates;
    public $overviewDates;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        $this->setOverviewDates();
        $this->setUserOverview();

        $sheets = [];

        foreach ($this->data as $key => $value) {
            $sheets[] = new SectionSheet($value);
        }

        return $sheets;
    }

    private function setOverviewDates()
    {
        $startDate = Carbon::parse(request()->startDate)->timezone('Asia/Manila')->startOfDay();
        $endDate = Carbon::parse(request()->endDate)->timezone('Asia/Manila')->startOfDay();

        $period = CarbonPeriod::create($startDate, $endDate);

        // Convert the period into an array of dates
        $dates = [];

        foreach ($period as $date) {
            $dates[$date->format('Y-m-d')] = [
                'date' => $date->format('Y-m-d'),
                'isDisabled' => $this->disableDate($date),
                'time_in' => null,
                'time_out' => null,
                'remarks' => 'Absent'
            ];
        }

        $this->overviewDates = $dates;
    }

    private function setUserOverview()
    {
        $this->data = collect($this->data)->map(function ($i) {
            $i['overview'] = $this->overviewDates;
            $i['users'] = collect($i['users'])
                ->filter(function ($i2) {
                    $isTeacher = collect($i2['roles'])->contains(function ($i3) {
                        return $i3['id'] === 2;
                    });

                    return !$isTeacher;
                })
                ->map(function ($i2) {
                    $overviewDates = &$this->overviewDates;
                    $this->fillOverview($overviewDates, $i2['attendances']);
                    $i2['overview'] = $overviewDates;
                    $i2['summary'] = $this->summaryAttendanceCount($overviewDates);

                    return $i2;
                })->toArray();

            return $i;
        })->toArray();
    }

    private function fillOverview(&$overviewDates, $attendances)
    {
        collect($attendances)->map(function ($q) use (&$overviewDates) {
            if (isset($overviewDates[$q['rawDate']])) {
                $overviewDates[$q['rawDate']]['time_in'] = $q['time_in'];
                $overviewDates[$q['rawDate']]['time_out'] = $q['time_out'];
                $overviewDates[$q['rawDate']]['remarks'] = $this->setRemarks($q['time_in'], $q['time_out']);
            }
        });
    }

    private function setRemarks($time_in, $time_out)
    {
        $status = $time_in['status'] . "," . $time_out['status'];
        $remarks = [
            'ontime,ontime' => 'Present',
        ];

        return $remarks[$status] ?? 'Risk';
    }

    public function summaryAttendanceCount($overviewDates)
    {
        $summary = [
            'present' => 0,
            'risk' => 0,
            'absent' => 0
        ];

        collect($overviewDates)->map(function ($q) use (&$summary) {
            $summary[strtolower($q['remarks'])]++;
        });

        return $summary;
    }

    private function disableDate($date)
    {
        $disableAsWeek = collect(request()->weekDaysToDisable)->contains(function ($i) use ($date) {
            return $i === strtolower($date->copy()->format('l'));
        });
        $disableAsDate = collect(request()->excludeDates)->contains(function ($i) use ($date) {
            return $date->eq(Carbon::parse($i)->timezone('Asia/Manila')->startOfDay());
        });


        return $disableAsWeek || $disableAsDate;
    }
}
