
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

    <!-- Bootstrap core CSS -->
    <link href="bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="jumbotron-narrow.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	<script src="http://code.jquery.com/jquery.min.js"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
  </head>

  <body>

    <div class="container">
      <div class="header">
	  <ul class="nav nav-pills pull-right">
          <li><a href="http://jagannath.pdn.cam.ac.uk/ant5/home.php">Home</a></li>
          <li><a href="http://jagannath.pdn.cam.ac.uk/ant5/about.php">About</a></li>
          <li><a href="http://jagannath.pdn.cam.ac.uk/ant5/contact.php">Contact</a></li>
        </ul>
        <h3 class="text-muted">Statistical analysis of RDF Linked data</h3>
      </div>

		<ol class="breadcrumb">
		<li><a href="http://jagannath.pdn.cam.ac.uk/ant5/home.php">Home</a></li>
		<li class="active">Retrieve data via SPARQL query</li>
		</ol>
    <!--****** ALL THE PHP WORK HERE ******* -->
	      	
<?php
// define variables and set to empty values this is used to remember the fields completed
$queryInput = $database = "";

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		if (empty($_POST["queryInput"]))
		{
			$queryInput = "";
		}

		else
		{
			$queryInput = test_input($_POST["queryInput"]);
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
	 
		<form id="queryform" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>#results"> 
		<div class="jumbotron">
            <div class="form-group">
				<label for="database" class="col-sm-2 control-label">Enter Database Endpoint: </label>
				<div class="col-sm-10">
				<input type="text" class="form-control" name ="database" id="database" placeholder="Enter Database Endpoint:">
				</div>
			</div>
			<p>https://www.ebi.ac.uk/rdf/services/atlas/sparql</p>
			<br>
		
			<div class="form-group">
				<label for="queryarea"> Enter SPARQL Query:</label>  
				<textarea class="form-control" id="queryarea" name="queryInput" cols="100" rows="20" style="resize: none;" data-role="none" >
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

LIMIT 2
				</textarea>
			</div>
			<br><br>
			<input class="btn btn-sm btn-info " type="submit" name="submit" value="Show data">
		</div>

		</form>
  

 
	<?php
	//********************** If the button selected is submit ***********************//	
	if(isset($_POST['submit']))
		{
			require_once( "sparqlib.php" );  //---> load the SPARQL library
			$sparql =  $_POST['queryInput'] ;
			//--->It connects to the endpoint database and if database is null it should print an error.
			if (empty($_POST['database'])){
			 echo "Please select database to submit!"; exit;
			}
			$db = sparql_connect( $_POST['database'] );  
			if( !$db ) 
			{ 
				echo sparql_errno() . ": " . sparql_error(). "\n"; exit; 
			}	
			$result = sparql_query( $sparql ); 
			//--->If the $result, which is my table of data, is not displayed then a message is printed.
			if( !$result ) 
			{ 
				echo "Please select data to submit!"; exit; 
			} 
			$fields = sparql_field_array( $result ); 
			echo "<div id=\"results\">";
			echo "<p>Number of Results: ".sparql_num_rows( $result )." results.</p>";
			echo "<table class =\"table table-striped\">";
			echo "<tr>";
			foreach( $fields as $field ) //prints the columns names
			{
				echo "<th>$field</th>";
			}
			echo "</tr>";
			while( $row = sparql_fetch_array( $result ) )
			{ 
				echo "<tr>";
				//prints a row for n results
				foreach( $fields as $field )
				{	
					echo "<td>$row[$field]</td>";
				}	 
				echo "</tr>";
			}
			echo "</table>";
			echo "</div>";
			echo "<br><br>";
?>	
	 
	 <script language="javascript">
	$(document).ready(function(){
		$("#bs-tooltip").tooltip({
		title : 'For a better performance of the analysis you must leave this box blank.If you want a faster performance of the analysis you can fill the box, for example: 100.'
		});
	});

	function submitForms(){
		//document.getElementById("form12").submit();  
	document.getElementById('hiddenquery').value=document.getElementById('queryarea').value;
	document.getElementById('hiddenqueryD').value=document.getElementById('database').value;
	document.getElementById('hiddenqueryP').value=document.getElementById('performance').value;
	document.getElementById('hiddenqueryO').value=document.getElementById('ontology').value;
	document.getElementById('form123').submit();
	}
</script>

		<form id="form12" method="post">		
			<select class="form-control" name="ontology" id="ontology">
<?php
			foreach(glob('../../../home/ant5/newstructures/structures/*', GLOB_ONLYDIR) as $dir) 
			{
				$dir = str_replace('../../../home/ant5/newstructures/structures/', ' ', $dir);
				echo "<option value=".$dir.">".$dir."</option>";			
			}
?>  
			</select>
			<br><br>
		
			<div class="input-group" >
				<span class="input-group-addon">Performance of the test</span>
				<input type="text" name="performance" id="performance" class="form-control">
				<img src="info.png" id="bs-tooltip" data-toggle="tooltip" width="52" height="40">
			</div>
		</form>
	
	   <div class="container marketing">
			<div class="row">
				<div class=col-lg-3>
					<h3>Wilcoxon Test</h3>
					<p>Details about the test.</p>
					<form id="form123" method="post" action="visualiseResults2.php">
					<input type=hidden name="thequery" id="hiddenquery" value=""/>
					<input type=hidden name="thedatabase" id="hiddenqueryD" value=""/>
					<input type=hidden name="theperformance" id="hiddenqueryP" value=""/>
					<input type=hidden name="theonto" id="hiddenqueryO" value=""/>
					<input class="btn btn-sm btn-primary " type="button" onClick="submitForms()" value="Test1"/>
					<input type=hidden name="test1" value="TEST1"> </form>
				</div>
				<div class="col-lg-3">
					<h3>Hyper Test</h3>
					<p>Details about the test.</p>
					<form method="post"><input class="btn btn-sm btn-primary " type="submit" name="test2" value="TEST2"> </form>
				</div>
				<div class="col-lg-3">
					<h3>Binomial Test</h3>
					<p>Details about the test.</p>
					<form method="post"><input class="btn btn-sm btn-primary " type="submit" name="test3" value="TEST3"> </form>
				</div>
				<div class="col-lg-3">
					<h3>2x2contig Test</h3>
					<p>Details about the test.</p>
					<form method="post"><input class="btn btn-sm btn-primary " type="submit" name="test4" value="TEST4"> </form>
				</div>
			</div>
		</div>

<?php } ?>
		
      <div class="footer">
        <p>&copy; Ramona Tapi - ant5 </p>
      </div>

    </div> <!-- /container -->
  </body>
</html>
