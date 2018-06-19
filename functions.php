<?php
if (get_magic_quotes_gpc()) { $addslash="no"; } //AUTO-ADD SLASHES IF MAGIC QUOTES ARE ON
include("../includes/functions.php");
$dbname		= "zykla_net"; 
$_SESSION['link'] = selectdatabase($dbname);
date_default_timezone_set('Europe/Stockholm');

function checkLoginGame($userid, $password, $tablePrefix)
{
    $returnvalue = "";
    if ($password != "" && $userid != "")
    {
	    $query = "select namn from ".$tablePrefix."tippare where userid='$userid' and pwd='$password'";
	    if ($result = mysqli_query($_SESSION['link'], $query)) {
			if ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
			{
				foreach ($line as $col_value) 
				{
					$returnvalue = $col_value;
				}
			}
		}
	}
    return $returnvalue;
} 

function countActiveUsersInGame($tablePrefix) 
{
    $returnvalue = "0";
    $query = "select count(distinct namn) from ".$tablePrefix."tippare where userid<>'demo'";
    if ($result = mysqli_query($_SESSION['link'], $query)) {
		if ($line = mysqli_fetch_array($result))
		{
			foreach ($line as $col_value) 
			{
				$returnvalue = $col_value;
			}
		}
	}

    return $returnvalue;
} 

function getPaymentDate($user, $tablePrefix) 
{
    $returnvalue = "";
    $query = "select betalat from ".$tablePrefix."tippare where namn='$user'";
	if ($result = mysqli_query($_SESSION['link'], $query)) {
		if ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			foreach ($line as $col_value) 
			{
				$returnvalue = $col_value;
			}
		}
	}
    return $returnvalue;
} 

function pott($tablePrefix) 
{
    $returnvalue = 0;
    $toPay = 50;
    $query = "select count(distinct namn) from ".$tablePrefix."tippare where betalat is not null and userid<>'demo'";
	if ($result = mysqli_query($_SESSION['link'], $query)) {
		if ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			foreach ($line as $col_value) 
			{
				$returnvalue = $col_value * $toPay;
			}
		}
	}
    return $returnvalue;
} 

function ejBetalat($tablePrefix) 
{
    $returnvalue = 0;
    $toPay = 50;
    $query = "select count(distinct namn) from ".$tablePrefix."tippare where betalat is null and userid<>'demo'";
	if ($result = mysqli_query($_SESSION['link'], $query)) {
		if ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			foreach ($line as $col_value) 
			{
				$returnvalue = $col_value * $toPay;
			}
		}
	}
    return $returnvalue;
} 

function getUserSection($user, $tablePrefix, $zyklaDir, $feedbackLink, $officialGameSiteLink)
{
	$returnhtml =  "<td width=\"10%\" valign=\"top\">";
    $returnhtml .= "<form action=\"index.php\" method=\"post\">";
    $returnhtml .= "<hr><table border=\"0\" width=\"150\">";
		$returnhtml .=  "<tr>\n";
		$returnhtml .=  "\t<td align=\"center\"><font size=\"-1\"><nobr>".date("Y-m-d H:i")."</nobr></font></td>\n";
		$returnhtml .=  "</tr>\n";
 
	if (strlen($user) == 0)
	{
		$returnhtml .=  "<tr>\n";
		$returnhtml .=  "\t<td><font size=\"-1\">Username</font></td>\n";
		$returnhtml .=  "\t<td><input type=\"text\" name=\"gameuserid\" size=\"10\"/></td>\n";
		$returnhtml .=  "</tr>\n";
		$returnhtml .=  "<tr>\n";
		$returnhtml .=  "\t<td><font size=\"-1\">Password</font></td>\n";
		$returnhtml .=  "\t<td><input type=\"password\" name=\"gamepassword\" size=\"10\"/></td>\n";
		$returnhtml .=  "</tr>\n";
		$returnhtml .=  "<tr>\n";
		$returnhtml .=  "\t<td>&nbsp;</td>\n";
		$returnhtml .=  "\t<td><button type=\"submit\"><font size=\"-1\">Log in</font></button></td>\n";
		$returnhtml .=  "</tr>\n";
		$returnhtml .=  "<tr><td align=\"center\" colspan=\"2\">";
	}
	else
	{
		$returnhtml .=  "<tr>\n";
		$returnhtml .=  "\t<td align=\"center\"><font size=\"-1\">Logged in as <br>$user</font></td>\n";
		$returnhtml .=  "</tr>\n";
		$paymentDate = getPaymentDate($user, $tablePrefix);
		if ($paymentDate != "")
		{
	    	$returnhtml .=  "<tr>\n";
	    	$returnhtml .=  "\t<td align=\"center\"><font size=\"-1\">Payed $paymentDate</font></td>\n";
	    	$returnhtml .=  "</tr>\n";
	    }
	    else
	    {
	    	$returnhtml .=  "<tr>\n";
	    	$returnhtml .=  "\t<td align=\"center\"><font size=\"-1\"><b>Not payed yet</b></font></td>\n";
	    	$returnhtml .=  "</tr>\n";
	    }
		$returnhtml .=  "<tr>\n";
		$returnhtml .=  "\t<td align=\"center\"><button type=\"submit\"><font size=\"-1\">Log out</font></button></td>\n";
		$returnhtml .=  "</tr>\n";
		$returnhtml .=  "<tr>\n";
		$returnhtml .=  "\t<td><input type=\"hidden\" name=\"action\" value=\"logout\"/></td>\n";
		$returnhtml .=  "</tr>\n";
		$returnhtml .=  "<tr><td align=\"center\">";
	}
	$returnhtml .=  countActiveUsersInGame($tablePrefix)." active users";
	if (strlen($user) > 0)
	{
		$returnhtml .=  "<hr><a href=\"index.php\">Startpage</a><br>";
		if (!gameHasStarted())
		{
			$returnhtml .=  "<a href=\"tippamatcher.php\">Change tips</a><br>";
		}
		$returnhtml .=  "<a href=\"visatips.php\">Show tips</a><br>";
		if ($user == "Mattias Melin")
		{
			$returnhtml .=  "<a href=\"admin.php\">Admin</a><br>";
		}
	}
	if (gameHasStarted() || $user == "Mattias Melin") {
	    $returnhtml .=  "<hr>Show all tips:<br>";
	    $returnhtml .=  "<a href=\"showall.php\" target=\"_blank\">Layout 1</a><br/><a href=\"showall2.php\" target=\"_blank\">Layout 2</a><br>";
	    $returnhtml .=  "<hr>";
	}
	$returnhtml .=  "</td></tr>";
	$returnhtml .=  "<tr><td align=\"center\" colspan=\"2\">";
	$returnhtml .=  $feedbackLink;
	$returnhtml .=  "</td></tr>";
	$returnhtml .=  "<tr><td align=\"center\" colspan=\"2\">";
	$returnhtml .=  $officialGameSiteLink;
	$returnhtml .=  "<hr></td></tr>";
	$returnhtml .=  "</table></form>";
	return $returnhtml;
}

function gameHasStarted()
{
	return (time() >= mktime(17, 0, 0, 6, 14, 2018));
}

function calcPoints($matchreshemma, $matchresborta, $tipshemma, $tipsborta)
{
	$matchpoang = -1;
	if ($matchreshemma != "" && $matchresborta != "")
	{
	    if ($matchreshemma == $tipshemma && $matchresborta == $tipsborta)
	    {
	    	$matchpoang = 3;
	    }
	    else if (($matchreshemma - $matchresborta) == ($tipshemma - $tipsborta) &&
	    		 ($matchreshemma != $matchresborta))
	    {
	    	$matchpoang = 2;
	    }
	    else if ((($matchreshemma > $matchresborta) && ($tipshemma > $tipsborta)) ||
	    		 (($matchreshemma < $matchresborta) && ($tipshemma < $tipsborta)) ||
	    		 (($matchreshemma == $matchresborta) && ($tipshemma == $tipsborta)))
	    {
	    	$matchpoang = 1;
	    }
	    else
	    {
	    	$matchpoang = 0;
	    }
    }
    return $matchpoang;
}

function getColorByPoints($matchpoang)
{
	$bgColor = "";
	if ($matchpoang == 3)
	{
		$bgColor="bgcolor=\"red\"";
	}
	if ($matchpoang == 2)
	{
		$bgColor="bgcolor=\"green\"";
	}
	if ($matchpoang == 1)
	{
		$bgColor="bgcolor=\"yellow\"";
	}
	if ($matchpoang == 0)
	{
		$bgColor="bgcolor=\"white\"";
	}
	return $bgColor;
}

function showPoang($matchpoang)
{
	$returnValue = $matchpoang;
	if ($matchpoang == -1)
	{
		$returnValue = "&nbsp;";
	}
	return $returnValue;
}

function getColorByResult($matchreshemma, $matchresborta)
{
	$bgColor = "";
	if ($matchreshemma != "" && $matchresborta != "")
	{
		$bgColor="bgcolor=\"gray\"";
	}
	return $bgColor;
}

function leaderboard($tablePrefix)
{
    $returnvalue = "";
    $query = "select namn,sum(poang) summa from ".$tablePrefix."matchtips,".$tablePrefix."tippare where tippare=id and userid<>'demo' group by tippare order by 2 desc,namn";
    if ($result = mysqli_query($_SESSION['link'], $query)) {
		$poang = 0;

		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if ($line["summa"] != $poang)
			{
				$poang = $line["summa"];
				$returnvalue .= "<br/><b>".$poang." poäng</b><br/>";
			}
			$returnvalue .= $line["namn"]."<br/>";
		}
		
	}
    return $returnvalue;
}

function leaderboardLastNoOfMatches($noOfMatchesBack, $tablePrefix)
{
    $returnvalue = "";
    $startmatchid = "";

    $lastmatchquery = "select id from ".$tablePrefix."match where hemmamal is not null and bortamal is not null order by matchdatum desc, tvtid desc";
	
    if ($lastmatchresult = mysqli_query($_SESSION['link'], $lastmatchquery)) {
		$count = $noOfMatchesBack;
		while ($lastmatchline = mysqli_fetch_array($lastmatchresult, MYSQLI_ASSOC)) {
			$count--;
			if ($count >= 0) {
				$startmatchid .= $lastmatchline["id"];
				if ($count > 0) {
					$startmatchid .= ",";	    	  
				}
			}
		}
	}
    $query = "select namn,sum(poang) summa from ".$tablePrefix."matchtips,".$tablePrefix."tippare where tippare=id and userid<>'demo' and matchid in ($startmatchid) group by tippare order by 2 desc,namn";
    if ($result = mysqli_query($_SESSION['link'], $query)) {
		$poang = 0;

		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			if ($line["summa"] != $poang)
			{
				$poang = $line["summa"];
				$returnvalue .= "<br><b>".$poang." poäng</b><br>";
			}
			$returnvalue .= $line["namn"]."<br>";
		}
	}
    return $returnvalue;
}

function noOfMatchesPlayed($tablePrefix)
{
    $returnvalue = 0;
    $query = "select count(*) noofmatches from ".$tablePrefix."match where hemmamal is not null and bortamal is not null";
    if ($result = mysqli_query($_SESSION['link'], $query)) {
		if ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$returnvalue = $line["noofmatches"];
		}
	}
    return $returnvalue;
}

function noOfMatches($tablePrefix)
{
    $returnvalue = 0;
    $query = "select count(*) noofmatches from ".$tablePrefix."match";
    if ($result = mysqli_query($_SESSION['link'], $query)) {
		if ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$returnvalue = $line["noofmatches"];
		}
	}
    return $returnvalue;
}

function getRandomScore() 
{
  return rand(0, 4);
}

function getAdditionalStatistics($tablePrefix) {
    $returnvalue = "";
    $queryPoints = "select namn, count(*) noofmatches from ".$tablePrefix."matchtips,".$tablePrefix."tippare where tippare=id and poang>0 group by namn order by 2 desc";
    $queryZeroPoints = "select namn, count(*) noofmatches from ".$tablePrefix."matchtips,".$tablePrefix."tippare where tippare=id and poang=0 group by namn order by 2 desc";
    $queryOnePoints = "select namn, count(*) noofmatches from ".$tablePrefix."matchtips,".$tablePrefix."tippare where tippare=id and poang=1 group by namn order by 2 desc";
    $queryTwoPoints = "select namn, count(*) noofmatches from ".$tablePrefix."matchtips,".$tablePrefix."tippare where tippare=id and poang=2 group by namn order by 2 desc";
    $queryThreePoints = "select namn, count(*) noofmatches from ".$tablePrefix."matchtips,".$tablePrefix."tippare where tippare=id and poang=3 group by namn order by 2 desc";
    if ($result = mysqli_query($_SESSION['link'], $queryPoints )) {
		$noofmatches = 0;
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{   
			if ($noofmatches == 0) {
				$returnvalue .= "<br><b>Most games with points (".$line["noofmatches"].")</b><br>";
				$noofmatches = $line["noofmatches"];
		  }
		  if ($noofmatches == $line["noofmatches"]) {
				$returnvalue .= $line["namn"]."<br>";
			}
		}
	}
    if ($result = mysqli_query($_SESSION['link'], $queryThreePoints)) {
		$noofmatches = 0;
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{   
			if ($noofmatches == 0) {
				$returnvalue .= "<br><b>Most 3 point games (".$line["noofmatches"].")</b><br>";
				$noofmatches = $line["noofmatches"];
		  }
		  if ($noofmatches == $line["noofmatches"]) {
				$returnvalue .= $line["namn"]."<br>";
			}
		}
	}
    if ($result = mysqli_query($_SESSION['link'], $queryTwoPoints)) {
		$noofmatches = 0;
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{   
			if ($noofmatches == 0) {
				$returnvalue .= "<br><b>Most 2 point games (".$line["noofmatches"].")</b><br>";
				$noofmatches = $line["noofmatches"];
		  }
		  if ($noofmatches == $line["noofmatches"]) {
				$returnvalue .= $line["namn"]."<br>";
			}
		}
	}
    if ($result = mysqli_query($_SESSION['link'], $queryOnePoints)) {
		$noofmatches = 0;
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{   
			if ($noofmatches == 0) {
				$returnvalue .= "<br><b>Most 1 point games (".$line["noofmatches"].")</b><br>";
				$noofmatches = $line["noofmatches"];
		  }
		  if ($noofmatches == $line["noofmatches"]) {
				$returnvalue .= $line["namn"]."<br>";
			}
		}
	}
    if ($result = mysqli_query($_SESSION['link'], $queryZeroPoints)) {
    $noofmatches = 0;
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{   
			if ($noofmatches == 0) {
				$returnvalue .= "<br><b>Most 0 point games (".$line["noofmatches"].")</b><br>";
				$noofmatches = $line["noofmatches"];
		  }
		  if ($noofmatches == $line["noofmatches"]) {
				$returnvalue .= $line["namn"]."<br>";
			}
		}
	}
        $mostcommonresult = "SELECT if (hemmamal < bortamal, bortamal, hemmamal) mal1, if (hemmamal < bortamal, hemmamal, bortamal) mal2, count(*) noofmatches ";
        $mostcommonresult .= "FROM ".$tablePrefix."match where hemmamal is not null ";
        $mostcommonresult .= "group by 1,2 ";
        $mostcommonresult .= "order by 3 desc";	
    if ($result = mysqli_query($_SESSION['link'], $mostcommonresult)) {
		$noofmatches = 0;
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{   
			if ($noofmatches == 0) {
				$returnvalue .= "<br><b>Most common result (".$line["noofmatches"]." games)</b><br>";
				$noofmatches = $line["noofmatches"];
		  }
		  if ($noofmatches == $line["noofmatches"]) {
				$returnvalue .= $line["mal1"]." - ";
				$returnvalue .= $line["mal2"]."<br>";
			}
		}
	}
        $equalmatches = "SELECT count(*) noofmatches ";
        $equalmatches .= "FROM ".$tablePrefix."match  where hemmamal=bortamal and hemmamal is not null";
    if ($result = mysqli_query($_SESSION['link'], $equalmatches)) {
		if ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{   
			$returnvalue .= "<br><b>Number of tied games: ".$line["noofmatches"]." st</b><br>";
		}
	}

        $equalmatches = "SELECT sum(hemmamal + bortamal) noofgoals ";
        $equalmatches .= "FROM ".$tablePrefix."match";
    if ($result = mysqli_query($_SESSION['link'], $equalmatches)) {
		if ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{   
			$returnvalue .= "<br><b>Total number of goals: ".$line["noofgoals"]."</b><br>";
			$returnvalue .= "<br><b>Goal average: ".round($line["noofgoals"]/noOfMatchesPlayed($tablePrefix),2)."</b><br>";
		}
	}

    return $returnvalue;
}
?>