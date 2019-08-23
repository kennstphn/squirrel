<?php

namespace App\Filesystem\Drivers;

use App\Filesystem\DriverInterface;
use App\Filesystem\ExtensionParser;
use App\Filesystem\FilesystemError;

class jpg implements DriverInterface
{
    function toData()
    {
        return $this->data;
    }

    function getPropertyName()
    {
        return $this->propertyName;
    }

    protected $data, $propertyName;

    public function __construct(string $fullPath)
    {
        $relPath = substr($fullPath,strlen(ROOT_DIR.'/data'));
        $newFile = ROOT_DIR.'/public'.$relPath;
        $dir = substr($newFile,0,strrpos($newFile,DIRECTORY_SEPARATOR));
        if (! file_exists($dir) || ! is_dir($dir)){
            if ( ! mkdir($dir)){
                throw new FilesystemError('Unable to create directory '.$dir.' for file '.$newFile);
            }
        }
        copy($fullPath,$newFile);

        $this->propertyName = (new ExtensionParser(substr($newFile,strrpos($newFile,DIRECTORY_SEPARATOR)+1 )))->getNameWithoutExtension();
        $this->data = $relPath;
    }


}