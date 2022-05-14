<?php

namespace Marrios\Router\Pages;

trait NotFound
{
    public function notFound()
    {
        header("HTTP/1.1 404 Not Found");
        echo json_encode("Page Not Found");
    }
}