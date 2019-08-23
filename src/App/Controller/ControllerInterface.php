<?php


namespace App\Controller;


use App\Request\Request;
use App\Response\Response;

interface ControllerInterface
{
    function __invoke(Response $response);
    function __construct(array $arguments, Request $request);

}