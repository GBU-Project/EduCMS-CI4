<?php

namespace App\Controllers;

class Main extends BaseController
{
    public function error_404()
    {
        return $this->response
            ->setStatusCode(404)
            ->setBody(view('errors/html/error_custom_404'));
    }
}
