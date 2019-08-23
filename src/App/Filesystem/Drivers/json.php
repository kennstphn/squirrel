<?php

namespace App\Filesystem\Drivers;

use App\Filesystem\DriverInterface;
use App\Filesystem\ExtensionParser;
use App\Filesystem\FilesystemError;

class json implements DriverInterface
{
    protected $fullPath;
    protected $data;
    protected $propertyName;

    /**
     * json constructor.
     * @param $fullPath
     * @throws FilesystemError
     */
    function __construct(string $fullPath)
    {
        $this->fullPath = $fullPath;
        $pieces = explode(DIRECTORY_SEPARATOR,$this->fullPath);
        $this->propertyName =  (new ExtensionParser(array_pop($pieces )))->getNameWithoutExtension();

        if( ! is_readable($this->fullPath)){
            throw new FilesystemError($this->fullPath .' is not readable');
        }

        $contents = file_get_contents($this->fullPath);
        if ( ! $contents){
            throw new FilesystemError('Unable to get contents of '.$this->fullPath);
        }

        try{
            $data = json_decode($contents,false, 512,JSON_THROW_ON_ERROR);
        }catch (\JsonException $e){
            throw new FilesystemError('Unable to parse '.$this->fullPath.'; '.$e->getMessage(),$e->getCode(),$e);
        }

        $this->data = $data;
    }

    function toData()
    {
        return $this->data;
    }

    function getPropertyName()
    {
        return $this->propertyName;
    }


}