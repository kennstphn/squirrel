<?php


namespace App\Response;


class Response implements \JsonSerializable
{

    protected $responseCode = 200;
    protected $headers=[];
    protected $meta=[];
    protected $data;

    /**
     * @var \Throwable[]
     */
    protected $errors=[];

    /**
     * @return mixed
     */
    public function getResponseCode()
    {
        return $this->errors === [] ?$this->responseCode : $this->errors[0]->getCode();
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function jsonSerialize()
    {
        return count($this->errors) === 0
            ?(object)[
                'code'=>$this->getResponseCode(),
                'headers'=>$this->getHeaders(),
                'data'=>$this->getData(),
                'meta'=>$this->getMeta()
            ]
            :(object)[
                'code'=>$this->getResponseCode(),
                'headers'=>$this->getHeaders(),
                'errors'=>array_map(function(\Throwable$e){return [
                    'code'=>$e->getCode(),
                    'detail'=>$e->getMessage(),
                    'file'=>$e->getFile(),
                    'line'=>$e->getLine(),
                    'trace'=>$e->getTraceAsString()
                ];},$this->getErrors()),
                'meta'=>$this->getMeta()
            ];
    }

    function getTemplate(){
        return array_key_exists('template',$this->meta) ? $this->meta['template'] : null;
    }

    /**
     * @param $data
     * @param int|null $code
     */
    public function setData($data, int $code = null): void
    {
        $this->data = $data;
        $this->responseCode = $code ?? 200;
    }

    function setRedirect($location, $code = 301){
        $this->responseCode = $code;
        array_push($this->headers,'Location: '.$location);
    }

    function addError(\Throwable $e){
        array_push($this->errors, $e);
        $this->responseCode = $e->getCode();
    }

    function setMeta(string $key, $val){
        $this->meta[$key] = $val;
    }




}