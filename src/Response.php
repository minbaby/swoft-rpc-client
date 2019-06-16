<?php

namespace Minbaby\SwoftClient;

class Response
{

    /**
     * @var mixed
     */
    private $result;

    /**
     * @var Error|null
     */
    private $error;

    public function __construct($result, $error)
    {
        $this->result = $result;
        $this->error = $error;
    }


    /**
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @return Error
     */
    public function getError(): ?Error
    {
        return $this->error;
    }
}
