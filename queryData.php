
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../assets/ico/favicon.ico">
	

    <title>Statistical Analysis</title>

  
    <link href="bootstrap.min.css" rel="stylesheet">
    <link href="jumbotron-narrow.css" rel="stylesheet">
  </head>

  <body>

    <div class="container">
      <div class="header">
	  <ul class="nav nav-pills pull-right">
          <li><a href="http://jagannath.pdn.cam.ac.uk/ant5/index.html">Home</a></li>
          <li><a href="http://jagannath.pdn.cam.ac.uk/ant5/help.html">Help</a></li>
          <li><a href="http://jagannath.pdn.cam.ac.uk/ant5/contact.php">Contact</a></li>
        </ul>
        <h3 class="text-muted">Statistical analysis of RDF Linked data</h3>
      </div>

		<ol class="breadcrumb">
		<li><a href="http://jagannath.pdn.cam.ac.uk/ant5/index.html">Home</a></li>
		<li class="active">Retrieve data via SPARQL query</li>
		</ol>
    <!--****** ALL THE PHP WORK HERE ******* -->
	      	
<?php
// define variables and set to empty values
$comment = $database = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		if (empty($_POST["comment"]))
		{
			$comment = "";
		}
		else
		{
			$comment = test_input($_POST["comment"]);
		}
	
		if (empty($_POST["database"]))
		{
			$database = "";
		}
		else
		{
			$database = test_input($_POST["database"]);
		}
		
	}


	function test_input($data)
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
}
?>

	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>#results"> 
	
	<div class="jumbotron">
             	
		<h2> Enter Database Endpoint: </h2>
		<textarea name="database" cols="80" rows="1" style="resize: none;" data-role="none">https://www.ebi.ac.uk/rdf/services/atlas/sparql</textarea>
		<br><br>
		
		<h2> Enter SPARQL Query: </h2>
		<textarea name="comment" cols="100" rows="30" style="resize: none;" data-role="none" >
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX owl: <http://www.w3.org/2002/07/owl#>
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX obo: <http://purl.obolibrary.org/obo/>
PREFIX sio: <http://semanticscience.org/resource/>
PREFIX efo: <http://www.ebi.ac.uk/efo/>
PREFIX atlas: <http://rdf.ebi.ac.uk/resource/atlas/>
PREFIX atlasterms: <http://rdf.ebi.ac.uk/terms/atlas/>
PREFIX identifiers: <http://identifiers.org/ensembl/>
PREFIX upc: <http://purl.uniprot.org/core/>
PREFIX goa: <http://bio2rdf.org/goa_vocabulary:>
   
SELECT DISTINCT ?uniprot ?gonum ?pvalue WHERE{ 
?expUri atlasterms:hasAnalysis ?analysis .  
?analysis atlasterms:hasExpressionValue ?value .  
?value atlasterms:pValue ?pvalue . 
?value atlasterms:isMeasurementOf ?probe . 
?probe atlasterms:dbXref ?uniprot .  

BIND (IRI (CONCAT (CONCAT ("http://bio2rdf.org/goa_resource:", REPLACE(STR(?uniprot), "http://purl.uniprot.org/uniprot/", "")),"_1")) as ?newuniprot) . 
SERVICE <http://cu.goa.bio2rdf.org/sparql> { 
?newuniprot goa:go_term ?goid . 
}
BIND (IRI(CONCAT("GO:", REPLACE(str(?goid), "http://bio2rdf.org/go:", ""))) as ?gonum) 
. }

LIMIT 20
			</textarea>
		<br><br>
  
		Results per page : 
		<select name="taskOption" id="taskOption">
			<option value="40" <?php if( isset($_POST['taskOption']) && $_POST['taskOption'] == 40) echo "selected";?>>Write your own limit</option>
			<option value="10" <?php if( isset($_POST['taskOption']) && $_POST['taskOption'] == 10) echo "selected";?>>30</option>
			<option value="20" <?php if( isset($_POST['taskOption']) && $_POST['taskOption'] == 20) echo "selected";?>>50</option>
			<option value="30" <?php if( isset($_POST['taskOption']) && $_POST['taskOption'] == 30) echo "selected";?>>70</option>	
		</select>			
		<br><br>
		
	
      <input class="btn btn-sm btn-info " type="submit" name="submit" value="Show data">
	  </div>

	</form>
  


	<?php
	
	session_start();  //---> used to store the variable for ex: fileName for the page
	
	
	
	//*************************** Results per page ***************************//
		if($_POST['taskOption']=='10')
		{
			$sparql =  ($_POST['comment'] . 'LIMIT 30');
		}
			else if($_POST['taskOption']=='20')
			{
				$sparql =  $_POST['comment'] . 'LIMIT 50';
			}
			else if($_POST['taskOption']=='30')
			{
				$sparql =  $_POST['comment'] . 'LIMIT 70';
			}
			else if($_POST['taskOption']=='40')
			{
				$sparql =  $_POST['comment'] ;
			}
	//************************ End of Results per page ***********************//
		
	
	//*************************** If the button selected is submit ***************************//	
	if(isset($_POST['submit']))
	{
					
		$c = uniqid (rand (),true); //---> generates random unique ids
		$_SESSION['fileName']="/var/www/ant5/input/inputFile" . $c . ".txt";

		require_once( "sparqlib.php" );  //---> load the SPARQL library
		
		//--->It connects to the endpoint database and if database is null it should print an error.
		$db = sparql_connect( $_POST['database'] );  
		if( !$db ) 
		{ 
			echo sparql_errno() . ": " . sparql_error(). "\n"; exit; 
		}	
		$result = sparql_query( $sparql ); 
	
		//--->If the $result, which is my table of data, is not displayed then a message is printed.
		if( !$result ) 
		{ 
			//echo "Please select data to submit!"; exit; 
		} 

		$fields = sparql_field_array( $result );
 
		echo "<div id=\"results\">";
		echo "<p>Number of rows: ".sparql_num_rows( $result )." results.</p>";
		echo "<table class =\"table table-striped\">";
		echo "<tr>";
		
		foreach( $fields as $field ) //prints the columns names
		{
			echo "<th>$field</th>";
		}
		echo "</tr>";
	 
		while( $row = sparql_fetch_array( $result ) )
		{ 
			$handle = fopen($_SESSION['fileName'], 'a'); // opens the file to append to it data
			echo "<tr>";
			//prints a row for n results
			foreach( $fields as $field )
			{	
				echo "<td>$row[$field]</td>";
				fwrite($handle, $row[$field]); //write to the file
				fwrite($handle, "\t");	// tab between columns in the text file
			}
			fwrite($handle, "\n"); //next line in the text file 
			echo "</tr>";
		}
		echo "</table>";
		echo "</div>";
		fclose($handle);// close the file
		echo "<br><br>";
		

	//*************************** Ontology selection ***************************//		


		
	//************************ End of Ontology selection ***********************//	

		
	//************* Four buttons of the test below the SPARQL query ************//
	
	   echo "<div class=\"container marketing\">";
		echo "<div class=\"row\">";
			echo "<div class=\"col-lg-3\">";
				echo "<h3>Wilcoxon Test</h3>";
				echo "<p>Details about the test.</p>";
				echo "<form method=\"post\"><input class=\"btn btn-sm btn-primary \" type=\"submit\" name=\"test1\" value=\"TEST1\"> </form>";
			echo "</div>"; //.col-lg-3 
			echo "<div class=\"col-lg-3\">";
				echo "<h3>Hyper Test</h3>";
				echo "<p>Details about the test.</p>";
				echo "<form method=\"post\"><input class=\"btn btn-sm btn-primary \" type=\"submit\" name=\"test2\" value=\"TEST2\"> </form>";
			echo "</div>";//.col-lg-3 
			echo "<div class=\"col-lg-3\">";
				echo "<h3>Binomial Test</h3>";
				echo "<p>Details about the test.</p>";
				echo "<form method=\"post\"><input class=\"btn btn-sm btn-primary \" type=\"submit\" name=\"test3\" value=\"TEST3\"> </form>";
			echo "</div>"; //.col-lg-3 
			echo "<div class=\"col-lg-3\">";
				echo "<h3>2x2contig Test</h3>";
				echo "<p>Details about the test.</p>";
				echo "<form method=\"post\"><input class=\"btn btn-sm btn-primary \" type=\"submit\" name=\"test4\" value=\"TEST4\"> </form>";
			echo "</div>"; //.col-lg-3
		echo "</div>"; //.row
	  echo"</div>";
	  
	  //************************  End of the test buttons ************************//
	  
		
	}
		
	else if(isset($_POST['test1']))
	{
		$dirName1 = trim($_SESSION['fileName'], "/var/www/ant5/input/");
	  $_SESSION['dirName'] = trim($dirName1, ".txt");
	  $dirPath = "/var/www/ant5/output/".$_SESSION['dirName'] ;
		$result = mkdir($dirPath, 0755);
			if ($result == 1) {
				echo $dirPath . " has been created";
			} else {
			echo $dirPath . " has NOT been created";}
		$output = shell_exec('func_wilcoxon -i '.$_SESSION['fileName'].' -t /home/ant5/newstructures/structures/gene_ontology -o'.$dirPath.' -g owl:Thing -r 100');
		header('Location: http://jagannath.pdn.cam.ac.uk/ant5/visualiseResults.php');
	} 


	?>
<br>
	
	




<!-- ***** THE PHP CODE END HERE ***** -->


      <div class="footer">
        <p>&copy; ant5 2014</p>
      </div>

    </div> <!-- /container -->

  </body>
</html>
