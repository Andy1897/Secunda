<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Activity::query()->with('children')->whereNull('parent_id')->get()
        );
    }
}
