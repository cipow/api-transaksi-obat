<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Handlers\Response;

class Controller extends BaseController
{
    protected $response;

    public function __construct() {
      $this->response = new Response;
    }
}
