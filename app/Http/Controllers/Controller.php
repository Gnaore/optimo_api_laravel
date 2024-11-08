<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="GODWINMULTISERVICE API Documentation",
 *      description="Plateforme d'échanges de monnaies électroniques",
 *      @OA\Contact(
 *          email="flory.demaf@gmail.com.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Demo API Server"
 * )

 *
 *
 */
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
