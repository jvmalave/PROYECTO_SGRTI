<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes as OA;

#[OA\Info(title: "SGRTI API", version: "1.0.0", description: "Core Transaccional")]
#[OA\Server(url: "http://127.0.0.1:8000", description: "Servidor Local")]
abstract class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}