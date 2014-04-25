<?php
require_once( "sparqlib.php" );  //---> load the SPARQL library

$filename="/var/www/ant5/input/".$argv[1] ;
$filenameSparql="/var/www/ant5/input/".$argv[1]."-sparql" ;
$filenameDb="/var/www/ant5/input/".$argv[1]."-db" ;
$filenameOntology="/var/www/ant5/input/".$argv[1]."-onto" ;
$filenamePerformance="/var/www/ant5/input/".$argv[1]."-performance" ;

$sparql =  file_get_contents($filenameSparql) ;
$database =  file_get_contents($filenameDb) ;
$ontology =  file_get_contents($filenameOntology) ;
$performance =  file_get_contents($filenamePerformance) ;
//--->It connects to the endpoint database and if database is null it should print an error.
$db = sparql_connect( $database );  
if( !$db ) 
  { 
    echo sparql_errno() . ": " . sparql_error(). "\n"; exit; 
  }	
$result2 = sparql_query( $sparql ); 

		//--->If the $result, which is my table of data, is not displayed then a message is printed.
if( !$result2 ) 
  { 
    echo "Please select data to submit!"; exit; 
  } 

$fields = sparql_field_array( $result2 );	 
while( $row = sparql_fetch_array( $result2 ) )
  { 
    $handle = fopen($filename, 'a'); // opens the file to append to it data
    foreach( $fields as $field )
      {	
	fwrite($handle, $row[$field]); //write to the file
				fwrite($handle, "\t");	// tab between columns in the text file
      }
    fwrite($handle, "\n"); //next line in the text file 
  }
fclose($handle);// close the file
		//echo $_POST['thequery'];
		//echo "<br>";
		//echo $_POST['thedatabase'];


$dirName1 = trim($filename, "/var/www/ant5/input/");


$dirName = trim($dirName1, ".txt");
$dirPath = "/var/www/ant5/output/".$dirName ;
$result = mkdir($dirPath, 0755);
if ($result == 1) {
  echo $dirPath . " has been created";
} else {
  echo $dirPath . " has NOT been created";
}

//echo $_POST["ontology"];
			
//Check if performance input is empty or with white spaces or is anything else rathert than a number.

if (empty($_POST["performance"]) || preg_match('/\s/', ($_POST["performance"])) > 0 || !is_numeric($_POST["performance"]))
  {
    $perf = "";
    //echo "The input is wrong";
  }
else
  {
    $perf = "-r ".$_POST["performance"] ;
    //echo $perf;
  }


//$perf = 10000;			
$output = shell_exec('func_wilcoxon -i '.$filename.' -t/home/ant5/newstructures/structures/'.$ontology.' -o'.$dirPath.' -g owl:Thing ' .$perf );
//header('Location: http://jagannath.pdn.cam.ac.uk/ant5/visualiseResults.php');
		


?>
