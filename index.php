<?php

use Phalcon\Mvc\Micro;
use Phalcon\Db\Adapter\Pdo\Mysql;
require "./restclient.php.1";

$rc=new RestClient();

$app=new Micro();

$config = [
    "host"     => "localhost",
    "dbname"   => "ratkajec",
    "username" => "ratkajec",
    "password" => "hexis1",
];

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
	function() use ($connection, $rc){
		$success = $connection->execute(
   			 "INSERT INTO Tweets(Title, Body, Date) VALUES (?, ?, now())",
			 [
				$_POST['title'],
				$_POST['body']
			 ]
		);
		$rc->post("http://makovac.riteh.hexis.hr/api/insert", $_POST);
		$lastID=$connection->lastInsertId();
		echo $lastID;
	}
);

$app->get(
	'/api/printtweet/{ID}',
	function($ID) use ($connection){
		$resultset=$connection->fetchOne(
			"SELECT * FROM Tweets where ID=:id",
			\Phalcon\Db::FETCH_ASSOC,
			[
				"id"=>$ID,
			]
		);
		print_r($resultset);

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

$app->handle();

?>
