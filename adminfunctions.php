<?php
if (get_magic_quotes_gpc()) { $addslash="no"; } //AUTO-ADD SLASHES IF MAGIC QUOTES ARE ON

function displayUnPayedUsers($tablePrefix)
{
	$header = "<h3>Ej betalat</h3><table>";
	$footer = "</table>";
	$returnValue = "";
	$fields = "id, namn, emailadress";
	$frompart = $tablePrefix."tippare";
	$wherepart = "betalat is null and userid<>'demo'";
	$orderby = "namn";
	$query = "select $fields from $frompart where $wherepart order by $orderby";
    if ($result = mysqli_query($_SESSION['link'], $query)) {
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
		{
			$returnValue .= "\t<tr>\n"; 
			if ($line["namn"] != "")
			{
				$returnValue .=  "\t\t<td valign=\"top\">".$line["namn"]."</td>\n";
			}
			else
			{
				$returnValue .=  "\t\t<td>&nbsp;</td>\n";
			}
			if ($line["emailadress"] != "")
			{
				$returnValue .=  "\t\t<td>&nbsp;</td>\n\t\t<td valign=\"top\"><a href=\"mailto:".$line["emailadress"]."?Subject=Betalning ".$subjectEmail."\">".$line["emailadress"]."</a></td>\n";
			}
			else
			{
				$returnValue .=  "\t\t<td>&nbsp;</td>\n\t\t<td>&nbsp;</td>\n";
			}
			$returnValue .=  "\t\t<td>&nbsp;</td>\n\t\t<td><form action=\"admin.php\" method=\"post\">\n";
			$returnValue .=  "\t\t\t<input type=\"hidden\" value=\"".$line["id"]."\" name=\"betalat_tipparid\"/>\n";
			$returnValue .=  "\t\t\t<button type=\"submit\">Betalat</button>";
			$returnValue .=  "\t\t</form></td>";
			$returnValue .= "\t</tr>\n"; 
		}
	}
	if ($returnValue != "")
	{
		$returnValue = $header.$returnValue.$footer;
	}
	return $returnValue;
}

function displayUsersWithoutTips($tablePrefix)
{
	$header = "<h3>Ej tippat alla matcher</h3><table>";
	$footer = "</table>";
	$returnValue = "";
	$antalMatcher = 0;
	$fields = "count(*) antal";
	$frompart = $tablePrefix."match";
	$query = "select $fields from $frompart";
    if ($result = mysqli_query($_SESSION['link'], $query)) {
		if ($line = mysqli_fetch_array($result, MYSQLI_ASSOC))
		{
			$antalMatcher = $line["antal"];
		}
	}

	$fields = "id,namn,emailadress";
	$frompart = $tablePrefix."tippare";
	$wherepart = "userid<>'demo'";
	$orderby = "namn";
	$query = "select $fields from $frompart where $wherepart order by $orderby";
    if ($result = mysqli_query($_SESSION['link'], $query)) {
		while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
		{
			$fields = "count(*) antal";
			$frompart = $tablePrefix."matchtips";
			$wherepart = "tippare=".$line["id"];
			$query = "select $fields from $frompart where $wherepart";
			if ($result2 = mysqli_query($_SESSION['link'], $query)) {
				if ($line2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) 
				{
					$antalTippadeMatcher = $line2["antal"];
				}
				if ($antalTippadeMatcher < $antalMatcher)
				{
					$returnValue .= "\t<tr>\n"; 
					$returnValue .=  "\t\t<td>".$line["namn"]."</td>\n";
					if ($line["emailadress"] != "")
					{
						$returnValue .=  "\t\t<td>&nbsp;</td>\n\t\t<td valign=\"top\"><a href=\"mailto:".$line["emailadress"]."?Subject=".$subjectEmail."&body=Du har inte tippat alla macther.\">".$line["emailadress"]."</a></td>\n";
					}
					else
					{
						$returnValue .=  "\t\t<td>&nbsp;</td>\n\t\t<td>&nbsp;</td>\n";
					}
					$returnValue .=  "\t\t<td>".$antalTippadeMatcher." tippade matcher</td>\n";
					$returnValue .= "\t</tr>\n"; 
				}
			}
		}
	}
	if ($returnValue != "")
	{
		$returnValue = $header.$returnValue.$footer;
	}
	return $returnValue;
}


?>