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
	if (gameHasStarted())
	{
	    die("<b>Du kan inte ändra ditt tips pga att mästerskapet har startat!</b>");
	}

$headerrow = "<tr><th>Date</th><th>Time</th><th>Gr</th><th>Home</th><th>Away</th><th>Arena</th><th>&nbsp;</th><th>&nbsp;</th><tr>";
$fields = "id, DATE_FORMAT(matchdatum, \"%e/%c\"), TIME_FORMAT(tvtid,\"%H:%i\"), grupp, hemmalag, bortalag, matchstad";
$frompart = $_SESSION['tablePrefix']."match";
$orderby = "matchdatum,tvtid,id";
$query = "SELECT ".$fields." FROM ".$frompart." order by ".$orderby;
$updatedData = false;
if ($debug)
{
    print "SQL: ".$query."<br>";
}
if ($result = mysqli_query($_SESSION['link'], $query)) {
	// Printing results in HTML
	$body = "<table border=\"1\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#85BBE9\">\n";
	$body .= $headerrow."\n";
	$mailbody = $body;
	while ($line = mysqli_fetch_array($result, MYSQLI_ASSOC)) 
	{
		$body .=  "\t<tr>\n";
		$mailbody .=  "\t<tr>\n";
		$index = 0;
		foreach ($line as $col_value) 
		{
			if ($index > 0) {
				if ($col_value != "")
				{
					$body .=  "\t\t<td valign=\"top\">$col_value</td>\n";
					$mailbody .=  "\t\t<td valign=\"top\">$col_value</td>\n";
				}
				else
				{
					$body .=  "\t\t<td>&nbsp;</td>\n";
					$mailbody .=  "\t\t<td>&nbsp;</td>\n";
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
			$$hemmamal_var = $_POST[$hemmamal_var];
			//echo $hemmamal_var."=".$_POST[$hemmamal_var]." ".$$hemmamal_var;
			$$bortamal_var = $_POST[$bortamal_var];
			if ($$hemmamal_var == "" && $$bortamal_var == "")
			{
				$$hemmamal_var = "-";
				$$bortamal_var = "-";
			} 
			if ($$hemmamal_var != "-" || $$bortamal_var != "-")
			{
			  if ($$hemmamal_var != "0") 
			  {
				  $$hemmamal_var = abs($$hemmamal_var);
				}
			  if ($$bortamal_var != "0")
			  {
				 $$bortamal_var = abs($$bortamal_var);
				}
			}
			$updatesql = "";
			
			if ((!($dbhemmamal == $$hemmamal_var && $dbhemmamal != "") && $$hemmamal_var != "" && $$hemmamal_var != "-") ||
				(!($dbbortamal == $$bortamal_var && $dbbortamal != "") && $$bortamal_var != "" && $$bortamal_var != "-"))
			{
				$updatesql = "hemmamal=".$$hemmamal_var;
				$updatesql .= ",bortamal=".$$bortamal_var;
			}
			else if ($dbhemmamal != "" || $dbbortamal != "")
			{
				$$hemmamal_var = $dbhemmamal;
				$$bortamal_var = $dbbortamal;
			}
			else if ($_GET['slumpa'] == "ejtippade") 
			{
				$$hemmamal_var = getRandomScore();	
				$$bortamal_var = getRandomScore();	
			}
			if ($_GET['slumpa'] == "alla") 
			{
				$$hemmamal_var = getRandomScore();	
				$$bortamal_var = getRandomScore();	
			}
			if ($updatesql != "")
			{
				$updatesql = "update ".$_SESSION['tablePrefix']."matchtips set $updatesql where matchid=".$line["id"]." and tippare=$tippare";
				mysqli_query($_SESSION['link'], $updatesql);
				if (mysqli_affected_rows($_SESSION['link']) == 0)
				{
					$updatesql = "insert into ".$_SESSION['tablePrefix']."matchtips(matchid,tippare,hemmamal,bortamal) values (".$line["id"].", ".$tippare.",".$$hemmamal_var.",".$$bortamal_var.")";
					mysqli_query($_SESSION['link'], $updatesql);
				}
				$updatedData = true;
			}

			$body .=  "\t\t<td><input type=\"text\" name=\"".$hemmamal_var."\" size=\"2\" maxlength=\"2\" value=\"".$$hemmamal_var."\"></td>\n";
			$body .=  "\t\t<td><input type=\"text\" name=\"".$bortamal_var."\" size=\"2\" maxlength=\"2\" value=\"".$$bortamal_var."\"></td>\n";
			$body .=  "\t</tr>\n";			
			$mailbody .=  "\t\t<td>".$$hemmamal_var."</td>\n";
			$mailbody .=  "\t\t<td>".$$bortamal_var."</td>\n";
			$mailbody .=  "\t</tr>\n";			
		}	
	}
}

if ($updatedData)
{
    /* Receiver */
    $to = $emailadress;
    /* subject */
    $mailSubject = "Your ".$_SESSION['subject']." ".date("Y-m-d H:i");
    
    /* message */
    $message = "
    <html>
    <head>
     <title>Your tip for ".$_SESSION['championship']."</title>
    </head>
    <body>
    <p>Here is acopy of your tip ".$_SESSION['championship']." submitted ".date("Y-m-d H:i")."</p>
    ".$mailbody."
    </body>
    </html>
    ";
    
    /* To send HTML mail, you can set the Content-type header. */
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-type: text/html";
    $headers .= "; charset=UTF-8\n";
    $headers .= "Content-Transfer-Encoding: 8bit\n";
    
    /* additional headers */
    $headers .= "From: Tip ".$_SESSION['championship']." <mattias.melin@zykla.net>\n";
    $headers .= "Bcc: mattias.melin@zykla.net\n";
    $headers .= "Reply-To: mattias.melin@ist.biz\n";
    $headers .= "X-Mailer: PHP/" . phpversion()."\n";
    
    /* and now mail it */
    if ($to != "" && mail($to, $mailSubject, $message, $headers))
    {
        $messageToShow = "<b>A copy of your tip has been sent to $emailadress.</b>";
    }
    else
    {
        $messageToShow = "<b>We could not send a copy of your tip to  \"$emailadress\".<br>Check if the email address is correct. Contact Mattias Melin if you need to correct it..</b>";
    }
}
 	// Add links for random
    	if ($messageToShow != "")
    	{
      	$messageToShow .= "<br>";
    	}
	$messageToShow .= "<b>Random result (0-4 goals):</b>&nbsp;&nbsp;&nbsp;";
	$messageToShow .= "<a href=\"tippamatcher.php?slumpa=ejtippade\"><b>Games without goals</b></a>&nbsp;&nbsp;&nbsp;";
	$messageToShow .= "<a href=\"tippamatcher.php?slumpa=alla\"><b>All games</b></a><br>";
if ($betalat == "")
{
    if ($messageToShow != "")
    {
        $messageToShow .= "<br>";
    }
    $messageToShow .= "<b>You have not paid.</b>";
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
print "<form action=\"tippamatcher.php\" method=\"post\">\n";
print $body;
?>
<tr>
<td colspan="7">&nbsp;</td>
<td colspan="2"><button type="submit"><font size="-1">Save</font></button></td>
</tr>
</table>
</form>
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
