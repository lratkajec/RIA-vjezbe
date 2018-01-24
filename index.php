<?php

use Phalcon\Mvc\Micro;
use Phalcon\Db\Adapter\Pdo\Mysql;

$app=new Micro();

$connection=new Mysql(
	[
		"host"=>"localhost",
		"username"=>"ratkajec",
		"password"=>"hexis1",
		"dbname"=>"ratkajec",
	]
);

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

/*class QueryBuilder{
	$query=NULL;
	function addSelect($table_name){
		$this->query.="SELECT * FROM ".$table_name;
	}
	function addWhere($str, $value){
		$this->query.=" AND WHERE ".$str." = ";
	}
	function getResults(){
		$resultset=
		return $resultset;
	}
}*/

interface iInputField{
	function getName();
}

class InputField implements iInputField{
	private $name;
	function __construct($name){
		$this->name=$name;
	}
	function getName(){
		return $this->name;
	}
	function __toString(){
		return "<input type='text' name='".$this->name."' id='".$this->name."'>";
	}
}

class InputFieldDecorator{
	private $input;
	function __construct(InputField $input){
		$this->input=$input;
	}
	function __toString(){
		return "<label for='".$this->input->getName()."'>".$this->input->getName()."".$this->input."</label>";
	}
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

$app->get(
	'/decorator',
	function(){
		$input=new InputField('username');
		echo $input;
		echo "<br>";
		$inputDecorator=new InputFieldDecorator($input);
		echo $inputDecorator;
	}
);

$app->handle();

?>
