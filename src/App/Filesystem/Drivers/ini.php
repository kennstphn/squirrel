<?php

namespace App\Filesystem\Drivers;

use App\Filesystem\DriverInterface;
use App\Filesystem\ExtensionParser;
use App\Filesystem\FilesystemError;

class ini implements DriverInterface
{
    function toData()
    {
        return $this->data;
    }

    function getPropertyName()
    {
        return $this->propertyName;
    }

    protected $data, $fullPath,$propertyName;

    /**
     * ini constructor.
     * @param string $fullPath
     * @throws FilesystemError
     */
    public function __construct(string $fullPath)
    {
        $this->fullPath = $fullPath;
        $pieces = explode(DIRECTORY_SEPARATOR,$this->fullPath);
        $this->propertyName =  (new ExtensionParser(array_pop($pieces )))->getNameWithoutExtension();

        try{
            $this->data = (object)parse_ini_file($this->fullPath,true);
        }catch (\Exception $e){
            throw new FilesystemError($e->getMessage(), $e->getCode(), $e);
        }
    }


}