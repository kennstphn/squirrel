<?php


namespace App\View;

use App\Filesystem\Data;
use App\Response\Response;
use Twig\Environment;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use Twig\TwigFilter;

class View
{
    const TEMPLATE_DIR=ROOT_DIR.'/templates';

    protected $twigLoaders=[];

    /**
     * @var Response
     */
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
        $this->initRootLoader();
    }

    public function addTwigLoader(LoaderInterface $loader){
        array_push($this->twigLoaders,$loader);
    }

    public function __toString()
    {
        try{
            $template = $this->response->getTemplate();

            if ( $template){
                $environment =new Environment(new ChainLoader($this->twigLoaders));
                $this->addStuff($environment);
                return $this->runShortcodes($environment->load($template)->render([
                    'response'=>$this->response,
                    'data'=>new Data(ROOT_DIR.'/data')
                ]));
            }

            return json_encode($this->response,JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES);
        }catch (\Throwable $e){
            return json_encode([
                'errors'=>[
                    'code'=>$e->getCode(),
                    'detail'=>$e->getMessage(),
                    'file'=>$e->getFile(),
                    'line'=>$e->getLine(),
                    'trace'=>$e->getTraceAsString()
                ]
            ],JSON_UNESCAPED_SLASHES+JSON_PRETTY_PRINT);
        }

    }

    protected function initRootLoader(){
        $loader = new FilesystemLoader(self::TEMPLATE_DIR.'/default');
        foreach(scandir(self::TEMPLATE_DIR) as $file){
            if(in_array($file,['default','..','.'])){continue;}

            $loader->addPath(self::TEMPLATE_DIR.DIRECTORY_SEPARATOR.$file,$file);

        }
        $this->addTwigLoader($loader);
    }

    protected function addStuff(Environment $environment){
        //i.e. globals, functions, filters

        $environment->addFilter(new TwigFilter('markdown',function ($string){
            if (
                ! is_string($string)
                && ( is_object($string) && ! is_callable([$string,'__toString']))
            ){return gettype($string).' not a string';}


            return (new \HTMLPurifier())->purify(
                str_replace('  ','<br/>',(new \Parsedown())->parse($string))
            );
        }));

        $ini = parse_ini_file(ROOT_DIR.'/config/App.View.View.ini',true);
        if($ini){
            foreach($ini as $key => $val){
                $environment->addGlobal($key, $val);
            }
        }
    }

    /**
     * @param $string
     * @return string;
     */
    protected function runShortcodes($string){
        return str_replace(['[break]','[br]'],'<br />',$string);
    }

}