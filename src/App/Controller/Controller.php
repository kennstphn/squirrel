<?php


namespace App\Controller;


use App\Filesystem\Data;
use App\Request\Request;
use App\Response\Response;

class Controller
{

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    function __invoke(Response $response){


        // store request into local variable for func
        $request = $this->request;

        //figure out whether we're using routes or handlers
        switch ($request->getLcRequestMethod()){
            case 'get':
                $prefix = 'App.Controller.Routes.';
                break;
            default:
                $prefix = 'App.Controller.Handlers.';
                break;
        }

        // mush up the path and prefix.
        $full = trim(str_replace(['..','//','/'],'.',$prefix.$request->getPath()),'.');
        $pieces = explode('.',$full);
        $pieces = array_filter($pieces,function($p){return $p !== '';});
        $arguments = [];
        $first = true;
        while(count($pieces) > 3){

            //after first time, pop off the end of the array
            if ( ! $first){
                array_push($arguments,array_pop($pieces));
            } else {$first = false;}

            $class = implode('\\',$pieces);


            //found a controller class
            if(class_exists($class)){
                $controller = new $class($arguments,$request);
                $controller->__invoke($response);
                return; //done! exit early and let the controller have responsibility.
            }

        }

        // use the default controller, which for this app is the Data chain from ROOT_DIR./data on down
        $data = new Data();
        $pieces = explode('.',trim(str_replace(['..','//','/'],'.',$request->getPath() ),'.'));
        $pieces = array_filter($pieces,function($p){return $p !== '';});
        $done = true;
        foreach($pieces as $piece){
            if(is_null($data) || ! is_object($data) || ! isset($data->$piece)){$done = false;break;}
            $data = $data->$piece;
            $done = true;
        }


        // check for redirects, again - exit early
        if( ! $done){
            $path = $request->getPath();
            $freshData =(new Data());
            $redirect = $freshData->redirects ? $freshData->redirects->$path : null;
            if($redirect){
                $response->setRedirect($redirect);
                return;
            }
        }


        if($done){ // still have data chain active
            $response->setMeta('path',$request->getPath());
            $response->setMeta('template',$data->template);
            $response->setData($data);
        }else{ //broke data chain somewhere
            $response->addError(new \Exception('Route not found',404));
        }
    }


}