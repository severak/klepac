<?php
namespace severak\backfire;

class backfire extends \Exception
{
    public function __construct($message="", $code=0, $previous=null)
    {
        // TODO - umožnit hlubší backfire
        $backtrace = debug_backtrace();
        parent::__construct($message, $code, $previous);
        $this->file = $backtrace[1]['file'];
        $this->line = $backtrace[1]['line'];
        // TODO - kompletovat metodu
    }
}