<?php


namespace App\Controller\Handlers;


use App\Controller\ControllerInterface;
use App\reCAPTCHA;
use App\Request\Request;
use App\Response\Response;

class Api implements ControllerInterface
{
    protected $request, $arguments;

    public function __construct(array $arguments, Request $request)
    {
        $this->request = $request;
        $this->arguments = $arguments;

    }

    function __invoke(Response $response)
    {

        echo '<pre>';
        $storage = new reCAPTCHA();
        $recaptcha = new \ReCaptcha\ReCaptcha($storage->getSecretKey() );
        $resp = $recaptcha->setExpectedHostname($storage->getHostName())
            ->setExpectedAction($this->request->getServer()['HTTP_REFERER'])
            ->setScoreThreshold(0.5)
            ->verify($this->request->getPost()['g-recaptcha-response'], $this->request->getServer()['REMOTE_ADDR']);

        if ($resp->isSuccess()) {
            var_dump('true');
        } else {
            $errors = $resp->getErrorCodes();
            var_dump($errors);
        }

        exit;

        $post = (object)$this->request->getPost();
        if ( ! isset($post->type)){
            throw new \Exception('Unidentified Post.type');
        }

    }

}