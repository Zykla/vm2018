<?php 
session_start();
/*
	Spara tips.
	Author: Mattias Melin
	Changed: 2008-03-23
*/
// Include function collection
include("functions.php");

    print "<html>\n";
    print "\t<head>\n";
    print "\t\t<title>".$_SESSION['titleText']."</title>\n";
    print "\t</head>\n";
    print "\t<body BGCOLOR=\"".$_SESSION['bgColor']."\" background=\"".$_SESSION['backgroundImage']."\">\n";
	/*
		Fetch tippar id.
	 */
	$fields = "*";
	$frompart = $_SESSION['tablePrefix']."tippare";
	$wherepart = "namn='".$_SESSION['gameloggedin']."'";
	$query = "select $fields from $frompart where $wherepart";
    if ($resultTippare = mysqli_query($_SESSION['link'], $query)) {
		if ($lineTippare = mysqli_fetch_array($resultTippare, MYSQLI_ASSOC))
		{
			$tippare = $lineTippare["id"];
			$emailadress = $lineTippare["emailadress"];
			$betalat = $lineTippare["betalat"];
		}
		else
		{
			die("<b>Du måste logga in först!</b>");
		}
		
	}

$headerrow = "<tr><th>Date</th><th>Time</th><th>Gr</th><th>Home</th><th>Away</th><th>Arena</th><th>Tip</th><th>Result</th><th>Points</th><tr>";
$fields = "id, DATE_FORMAT(matchdatum, \"%e/%c\"), TIME_FORMAT(tvtid,\"%H:%i\"), grupp, hemmalag, bortalag, matchstad";
$frompart = $_SESSION['tablePrefix']."match";
$orderby = "matchdatum,tvtid,id";
$query = "SELECT ".$fields." FROM ".$frompart." order by ".$orderby;
if ($debug)
{
    print "SQL: ".$query."<br>";
}
if ($result = mysqli_query($_SESSION['link'], $query)) {
	// Printing results in HTML
	$body = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#85BBE9\">\n";
	$body .= $headerrow."\n";
	$poang = 0;
	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
	{
		$body .=  "\t<tr>\n";
		$index = 0;
		foreach ($line as $col_value) 
		{
		    if ($index > 0) {
			    if ($col_value != "")
    			{
	    			$body .=  "\t\t<td valign=\"top\">$col_value</td>\n";
		    	}
			    else
			    {
			    	$body .=  "\t\t<td>&nbsp;</td>\n";
			    }
		    }
		    $index++;
		}
		$hemmamal_var = "hemmamal".$line["id"];
		$bortamal_var = "bortamal".$line["id"];

		/*
			Fetch previous tip.
		 */
		if ($tippare != "")
		{
			$fields = "hemmamal,bortamal";
			$frompart = $_SESSION['tablePrefix']."matchtips";
			$wherepart = "tippare = $tippare and matchid=".$line["id"];
			$orderby = "matchid";
			$query = "select $fields from $frompart where $wherepart order by $orderby";
			if ($result2 = mysqli_query($_SESSION['link'], $query)) {
				if ($line2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
				{
					$dbhemmamal = $line2["hemmamal"];
					$dbbortamal = $line2["bortamal"];
				}
				else
				{
					$dbhemmamal = "";
					$dbbortamal = "";
				}
			}
		}	
		$updatesql = "";
			
		$$hemmamal_var = $dbhemmamal;
		$$bortamal_var = $dbbortamal;

		$body .=  "\t\t<td align=\"center\">".$$hemmamal_var." - ".$$bortamal_var."</td>\n";

	// Fetch result
		$fields = "hemmamal,bortamal";
		$frompart = $_SESSION['tablePrefix']."match";
		$where = "id=".$line["id"];
		$query = "SELECT ".$fields." FROM ".$frompart." where ".$where;
		if ($debug)
		{
			print "SQL: ".$query."<br>";
		}
		if ($result3 = mysqli_query($_SESSION['link'], $query)) {
			if ($line3 = mysqli_fetch_array($result3, MYSQLI_ASSOC))
			{
				$matchreshemma = $line3["hemmamal"];
				$matchresborta = $line3["bortamal"];
			}
			else
			{
				$matchreshemma = "";
				$matchresborta = "";
			}
			if ($matchreshemma != "" && $matchresborta != "")
			{
				$body .=  "\t\t<td align=\"center\">".$matchreshemma." - ".$matchresborta."</td>\n";
				$matchpoang = calcPoints($matchreshemma, $matchresborta, $$hemmamal_var, $$bortamal_var);
				$poang += $matchpoang;
				$body .=  "\t\t<td align=\"center\" ".getColorByPoints($matchpoang).">$matchpoang</td>\n";
			}
			else
			{
				$body .=  "\t\t<td>&nbsp;</td>\n";
				$body .=  "\t\t<td>&nbsp;</td>\n";
			}
		}

		$body .=  "\t</tr>\n";
	}
}

if ($betalat == "")
{
    if ($messageToShow != "")
    {
        $messageToShow .= "<br>";
    }
    $messageToShow .= "<b>You have not payed.</b>";
}
?>
<table>
  <tr>
  	<td width="90%" valign="top">      
        <table border="0"> 
        <tr>
        <td width="40">&nbsp;</td>
        <td>
		<h1><?php print $_SESSION['titleText']; ?></h1>
<?php
print $messageToShow;
print $body;
print "<tr><td colspan=\"6\">&nbsp;</td><td colspan=\"2\">Sum points</td>";
print "<td align=\"center\">$poang</td>";
?>
</tr>
</table>
		</td>
		</tr>
		</table>
  	</td>
<?php  	
	print getUserSection($_SESSION['gameloggedin'], $_SESSION['tablePrefix'], $_SESSION['zyklaDir'], $_SESSION['feedbackLink'], $_SESSION['officialGameSiteLink']);
?>
  	</tr>
    </table>
</body>
</html>
