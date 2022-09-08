<?php

namespace App\Http\Services;


class Service
{

    protected function getScore(): string
    {
        $result = shell_exec("python3 /var/www/html/test.py");

        if(empty($result)) {
            return rand(0, 8) . ' ' . rand(0,8);
        }

        return $result;
    }

}
