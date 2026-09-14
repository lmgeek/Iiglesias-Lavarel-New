<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;

class SwaggerController extends Controller
{
    public function index()
    {
        return response()->file(storage_path('api-docs/api-docs.json'));
    }

    public function ui()
    {
        return view('l5-swagger::documentation');
    }
}
