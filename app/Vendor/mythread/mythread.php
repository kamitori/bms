<?php
class mythread extends Thread{
	public function __construct($callBack) {
        $this->callBack = $callBack($this);
    }
	public function run() {
       $this->callBack;
    }
}