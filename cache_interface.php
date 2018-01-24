<?php

interface cacheTemplate{
	public function setCache($name, $num);
	public function getCache($name);
	public function deleteCache($name);
}

class apc implements cacheTemplate{

	public function setCache($name, $num){
		apc_store($name, $num);
	}

	public function getCache($name){
		return apc_fetch($name);
	}

	public function deleteCache($name){
		apc_delete($name);
	}
}


$x=new apc();
$x->setCache("test", 2);
print "cache je:".$x->getCache("test");

?>
