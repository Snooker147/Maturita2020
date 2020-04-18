<?php

class APICustomResponse
{
    
    private $returnData;

    public function __construct($res)
    {
        $this->returnData = $res;
    }

    public function getReturnData()
    {
        return $this->returnData;
    }

}