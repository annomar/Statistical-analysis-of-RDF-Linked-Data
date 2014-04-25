<?php
session_start();
//echo $_POST["thequery"];
?>

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
	
	<link rel="stylesheet" href="javascript/jq.css" type="text/css" media="print, projection, screen" />
	<link rel="stylesheet" href="javascript/style.css" type="text/css" id="" media="print, projection, screen" />
	<script type="text/javascript" src="javascript/jquery-latest.js"></script>
	<script type="text/javascript" src="javascript/jquery.tablesorter.js"></script>
	<script type="text/javascript" src="http://tablesorter.com/docs/js/chili/chili-1.8b.js"></script>

 
    <link href="bootstrap.min.css" rel="stylesheet">
    <link href="jumbotron-narrow.css" rel="stylesheet">

	
	     <script type="text/javascript" id="js">
		 
		$(document).ready(function() { 
    $("table").tablesorter(); 
    
            // set sorting column and direction, this will sort on the first and third column 
            var sorting = [[0,0],[0,0]]; 
            // sort on the first column 
            $("table").trigger("sorton",[sorting]); 
    
});

</script>

	
	
  </head>

  <body>

    <div class="container">
      <div class="header">
		 <ul class="nav nav-pills pull-right">
          <li><a href="http://jagannath.pdn.cam.ac.uk/ant5/index.html">Home</a></li>
          <li><a href="http://jagannath.pdn.cam.ac.uk/ant5/help.html">Help</a></li>
		  <li><a href="http://jagannath.pdn.cam.ac.uk/ant5/about.html">About</a></li>
          <li><a href="http://jagannath.pdn.cam.ac.uk/ant5/contact.php">Contact</a></li>
        </ul>
        <h3 class="text-muted">Statistical analysis of RDF Linked data</h3>
      </div>
<ol class="breadcrumb">
		<li><a href="http://jagannath.pdn.cam.ac.uk/ant5/home.php">Home</a></li>
		<li><a href="http://jagannath.pdn.cam.ac.uk/ant5/indextest.php">Retrieve data via SPARQL query</a></li>
		<li class="active">Visualisation of the results</li>
		</ol>
		
		<?php
	$content = file('/var/www/ant5/output/'.$_GET['userid'].'/statistics.txt');
	//First line: $content[0];
	 $root_node = ltrim($content[7], "Root-Nodes:"); 
	 $cutoff = ltrim($content[9], "Cutoff:"); 
	 $annotations_a = ltrim($content[17], "Genes annotated in GO:"); 
	 $annotations_b = ltrim($content[18], "Genes ann. in owl:Thing:"); 
	 
	 $low_ranks = $content[39434];
	 $parts = explode("\t",$low_ranks);	
	 $low_ranks = $parts[0];
	 $high_ranks = $parts[1];
	?>
	
	<table id="myTable2" class="table table-striped tablesorter"> 
	<tr><th colspan=2>Overview</th></tr>
		<tr><td>Test:</td><td>wilcoxon</td></tr>
		<tr><td>Root node:</td><td><?php echo $root_node;?></td></tr>
		<tr><td>Cutoff: </td><td><?php echo $cutoff;?></td></tr>
		<tr><td>Genes annotated in GO: </td><td><?php echo $annotations_a;?></td></tr>
		<tr><td>Genes annotated in owl:Thing: </td><td><?php echo $annotations_b;?></td></tr>
		<tr><td>Global test statistics for low ranks (p-value): </td><td><?php echo $low_ranks;?></td></tr>
		<tr><td>Global test statistics for high ranks (p-value): </td><td><?php echo $high_ranks;?></td></tr>
	</table>
	
		</div>
 	<br><br>

	<div id="main">
	<div id="demo">
	
	<table  class="table table-striped tablesorter">
		
<?php



echo "the path is: ".$_SESSION['dirName'];

$row = 1;
if (($handle = fopen("/var/www/ant5/output/".$_GET['userid']."/groups.txt", "r")) !== FALSE) {
    
    while (($data = fgetcsv($handle, 1000, "\t")) !== FALSE) {
		
        $num = count($data);
        if ($row == 1) {
            $num = $num + 1 ;
            echo "<thead><tr>\n";
        }else{
            echo "<tr>\n";
        }
		
        for ($c=1; $c < $num-1 ; $c++) {
            //echo $data[$c] . "<br />\n";
            if($data[$c] == null ) {
               $value = "";
			   
			   
            }else{
               $value = $data[$c];
            }
	    if( $value != "tab" ){
                    if ($row == 1) {
				if ($value == 'node_name')
					{
						echo '<th> Node Name </th>';
						
					}
				else if ($value == 'node_id')
					{
						echo '<th> Node ID </th>';
					}
				else if ($value == '#genes_outside_node')
					{
						echo '<th> Genes outside Node </th>';
					}
				else if ($value == '#genes_in_node')
					{
						echo '<th> Genes in Node </th>';
					}
				else if ($value == 'sum_of_ranks_in_node')
					{
						echo '<th> Sum of Ranks </th>';
					}
				else if ($value == 'raw_p_low_ranks')
					{
						echo '<th> Low P-value </th>';
					}
				else if ($value == 'raw_p_high_ranks')
					{
						echo '<th> High P-value </th>';
					}
				else if ($value == 'FWER_low')
					{
						echo '<th> FWER low </th>';
					}
				else if ($value == 'FWER_high')
					{
						echo '<th> FWER high </th>';
					}
				else if ($value == 'FDR_low')
					{
						echo '<th> FDR low </th>';
					}
				else if ($value == 'FDR_high')
					{
						echo '<th> FDR high </th>';
					}
				else
					{
					echo '<th>'.$value.'</th>';
					}
				
            }else{
                echo "\n<td>". $value."</td>\n";
				
            }}
			
        }
        
        if ($row == 1) {
            echo "</tr>\n</thead><tbody>\n";
        }else{
            echo "</tr>\n";
        }
        $row++;
		
		 
    }
    echo '</tbody>';
   
	
    fclose($handle);
}


?>
</table>
	</div> 
	</div> 
<br>

 <div class="container">
    
      <div class="footer">
        <p>&copy; ant5 2014</p>
      </div>
	  
	  </div>

  

  </body>
</html>


