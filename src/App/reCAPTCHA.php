<?php


namespace App;


class reCAPTCHA
{
    protected $config;
    function __construct(array $config=null)
    {
        $this->config = (object)($config ?? parse_ini_file(ROOT_DIR.'/config/App.View.View.ini'));
    }

    function getSiteKey(){
        return $this->config->reCAPTCHA_site_key;
    }

    function getSecretKey(){
        return $this->config->reCAPTCHA_secret_key;
    }

    function getHostName(){
        return $this->config->reCAPTCHA_host_name;
    }




}