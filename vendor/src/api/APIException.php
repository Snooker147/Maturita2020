<?php

class APIException extends RuntimeException
{

    /** @var string */
    private $info;

    public function __construct(string $msg, string $info = "")
    {
        RuntimeException::__construct($msg);

        $this->$info = $info;
    }

    public function getInfo()
    {
        return $this->info;
    }

}

?>