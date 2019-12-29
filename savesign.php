<?php


$imageData = $_POST['data'];
$anno = $_POST['anno'];
$user = $_POST['user'];
$tablet = $_POST['tablet'];
$comment = $_POST['comment'];
$wedges = $_POST['wedges'];
$radio = $_POST['quality'];
echo $comment ;

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


$entry=mysql_query("INSERT INTO SIGNS (user_id,tablet_id, annotation_index, quality, comment) VALUES ('$user','$tablet','$anno', '$radio', '$comment')", $conn);
// printf ("New Record has id:  %d.\n", mysql_insert_id());
// printf("Last inserted record has id %d\n", mysql_insert_id());
// $id = mysql_insert_id();//$entry->insert_id; //mysqli_insert_id();
// echo "New record created successfully. Last inserted ID is: " . $id; 




if (! $entry){
    die ('Can\'t update Sign column: ' . mysql_error());
}
else{ $id = mysql_insert_id();
    echo "New record created successfully. Last inserted ID is: " . $id; }


// if ($conn->query($sql) === TRUE) {
//     $last_id = $conn->insert_id;
//     echo "New record created successfully. Last inserted ID is: " . $last_id;
// } else {
//     echo "Error: " . $sql . "<br>" . $conn->error;
// }




// $sql = "INSERT INTO MyGuests (firstname, lastname, email)
// VALUES ('John', 'Doe', 'john@example.com')";

// if ($conn->query($sql) === TRUE) {
//     echo "New record created successfully";
// } else {
//     echo "Error: " . $sql . "<br>" . $conn->error;
// }


 // Remove the headers (data:,) part.
 // A real application should use them according to needs such as to check image type
 $filteredData=substr($imageData, strpos($imageData, ",")+1);
 
// Need to decode before saving since the data we received is already base64 encoded
$unencodedData=base64_decode($filteredData);

//echo "unencodedData".$unencodedData;

// Save file. This example uses a hard coded filename for testing,
// but a real application can specify filename in POST variable
$fp = fopen( "data/".$id."_".$anno."_".$user.".png", 'wb' );
//$fp = fopen( 'data/test.jpg', 'wb' );
fwrite( $fp, $unencodedData);
fclose( $fp );

mysql_close($conn);

?>