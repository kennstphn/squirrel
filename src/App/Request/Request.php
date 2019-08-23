<?php


namespace App\Request;


class Request {
    protected $scheme;
    protected $user;
    protected $pass;
    protected $host;
    protected $port;
    protected $path;
    protected $query;
    protected $fragment;

    protected $post, $server, $cookie, $files, $session;

    protected $lcRequestMethod;


    function __construct(string $uri,$overrides=[])
    {
        $data = (new \League\Uri\Parser())->parse($uri);

        foreach($data as $key =>$val){
            $this->$key = $val;
        }

        $this->post = array_key_exists('post',$overrides) ? $overrides['post'] : $_POST;
        $this->server = array_key_exists('server',$overrides) ? $overrides['server'] : $_SERVER;
        $this->cookie = array_key_exists('cookie',$overrides) ? $overrides['cookie'] : $_COOKIE;
        $this->files = array_key_exists('files',$overrides) ? $overrides['files'] : $_FILES;

        if ( ! array_key_exists('session',$overrides)){
            session_start();
            $this->session = $_SESSION;
            session_write_close();
        }

        $this->lcRequestMethod = trim(strtolower($this->server['REQUEST_METHOD'] ?? 'get'));
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPass()
    {
        return $this->pass;
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function getPost()
    {
        return $this->post;
    }

    public function getServer()
    {
        return $this->server;
    }

    public function getCookie()
    {
        return $this->cookie;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getLcRequestMethod(): string
    {
        return $this->lcRequestMethod;
    }



}