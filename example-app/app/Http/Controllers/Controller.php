<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function getScore(): string
    {
        $result = shell_exec("python3 /var/www/html/test.py");

        if(empty($result)) {
            return rand(0, 8) . ' ' . rand(0,8);
        }

        return $result;
    }

}
