<?php


namespace App\Filesystem;


use Doctrine\Common\Collections\ArrayCollection;
use Traversable;

class Data implements \IteratorAggregate, \JsonSerializable
{

    protected $pathToFile;

    protected $childPaths;
    protected $properties;

    /**
     * @var
     */
    protected $fileName;


    /**
     * FilesystemObject constructor.
     * @param string $pathToFile
     * @throws FilesystemError
     */
    function __construct(string $pathToFile = null)
    {
        $pathToFile = $pathToFile ?? ROOT_DIR.'/data';
        $this->pathToFile = $pathToFile;
        $this->properties = new ArrayCollection();
        $this->childPaths = new ArrayCollection();
        $pieces = explode(DIRECTORY_SEPARATOR,$pathToFile);
        $this->fileName = array_pop($pieces);

        // validate
        if ( ! file_exists($pathToFile)){throw new FilesystemError('Directory does not exist: '.$pathToFile);}
        if ( ! is_dir($pathToFile)){throw new FilesystemError('Files are properties of Filesystem Objects. ('.$pathToFile.')');}

        // scan all children
        $children = array_filter(scandir($pathToFile),function($f){return $f !== '.' && $f !=='..';});

        // parse out child directories (as ->children objects)
        // parse files as properties
        foreach($children as $child){
            switch (is_dir($pathToFile.DIRECTORY_SEPARATOR.$child)){
                case true:
                    $this->childPaths->add($pathToFile.DIRECTORY_SEPARATOR.$child);
                    break;
                default:
                    $driver = DriverFactory::getDriver($pathToFile.DIRECTORY_SEPARATOR.$child);
                    $this->properties->set($driver->getPropertyName(), $driver->toData());
                    break;
            }
        }

    }

    public function getIterator()
    {
        return (
            new ArrayCollection(
                array_merge(
                    $this->properties->toArray(),
                    $this->getChildren()->toArray())
            )
        )->getIterator();
    }

    /**
     * @return ArrayCollection|$this[]
     */
    public function getChildren(){
        return $this->childPaths->map(function($d){return new self($d);});
    }

    function getName(){
        return $this->fileName; // no extension, because this is a directory.
    }

    function __isset($name)
    {
        // first priority given to a subdirectory named "name"
        foreach($this->getChildren() as $child){
            if($child->getName() === $name){return true;}
        }

        // second Priority for file called "name.extension". No built in sorting between those file extensions
        if($this->properties->containsKey($name)){return true;}

        // third priority given to file called "_properties.extension" housing an object with property of "name"
        if($this->properties->containsKey('_properties') && isset( ( (object)$this->properties->get('_properties'))->$name ) ) {
            return true;
        }
    }

    function __get($name)
    {
        /*
         * Handling for $this->$name
         */

        // first priority given to a subdirectory named "name"
        foreach($this->getChildren() as $child){
            if($child->getName() === $name){return $child;}
        }

        // second Priority for file called "name.extension". No built in sorting between those file extensions
        if($this->properties->containsKey($name)){return $this->properties->get($name);}

        // third priority given to file called "_properties.extension" housing an object with property of "name"
        if($this->properties->containsKey('_properties') && isset( ( (object)$this->properties->get('_properties'))->$name ) ) {
            return ( (object) $this->properties->get('_properties'))->$name;
        }

        // can't find it -- return null
        return null;
    }

    public function jsonSerialize()
    {
        $props = $this->properties->toArray();
        foreach($this->childPaths as $path){
            $path = str_replace($this->pathToFile.DIRECTORY_SEPARATOR, '',$path);
            $props[$path] = Data::class;
        }
        return $props;
    }

    function getProperties(){
        return $this->properties;
    }


}