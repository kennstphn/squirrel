<?php

namespace App\Filesystem\Drivers;


use App\Filesystem\DriverInterface;
use App\Filesystem\ExtensionParser;

class yaml implements DriverInterface
{
    function toData()
    {
        return $this->data;
    }

    function getPropertyName()
    {
        return $this->propertyName;
    }

    protected $data, $propertyName, $fullPath;
    public function __construct(string $fullPath)
    {
        $this->fullPath = $fullPath;
        $pieces = explode(DIRECTORY_SEPARATOR,$this->fullPath);
        $this->propertyName =  (new ExtensionParser(array_pop($pieces )))->getNameWithoutExtension();

        $this->data =\Symfony\Component\Yaml\Yaml::parseFile($this->fullPath);

    }


}