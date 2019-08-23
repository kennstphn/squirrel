<?php

namespace App\Filesystem;

interface DriverInterface
{
    function toData();
    function getPropertyName();
    function __construct(string $fullPath);
}