<?php
require_once __DIR__ . '/vendor/autoload.php';
use Workerman\Worker;

$ws_worker = new Worker("websocket://0.0.0.0:4236");
$ws_worker->count = 4;
$ws_worker->onConnect = function($connection) {
	echo "New connection\n";
	echo "Connection id: ".$connection->id.PHP_EOL;
};
$ws_worker->onMessage = function($connection, $data) use ($ws_worker){
	$firstChar = substr($data, 0, 1);
	if ($data=='give me all messages'){
		//init chat
		$res = DBConnect()->query("SELECT * FROM chat ORDER BY Time DESC")->fetch_all(MYSQLI_ASSOC);
		$connection->send(json_encode($res));
	} elseif ( $firstChar =='{' ) {
		$servData = json_decode($data);
		$query = "INSERT INTO chat (`id`, `userID`, `Msg`, `Name`, `Time`, `isEnc`) VALUES (NULL, ".$servData->id.", '".$servData->Msg."', '".$servData->Name."', '".$servData->Time."', '".$servData->isEnc."')";
		DBConnect()->query($query);
		// broadcast
		$res = DBConnect()->query("SELECT * FROM chat ORDER BY Time DESC")->fetch_all(MYSQLI_ASSOC);
		foreach ($ws_worker->connections as $con) {
			$con->send(json_encode($res));
		}
	} elseif ( preg_replace('/[^0-9]/', '', $firstChar) ){
		$formatData = explode("~", $data);
		$msgId = $formatData[0];
		$txt = $formatData[1];
		if ($formatData[2]) {
			if ($formatData[2]=='Y'){
				$isNec = '1';
			} else {
				$isNec = '0';
			}
			$query = "UPDATE chat SET Msg='".$txt."', isEnc='".$isNec."' WHERE id=".$msgId;
		} else {
			$query = "UPDATE chat SET Msg='".$txt."' WHERE id=".$msgId;
		}
		DBConnect()->query($query);
		//broadcast
		$res = DBConnect()->query("SELECT * FROM chat ORDER BY Time DESC")->fetch_all(MYSQLI_ASSOC);
		foreach ($ws_worker->connections as $con) {
			$con->send(json_encode($res));
		}
	} elseif ($firstChar=='d'){
		$formatData = explode("~", $data);
		$msgId = $formatData[1];
		$query = "DELETE FROM chat WHERE id=".$msgId;
		DBConnect()->query($query);
		//broadcast
		$res = DBConnect()->query("SELECT * FROM chat ORDER BY Time DESC")->fetch_all(MYSQLI_ASSOC);
		foreach ($ws_worker->connections as $con) {
			$con->send(json_encode($res));
		}
	}
};
$ws_worker->onClose = function($connection){
	echo "Connection closed\n";
};
Worker::runAll();
function DBConnect(){
	$mysql = @new mysqli('localhost', 'root', 'Demon101', 'homestead');
	return $mysql;
}