<?php

namespace Marrios\Router\Pages;

trait NotFound
{
    public function notFound($param = "Page Not Found")
    {
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["error" => $param]);
    }
}
