<?php 
session_start();
/*
	Visa alla tips.
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

function checkPointsInDB($tippare, $matchid, $poang, $tablePrefix)
{
	$query = "select * from ".$tablePrefix."matchtips where tippare=$tippare and matchid=$matchid and poang=$poang";
	$update = "update ".$tablePrefix."matchtips set poang = $poang where tippare=$tippare and matchid=$matchid";
	if ($result = mysqli_query($_SESSION['link'], $query)) {
		if (!($line = mysqli_fetch_array($result, MYSQLI_ASSOC))) {
			mysqli_query($_SESSION['link'], $update);
		} 
	}
}

function getUserHeader($tablePrefix)
{
	$returnValue = "";
	$fields = "namn";
	$frompart = $tablePrefix."tippare";
	$wherepart = "userid<>'demo'";
	$orderby = "namn";
	$query = "select $fields from $frompart where $wherepart order by $orderby";
    if ($result = mysqli_query($_SESSION['link'], $query)) {
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
		{
			$returnValue .= "<td colspan=\"2\" valign=\"top\" style=\"writing-mode: tb-rl;\"><font size=\"-1\">".formatName($line["namn"])."</font></td>"; 
		}
	}
	return $returnValue;
}

function formatName($text) {
	$returnValue = "";
	for ($i = 0; $i < strlen($text); $i++)	{
		if ($text{$i} == " ") {
			$returnValue .= "<br>";
		}
		$returnValue .= $text{$i};
	}
	
	return $returnValue;
}

function getUserTips($matchid, $matchreshemma, $matchresborta, $tablePrefix)
{
	$returnValue = "";
	$fields = "id";
	$frompart = $tablePrefix."tippare";
	$wherepart = "userid<>'demo'";
	$orderby = "namn";
	$query = "select $fields from $frompart where $wherepart order by $orderby";
    if ($result = mysqli_query($_SESSION['link'], $query)) {
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
		{
			$hemmamal = "";
			$bortamal = "";
			$matchpoang = -1;
			$fields = "hemmamal,bortamal";
			$frompart = $tablePrefix."matchtips";
			$wherepart = "tippare=".$line["id"]." and matchid=$matchid";
			$query = "select $fields from $frompart where $wherepart";
			if ($result2 = mysqli_query($_SESSION['link'], $query)) {
				if ($line2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
				{
					$hemmamal = $line2["hemmamal"];
					$bortamal = $line2["bortamal"];
					if ($matchreshemma != "" && $matchresborta != "")
					{
						$matchpoang = calcPoints($matchreshemma, $matchresborta, $hemmamal, $bortamal);
						checkPointsInDB($line["id"], $matchid, $matchpoang, $tablePrefix);
					}
				}
			}
			$returnValue .= "<td align=\"center\"><font size=\"-1\"><nobr>".$hemmamal."-".$bortamal."</nobr></font></td>"; 
			$returnValue .= "<td align=\"center\" ".getColorByPoints($matchpoang)."><font size=\"-1\">".showPoang($matchpoang)."</font></td>"; 
		}
	}
	return $returnValue;
}

$headerrow = "<tr><td valign=\"top\"><font size=\"-1\"><b>Home</b></font></td><td valign=\"top\"><font size=\"-1\"><b>Away</b></font></td><td valign=\"top\"><font size=\"-1\"><b>Score</b></font></td>".getUserHeader($_SESSION['tablePrefix'])."<tr>";
$fields = "id, hemmalag, bortalag, hemmamal, bortamal";
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
		$body .= "\t<tr>\n";
		$body .= "\t\t<td><font size=\"-1\">".$line["hemmalag"]."</font></td>\n";
		$body .= "\t\t<td><font size=\"-1\">".$line["bortalag"]."</font></td>\n";
		$body .= "\t\t<td align=\"center\" ".getColorByResult($line["hemmamal"], $line["bortamal"])."><font size=\"-1\">".$line["hemmamal"]."-".$line["bortamal"]."</td>\n";
		$body .= getUserTips($line["id"], $line["hemmamal"], $line["bortamal"], $_SESSION['tablePrefix']);
		$body .= "\t</tr>\n";
	}
?>
<h1><?php print $_SESSION['titleText']; ?></h1>
<?php
	print $body;
}


?>
</body>
</html>
