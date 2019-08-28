<?php
namespace App\Filesystem\Drivers;


use App\Filesystem\DriverInterface;
use App\Filesystem\ExtensionParser;
use SerginhoLD\Csv\Parser as CsvParser;

class csv implements DriverInterface
{
    function toData()
    {
        $csv = new CsvParser();
        $generator = $csv->parseFile($this->fullPath);
        $lines = [];
        foreach($generator as $line){
            array_push($lines,$line);
        }
        array_walk($lines, function(&$a) use ($lines) {
            $a = array_combine($lines[0], $a);
        });
        array_shift($lines); # remove column header
        return $lines;
    }

    function getPropertyName()
    {
        return (new ExtensionParser(substr($this->fullPath,strrpos($this->fullPath,DIRECTORY_SEPARATOR)+1 )))->getNameWithoutExtension();
    }

    public function __construct(string $fullPath)
    {
        $this->fullPath = $fullPath;
    }


}