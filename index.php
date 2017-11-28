<?php

use Phalcon\Mvc\Micro;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Cache\Backend\Memcache;
use Phalcon\Cache\Frontend\Data as FrontData;
require "./restclient.php";

$rc=new RestClient();

$app=new Micro();

$config = [
    "host"     => "localhost",
    "dbname"   => "ratkajec",
    "username" => "ratkajec",
    "password" => "hexis1",
];

$frontCache=new FrontData(
	[
		"lifetime" => 172800,
	]
);

$cache=new Memcache(
	$frontCache,
	[
		"host" => "localhost",
		"persistent" => false,
	]
);

$connection=new Mysql($config);

$app->get(
	'/api/tweets',
	function(){
		echo "<form action='/api/insert' method='POST'>";
		echo "<input type='text' name='title'><br>";
		echo "<input type='text' name='body'><br>";
		echo "<input type='submit' value='Submit'>";
		echo "</form>";
	}
);

$app->post(
	'/api/insert',
	function() use ($connection, $rc, $cache){
		$success = $connection->execute(
   			 "INSERT INTO Tweets(Title, Body, Date) VALUES (?, ?, now())",
			 [
				$_POST['title'],
				$_POST['body']
			 ]
		);
		//$cache->save($connection->lastInsertId(), $_POST);
		//$rc->post("http://makovac.riteh.hexis.hr/api/insert", $_POST);
		$lastID=$connection->lastInsertId();
		echo $lastID;
	}
);

$app->get(
	'/api/printtweet/{ID}',
	function($ID) use ($connection, $cache){
		$resultset=$connection->fetchOne(
			"SELECT * FROM Tweets where ID=:id",
			\Phalcon\Db::FETCH_ASSOC,
			[
				"id"=>$ID,
			]
		);
		print_r($resultset);
		//$data=$cache->get($ID);
		//echo $data;

		echo "<form action='/api/update/".$ID."' method='POST'>";
		echo '<input type="text" name="title" value="'.$resultset['Title'].'"><br>';
		echo "<input type='text' name='body' value='".$resultset['Body']."'><br>";
		echo "<input type='submit' value='Submit'>";
		echo "</form>";
	}
);

$app->post(
	'/api/update/{ID}',
	function($ID) use ($connection){
		$success=$connection->updateAsDict(
			"Tweets",
			[
				"Title" => $_POST['title'],
				"Body" => $_POST['body'],
			],
			"ID = ".$ID
		);
	}
);

$app->get(
	'/api/printall',
	function() use ($connection){
		$resultset=$connection->fetchAll(
			"SELECT ID, Title FROM Tweets",
			\Phalcon\Db::FETCH_ASSOC
		);
		echo json_encode($resultset);
	}
);

$app->get(
	'/api/details/{ID}',
	function($ID) use ($connection){
		$resultset=$connection->fetchOne(
			"SELECT * FROM Tweets where ID=:id",
			\Phalcon\Db::FETCH_ASSOC,
			[
				"id"=>$ID,
			]
		);
		echo json_encode($resultset);
	}
);

$app->get(
	'/api/gettweets',
	function() use ($rc, $connection){
		$domains=array("negulic", "makovac");
		$tweets=$rc->get("http://ratkajec.riteh.hexis.hr/api/printall");
		$myTweets=json_decode($tweets->response, true);
		print_r($myTweets);
		echo "<br><br>";

		$x=array();
		foreach($domains as $domain){
			$res=$rc->get("http://".$domain.".riteh.hexis.hr/api/printall");
			$x[$domain]=json_decode($res->response, true);
		}
		print_r($x);
	}
);

$app->handle();

?>
