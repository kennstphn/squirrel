<?php

namespace App\Filesystem;

class ExtensionParser
{
    protected $multiDotExtensions = [
        '.tar.gz'
    ];

    protected $extension;
    protected $filename;
    protected $nameWithoutExtension;

    function __construct($filename)
    {
        $this->filename = $filename;

        foreach($this->multiDotExtensions as $extension){
            if( strpos(strrev($filename), strrev($extension) ) === 0 ){
                $this->extension = substr($extension,1);
                $this->nameWithoutExtension = substr($filename,0,-1 * strlen($extension));

                return;
            }
        }

        $pieces = explode('.',$filename);

        $this->extension = array_pop($pieces);
        $this->nameWithoutExtension = implode('.', $pieces);



    }

    /**
     * @return mixed
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return mixed
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return bool|string
     */
    public function getNameWithoutExtension()
    {
        return $this->nameWithoutExtension;
    }

}