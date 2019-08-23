<?php
namespace App\Filesystem\Drivers;
use App\Filesystem\DriverInterface;
use App\Filesystem\ExtensionParser;
use App\Filesystem\Structures\Markdown;

use \Hyn\Frontmatter\Parser as MarkdownParser;

class md implements DriverInterface
{
    function toData()
    {
        return $this->data;
    }

    function getPropertyName()
    {
        return $this->propertyName;
    }

    protected $fullPath, $propertyName, $data;
    public function __construct(string $fullPath)
    {
        $this->fullPath = $fullPath;
        $pieces = explode(DIRECTORY_SEPARATOR,$this->fullPath);
        $this->propertyName =  (new ExtensionParser(array_pop($pieces )))->getNameWithoutExtension();

        $parser = new MarkdownParser(new \cebe\markdown\Markdown);
        $parser->setFrontmatter(\Hyn\Frontmatter\Frontmatters\YamlFrontmatter::class);

        $contents = $parser->parse(file_get_contents($this->fullPath));


        $this->data = new Markdown($contents['markdown'],$contents['html'],$contents['meta']);
    }

}