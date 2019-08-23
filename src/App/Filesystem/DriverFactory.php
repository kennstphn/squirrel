<?php

namespace App\Filesystem;


class DriverFactory
{

    /**
     * @param string $pathToFile
     * @return mixed
     * @throws FilesystemError
     */
    static function getDriver(string $pathToFile){

        $extensionParser = new ExtensionParser($pathToFile);
        $ext = $extensionParser->getExtension();
        $class = 'App\\Filesystem\\Drivers\\'.str_replace('.','_',$ext);
        if (class_exists($class)){return new $class($pathToFile);}

        throw new FilesystemError('Driver class '.$class.' not found for extension '.$ext.'. ('.$pathToFile.')' );
    }


}