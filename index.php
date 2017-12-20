<?php

use Phalcon\Mvc\Micro;
use Phalcon\Db\Adapter\Pdo\Mysql;

$app=new Micro();


class Db{
	public static $db;

	public static function getInstance(){
		if (null===self::$db){
			self::$db=new Db();
			self::$db->mysql=new Mysql(
						[
							"host"=>"localhost",
    							"dbname"=>"ratkajec",
        						"username"=>"ratkajec",
            						"password"=>"hexis1",
						]
						);
		}
		return self::$db;
	}

	public function rawQuery($query){
		return self::$db->mysql->query($query);
	}
}

class ArrayOperations{
	public static $ao;
	public static function getInstance(){
		if(null===self::$ao){
			self::$ao=new ArrayOperations();
		}
		return self::$ao;
	}
	public function getFirst($array){
	}
	public function getLast($array){
	}
}

class HtmlBuilder{
	public $currentHtml="html";
	public $currentHead="head";
	public $currentTitle="title";
	public $currentBody="body";
	public $currentParagraph="p";

	public $page=NULL;
	public $page_html=NULL;
	public $page_head=NULL;
	public $page_title=NULL;
	public $page_body=NULL;
	public $body_contents=NULL;

	function addHtml(){
		$this->page_html="<".$this->currentHtml.">";
	}
	function addHead(){
		$this->page_head="<".$this->currentHead.">";
	}
	function addTitle(){
		$this->page_title="<".$this->currentTitle.">";
	}
	function addBody(){
		$this->page_body="<".$this->currentBody.">";
	}
	function addParagraph($string){
		$this->body_contents.="<".$this->currentParagraph.">";
		$this->body_contents.=$string;
		$this->body_contents.="</".$this->currentParagraph.">";
	}
	function build(){
		echo $this->page_html;
		echo $this->page_head;
		echo $this->page_title;
		echo "</".$this->currentTitle.">";
		echo "</".$this->currentHead.">";
		echo $this->page_body;
		echo $this->body_contents;
		echo "</".$this->currentBody.">";
		echo "</".$this->currentHtml.">";
	}
}

class QueryBuilder{
}

function db(){
	return Db::getInstance();
}

function strategy(){
	return ArrayOperations::getInstance();
}

$app->get(
	'/test',
	function(){
		$tweets=db()->rawQuery("select * from Tweets");


		$html_builder=new HtmlBuilder();
		$html_builder->addBody();
		$html_builder->addParagraph("alalalalalal sfdsf");
		$html_builder->addParagraph("bla bla");
		$html_builder->addHtml();
		$html_builder->addHead();
		$html_builder->addTitle();

		$html_builder->build();
	}
);

$app->handle();

?>
