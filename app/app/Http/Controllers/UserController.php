<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{
    public $us;

    public function __construct()
    {
        $this->us = new UserService('teacher');
    }
    
}
