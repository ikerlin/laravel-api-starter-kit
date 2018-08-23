<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\Traits\ApiResponse;
use App\Http\Controllers\Controller;

/**
 * @OA\Info(title="My First API", version="0.1")
 */

/**
 * @OA\Get(
 *     path="/api/resource.json",
 *     @OA\Response(response="200", description="An example resource")
 * )
 */
class ApiController extends Controller
{
    use ApiResponse;

    //其他通用的Api帮助函数

}
