<?php

namespace Thumbrise\Toolkit\Tests\Laravel\Skeleton\app\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use OpenApi\Attributes\Info;

/**
 * @OA\Info(
 *     title="Soundhaunt",
 *     version="1.0.0",
 *     description="OpenApi песочница",
 *     @OA\Contact(
 *         email="support@example.com",
 *         name="Техническая поддержка"
 *     ),
 * ),
 * @OA\SecurityScheme(
 *          scheme="bearer",
 *          type="http",
 *          in="header",
 *          securityScheme="bearerAuth",
 *          name="Authentication",
 *          bearerFormat="JWT",
 *      )
 */
#[Info(
    version: '1.0.0', description: 'test', title: 'test'
)]
class Controller extends BaseController
{

}