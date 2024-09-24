<?php

namespace App\Services;
use App\Models\Attendance;

class AttendanceService {
    
    public function create($request): void {
        Attendance::create($request->all);
    }

    public function read() {
        
    }

    public function update() {

    }

    public function delete() {

    }


}