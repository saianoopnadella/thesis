
<!DOCTYPE html>
<html>
	<head>

		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" href="favicon.ico" type="image/x-icon">
		<link rel = "stylesheet" href = "bootstrap/css/bootstrap.min.css">
		<link rel = "stylesheet" href = "bootstrap/css/bootstrap-theme.min.css">
		<link rel = "stylesheet" href = "bootstrap/css/style.css">

		<script type="text/javascript" src="./java.js"></script>
		<script type="text/JavaScript">

			var auto_refresh = setInterval(
			function ()
			{
			$('#tweet').fadeIn("slow");
			}, 5000); // refresh every 5000 milliseconds

		</script>

		<title>
			Assignment 1
		</title>
	</head>


	<body>


<!-- Navigation panel -->

<div class = "col-md-1" style = "padding: 0; border-right: solid 1px black; height: 1241px;">
	<div class = "container-fluid" style = "margin: 0; padding: 0;">
		<ul class = "nav nav-pills nav-stacked">
			<li role = "presentation" class = "active"><a href = "index.php">Refresh</a></li>
		</ul>
	</div>
</div>
<div class="col-md-11"style = "padding: 0;">
<center>
<h2>Assignment 1 </h2>
</center>
<?php
include "db.php";
   
   $conn = mysql_connect($hostname, $username, $password, $port);
   
   if(! $conn )
   {
      die('Could not connect: ' . mysql_error());
   }
   
   $sql = 'SELECT IP, PORT, COMMUNITY,interfaces,sysname,sysuptime,syscontact,syslocation,sysdescr FROM mrtg_system';
   mysql_select_db($database);
   $result = mysql_query( $sql, $conn );
   
   if(! $result )
   {
      die('Could not get data: ' . mysql_error());
   }
if (($rows = mysql_num_rows($result)) > 0) 
{
    // output data of each row
    while($row = mysql_fetch_array($result, MYSQL_NUM)) 
    {
        $ip=$row[0];
        $port=$row[1];
        $community=$row[2];
        $interface=$row[3];
        $sysuptime=$row[5];
        $syscontact=$row[6];
        $syslocation=$row[7];
        $sysdescr=$row[8];
        $sysname=$row[4];
        $pieces = explode(":", $interface);
	if (empty($interface))					
	{		
	}
	else								
	{
echo "<h3>$ip - $community -$port</h3>";
 	foreach($pieces as $i => $value)
		{ 
		echo "<div class='clearfix' id='picture' style='margin: 20px;'>";
		$z=$pieces[$i];
		create_graphd($ip,$port,$community,$z,"day$ip$port$community-$z.png", "-1d", "Daily graph-$ip-$port-$community-".' '."$z");
 		echo"<form id = \"login\" method = \"POST\" action = \"mrtggraph.php\">";
		echo "<h4>Interface $z --$sysname</h4>";
		echo "<button form = \"login\" type=\"submit\" name = \"login\" class='pull-left' value=\"$ip+$port+$community+$z+$sysuptime+$syscontact+$syslocation+$sysdescr+$sysname\"><img src='day$ip$port$community-$z.png' height='180' width='500' alt='Generated RRD image' class='pull-left'></button>";
echo"</form>"	;	
echo "</div>";						
}
}
}
}
else {
    echo "0 results";
}
	
function create_graphd($ip,$port,$community,$f,$output, $start, $title) {

$file = "$ip\:$port\:$community.rrd";
  $options = array(
    "--slope-mode",
    "--start", $start,
    "--title=$title",
    "--x-grid",
					"HOUR:1:HOUR:2:HOUR:2:0:%H",
					"--units=si", 
					"--grid-dash", "1:3", "--alt-autoscale-max","--lower-limit","0",
					"--vertical-label=Bytes per Second",
					"DEF:inBytes=" . $file . ":bytesIn" . $f . ":AVERAGE",
					"DEF:outBytes=" . $file . ":bytesOut" . $f . ":AVERAGE",
					"VDEF:avg_in=inBytes,AVERAGE",
					"VDEF:avg_out=outBytes,AVERAGE",
					"VDEF:last_in=inBytes,LAST",
					"VDEF:last_out=outBytes,LAST",
					"VDEF:max_in=inBytes,MAXIMUM",
					"VDEF:max_out=outBytes,MAXIMUM",
				//	"CDEF:base_line=outBytes, 0, UN, outBytes, IF",
					"CDEF:incdef=inBytes,8,*",
					"CDEF:outcdef=outBytes,8,*",
					"COMMENT: \\n",
					"COMMENT: \\t",
					"COMMENT: \\tMAXIMUM\\t",
					"COMMENT:  AVERAGE\\t",
					"COMMENT:  LAST\\n",
					"AREA:inBytes#00FF00:In traffic\\t",
					"GPRINT:max_in: %6.2lf %sBps\\t",
					"GPRINT:avg_in: %6.2lf %sBps\\t",
					"GPRINT:last_in: %6.2lf %sBps\\n",
					"LINE1:outBytes#0000FF:Out traffic\\t",
					"GPRINT:max_out: %6.2lf %sBps\\t",
					"GPRINT:avg_out: %6.2lf %sBps\\t",
					"GPRINT:last_out: %6.2lf %sBps\\n",
    
  );

  $ret = rrd_graph($output, $options);
  if (! $ret) {
    echo "<b>Graph error: </b>"."\n".rrd_error()."\n";
  }
}

mysql_close($conn);
?> 
</div>
</body>
</html>
