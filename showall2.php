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

function getHeader($tablePrefix)
{
	$returnValue = "";
	$fields = "id, hemmalag, bortalag, hemmamal, bortamal";
	$frompart = $tablePrefix."match";
	$orderby = "matchdatum,tvtid,id";
	$query = "select $fields from $frompart order by $orderby";
	if ($result = mysqli_query($_SESSION['link'], $query)) {
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
		{
			$returnValue .= "<td align=\"center\" colspan=\"2\" valign=\"top\"><font size=\"-1\">".$line["hemmalag"]."<br/>".$line["bortalag"]."<br/>".$line["hemmamal"]."-".$line["bortamal"]."</font></td>"; 
		}
	}
	return $returnValue;
}

function getMatchOrder($tablePrefix,$noOfMatches)
{
	$returnValue = array();
	$fields = "id, hemmalag, bortalag, hemmamal, bortamal";
	$frompart = $tablePrefix."match";
	$orderby = "matchdatum,tvtid,id";
	$index = 1;
	$query = "select $fields from $frompart order by $orderby";
	if ($result = mysqli_query($_SESSION['link'], $query)) {
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
		{
			$returnValue[$index++] = $line["id"];
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

function getUserTips($matchid, $user_id, $tablePrefix)
{
	$fields1 = "id, hemmalag, bortalag, hemmamal, bortamal";
	$frompart1 = $tablePrefix."match";
	$wherepart1 = "id=$matchid";
	$query1 = "select $fields1 from $frompart1 where $wherepart1";
	if ($result1 = mysqli_query($_SESSION['link'], $query1)) {
		if ($line1 = mysqli_fetch_array($result1, MYSQLI_ASSOC))
		{
			$matchreshemma = $line1["hemmamal"];
			$matchresborta = $line1["bortamal"];
		}
	}

	$hemmamal = "";
	$bortamal = "";
	$matchpoang = -1;
	$fields = "hemmamal,bortamal";
	$frompart = $tablePrefix."matchtips";
	$wherepart = "tippare=$user_id and matchid=$matchid";
	$query = "select $fields from $frompart where $wherepart";
	if ($result2 = mysqli_query($_SESSION['link'], $query)) {
		if ($line2 = mysqli_fetch_array($result2, MYSQLI_ASSOC))
		{
			$hemmamal = $line2["hemmamal"];
			$bortamal = $line2["bortamal"];
			if ($matchreshemma != "" && $matchresborta != "")
			{
				$matchpoang = calcPoints($matchreshemma, $matchresborta, $hemmamal, $bortamal);
				checkPointsInDB($user_id, $matchid, $matchpoang, $tablePrefix);
			}
		}
	}
	$returnValue = "<td align=\"center\"><font size=\"-1\"><nobr>".$hemmamal."-".$bortamal."</nobr></font></td>"; 
	$returnValue .= "<td align=\"center\" ".getColorByPoints($matchpoang)."><font size=\"-1\">".showPoang($matchpoang)."</font></td>"; 
	return $returnValue;
}

$headerrow = "<tr><td align=\"center\"><font size=\"-1\">Tippare</font></td>".getHeader($_SESSION['tablePrefix'])."</tr>";
$fields = "id, namn";
$frompart = $_SESSION['tablePrefix']."tippare";
$orderby = "namn";
$wherepart = "userid<>'demo'";
$query = "select $fields from $frompart where $wherepart order by $orderby";
$noOfMatches = noOfMatches($_SESSION['tablePrefix']);

if ($debug)
{
    print "SQL: ".$query."<br>";
}
if ($result = mysqli_query($_SESSION['link'], $query)) {

	// Printing results in HTML
	$body = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#85BBE9\">\n";
	$body .= $headerrow."\n";
	$poang = 0;
    $matchOrder = getMatchOrder($_SESSION['tablePrefix'],$noOfMatches);
	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
	{
		$body .= "\t<tr>\n";
		$body .= "\t\t<td><font size=\"-1\">".$line["namn"]."</font></td>\n";
		for ($i = 1; $i <= $noOfMatches; $i++)	{
			$body .= getUserTips($matchOrder[$i], $line["id"], $_SESSION['tablePrefix']);
		}
		$body .= "\t</tr>\n";
	}
}
?>
<h1><?php print $titleText; ?></h1>
<?php
print $body;
?>
</body>
</html>
