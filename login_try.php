<?php

$first_name = $_POST['firstname'];
$email = $_POST['mail'];
$last_name = $_POST['lastname'];
$institute = $_POST['institute'];




if ($first_name == '' || $last_name== '' || $email == '' ) {
    echo json_encode(array("data"=>"empty"));
    die();
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL) === false) {}
else{
     echo json_encode(array("data"=>"invalid"));
  die();
}


$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = 'root';

$conn = mysql_connect($dbhost, $dbuser, $dbpass);
if(! $conn )
{
  die('Could not connect: ' . mysql_error());
}
// echo 'Connected successfully';


$db_selected = mysql_select_db('CUNEIFORM', $conn);
if (!$db_selected) {
    die ('Can\'t use CUNEIFORM : ' . mysql_error());
}

//CREATE NEW USER or IGNORE if it already EXISTS
$record_update=mysql_query("INSERT IGNORE INTO USERS (user_first,user_last, mail, institute) VALUES ('$first_name','$last_name','$email','$institute') ", $conn); //ON DUPLICATE KEY UPDATE user_id=user_id  ON DUPLICATE KEY UPDATE user_first='$first_name', user_last='$last_name', institute='$institute' "
// if(! $record)
//     {die('PROBLEM' . mysql_error());} 

//find current user_id
$usr = mysql_query("SELECT user_id FROM USERS WHERE (USERS.mail='$email') ");
if(! $usr)
    {die('Could not find current user' . mysql_error());} 
$usr_arr=mysql_fetch_array($usr);

//Calculate number of tablets in the database.
$tablets_num = mysql_query("SELECT * FROM TABLETS", $conn);
$num_rows = mysql_num_rows($tablets_num);



$record = mysql_query("SELECT JUNCTION.user_id, JUNCTION.tablet_id FROM JUNCTION WHERE (JUNCTION.user_id = '$usr_arr[0]')", $conn); //' USERS.user_id AND USERS.mail='$email'
if (! $record){

  die ('Can\'t find records in junction: ' . mysql_error());
}

if(mysql_num_rows($record) == 0) {
    //echo "none in the list";
    $sql ="SELECT tablet_id, tablet_name, collection,era FROM TABLETS ORDER BY RAND() LIMIT 1";
    $retval = mysql_query( $sql, $conn );
  	if(! $retval )
  	   {die('Could not retrieve random tablet ' . mysql_error());}
    $load_tablets=mysql_query( "SELECT tablet_name FROM TABLETS", $conn );
    $storeArray = Array();
    while ($row = mysql_fetch_array($load_tablets, MYSQL_ASSOC)) {
        $storeArray[] =  $row["tablet_name"];  }
    $values = mysql_fetch_array($retval);
    $array = array("id"=>"$values[0]", "name"=>"$values[1]", "collection"=>"$values[2]","era"=>"$values[3]","user"=>"$usr_arr[0]",  "tabletarray"=>$storeArray ); //,
    echo json_encode($array);
  }

else if (mysql_num_rows($record)==$num_rows){

     echo json_encode(array("data"=>"none"));
    die();   
}

else {
    //echo "Found list $usr_arr[0]";
    $sql ="SELECT tablet_id, tablet_name, collection,era FROM TABLETS WHERE tablet_id NOT IN (SELECT JUNCTION.tablet_id FROM JUNCTION WHERE (JUNCTION.user_id = '$usr_arr[0]')) ORDER BY RAND() LIMIT 1" ;
  	$retval = mysql_query( $sql, $conn );
  	if(! $retval )
  	   {die('Could not retrieve random tablet ' . mysql_error());}
    $load_tablets=mysql_query( "SELECT tablet_name FROM TABLETS WHERE tablet_id NOT IN (SELECT JUNCTION.tablet_id FROM JUNCTION WHERE (JUNCTION.user_id = '$usr_arr[0]'))", $conn );
    $storeArray = Array();
    while ($row = mysql_fetch_array($load_tablets, MYSQL_ASSOC)) {
        $storeArray[] =  $row["tablet_name"];  }
    $values = mysql_fetch_array($retval);
    $array = array("id"=>"$values[0]", "name"=>"$values[1]", "collection"=>"$values[2]","era"=>"$values[3]","user"=>"$usr_arr[0]", "tabletarray"=>$storeArray ); //
    echo json_encode($array);
	

}



mysql_close($conn);
?>