<?php
//echo $_POST['variable'];
$user = $_POST['user'];
$tablet = $_POST['tablet'];

//Connect to SERVER/MYSQL

$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'root';
$conn = mysql_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
  die('Could not connect: ' . mysql_error());
}
// echo 'Connected successfully';

//Connect to DATABASE CUNEIFORM
$db_selected = mysql_select_db('CUNEIFORM', $conn);
if (!$db_selected) {
    die ('Can\'t use CUNEIFORM : ' . mysql_error());
}


//Calculate number of tablets in the database.
$tablets_num = mysql_query("SELECT * FROM TABLETS", $conn);
$num_rows = mysql_num_rows($tablets_num);

// echo '<br> connected to cuneiform db';
$used=mysql_query("INSERT IGNORE INTO JUNCTION (user_id,tablet_id) VALUES ('$user','$tablet')", $conn);
if (! $used){
    die ('Can\'t update junction: ' . mysql_error());
}

$record = mysql_query("SELECT user_id, tablet_id FROM JUNCTION WHERE (user_id = '$user')", $conn);
if (! $record){

  die ('Can\'t find records in junction: ' . mysql_error());
}
if(mysql_num_rows($record) == 0) {
    //echo "none in the list";
    $sql ="SELECT tablet_id, tablet_name, collection,era FROM TABLETS ORDER BY RAND() LIMIT 1";
    $retval = mysql_query( $sql, $conn );
	if(! $retval )
		{die('Could not retrieve random tablet ' . mysql_error());}
	$values = mysql_fetch_array($retval);
	$array = array("id"=>"$values[0]", "name"=>"$values[1]", "collection"=>"$values[2]","era"=>"$values[3]"); //
	echo json_encode($array);
} 

else if (mysql_num_rows($record)==$num_rows){
     //echo "all";
     echo json_encode(array("data"=>"none"));
    	die();   
}

else {
    //echo "Found in the list";
    $sql ="SELECT tablet_id, tablet_name, collection,era FROM TABLETS WHERE tablet_id NOT IN (SELECT JUNCTION.tablet_id FROM JUNCTION WHERE (JUNCTION.user_id = '$user')) ORDER BY RAND() LIMIT 1" ;
	$retval = mysql_query( $sql, $conn );
	if(! $retval )
		{die('Could not retrieve random tablet ' . mysql_error());}
	$values = mysql_fetch_array($retval);
	$array = array("id"=>"$values[0]", "name"=>"$values[1]", "collection"=>"$values[2]","era"=>"$values[3]"); //
	echo json_encode($array);

}



// $sql ="SELECT tablet_id, tablet_name, collection,era FROM tablets WHERE NOT (currently_used=1) ORDER BY RAND() LIMIT 1" ;

// $retval = mysql_query( $sql, $conn );
// if(! $retval )
// {
//   die('Could not retrieve random tablet ' . mysql_error());
// }
// // echo "<br> Random tablet retrieved";

// $values = mysql_fetch_array($retval);


// $unset_current ="UPDATE tablets SET currently_used=0 WHERE tablet_name='$variable'";
// $unset_result = mysql_query( $unset_current, $conn );
// if(! $unset_result  )
// {
//   die('Could not change to not currently_used' . mysql_error());
// }

// //var_dump($values);

// $set_current ="UPDATE tablets SET currently_used=1 WHERE tablet_id=$values[0]";
// $result = mysql_query( $set_current, $conn );
// if(! $result )
// {
//   die('Could not change to currently_used' . mysql_error());
// }
// //echo "<br>index currently_used changed";
// // $id=$values[0];
// // $name=$values[1];
// // $collection=$values[2];
// // $era=$values[3];
// // $files = glob('images/*.jpg');


// $array = array("id"=>"$values[0]", "name"=>"$values[1]", "collection"=>"$values[2]","era"=>"$values[3]");

// //echo "<br>$values";
// //$myJSONString = 
// echo json_encode($array);
// //echo "<br>$myJSONString";

mysql_close($conn);
?>