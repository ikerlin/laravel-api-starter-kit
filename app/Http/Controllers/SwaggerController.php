<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SwaggerController extends Controller
{
    public function docs()
    {
        return view('docs');
    }

    public function v1()
    {
        $openapi = \OpenApi\scan(app_path('Http/Controllers/Api/v1'));
        return $openapi->toJson();
    }
}
