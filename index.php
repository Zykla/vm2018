<?php 
session_start();
include_once("settings.php");
/*
	Startsida för vmtipset 2018
	Author: Mattias Melin
	Changed: 2014-05-15
*/
// Include function collection
include("functions.php");
//session_register("gameloggedin");
if (strlen($_SESSION['gameloggedin']) == 0)
{
    $_SESSION['gameloggedin'] = checkLoginGame($_POST['gameuserid'], $_POST['gamepassword'], $_SESSION['tablePrefix']);
}
if (strlen($_SESSION['gameloggedin']) > 0 && $_POST['action'] == logout)
{
    $_SESSION['gameloggedin'] = "";
}

    print "<html>\n";
    print "\t<head>\n";
    print "\t\t<title>".$_SESSION['titleText']."</title>\n";
    print "\t</head>\n";
    print "\t<body BGCOLOR=\"".$_SESSION['bgColor']."\" background=\"".$_SESSION['backgroundImage']."\">\n";
?>
<table>
  <tr>
  	<td width="90%" valign="top">
        <table border="0"> 
        <tr>
        <td width="40">&nbsp;</td>
        <td>
		<h1><?php print $_SESSION['titleText'] ?></h1>

		Join the World Cup Tips<br>
		Tip all <?php print noOfMatches($_SESSION['tablePrefix']); ?> matches in the group stage and get a chans to win.<br>

		<br><b>Scoring explained:</b>
		<ul>
			<li>3 points for the correct result</li>
			<li>2 points for correct winner and goal difference</li>
			<li>1 point for correct winner.</li>
			<li>Tied games can only given 3, 1 or 0 points</li>
		</ul>
		<br>
		<b>Winnings (preliminary):</b>
		<ul>
			<li>Number one gets 70% of the pot</li>
			<li>Second place gets 20%</li>
			<li>Third place gets 10%</li>
			<li>If there is more than one winner they share the winnings</li>
		</ul>
<?php
//		<br>
//	<br>
//		The stake in the game is <b>50 SEK</b> (must be payed to Mattias Melin before the first match starts)<br>
//		Swish is preferred payment method. Contact Mattias Melin for more information.
//        print "The pot is currently ".pott($_SESSION['tablePrefix'])." SEK</b> (+<i><b>".ejBetalat($_SESSION['tablePrefix'])." SEK</b> that is to be payed</i>).<br>";
	print "The pot is ".pott($_SESSION['tablePrefix'])." SEK<br>";
//		<br>
//		You can change your tips until match start for the first game (14:e of June at 17.00 swedish time).
//		<br>
//		<b>N.B.</b> You can create an random tip (0-4 goals) for all matches or matches that has no result.<br>
?>
	<br>
	<b>Layout 2 should work now.</b><br>
	<br>
		Mattias Melin</b>
		<hr>
		</p>
		</td>
		</tr>
<?php
if (strlen($_SESSION['gameloggedin']) == 0 && !gameHasStarted())
{
	print "<tr>\n";
    print "<td width=\"40\">&nbsp;</td>";
	print "<td>\n";
    print "<br><br>Create user below:\n<br><hr>";
    print "<form action=\"createuser.php\" method=\"post\">\n";
	print "\t<table>\n";
	print "\t\t<tr>\n";
	print "\t\t\t<td>Name</td>\n";
	print "\t\t\t<td><input type=\"text\" name=\"namn\" size=\"50\" autocomplete=\"off\"/></td>\n";
	print "\t\t</tr>\n";
	print "\t\t<tr>\n";
	print "\t\t\t<td>Email</td>\n";
	print "\t\t\t<td><input type=\"text\" name=\"email\" size=\"50\" autocomplete=\"off\"/></td>\n";
	print "\t\t</tr>\n";
	print "\t\t<tr>\n";
	print "\t\t\t<td>Username</td>\n";
	print "\t\t\t<td><input type=\"text\" name=\"username\" size=\"50\" autocomplete=\"off\"/></td>\n";
	print "\t\t</tr>\n";
	print "\t\t<tr>\n";
	print "\t\t\t<td>Password</td>\n";
	print "\t\t\t<td><input type=\"password\" name=\"pwd\" size=\"50\" autocomplete=\"off\"/></td>\n";
	print "\t\t</tr>\n";
	print "\t\t<tr>\n";
	print "\t\t\t<td>&nbsp;</td>\n";
	print "\t\t\t<td>(N.B. that the password handled unencrypted and is sent in the confirmation email so use a separate password for this game).</td>\n";
	print "\t\t</tr>\n";
	print "\t\t<tr>\n";
	print "\t\t\t<td>&nbsp;</td>\n";
	print "\t\t\t<td><button type=\"submit\">Create user</button></td>\n";
	print "\t\t</tr>\n";
	print "\t</table>\n";
	print "</form>\n<hr>";
	print "</td>\n";
	print "</tr>\n";
}

if (gameHasStarted())
{
	print "<tr>\n";
//    print "<td width=\"40\">&nbsp;</td>";
//	print "<td>Tävlingen är stängd för nya tävlanden!<br>";
//	print "Kontakta Mattias Melin om du ändå vill vara med (spelade matcher ger 0 poäng).</td></tr>";
//	print "<tr>\n";

	print "<tr>\n";
    print "<td width=\"40\">&nbsp;</td>";
	print "<td valign=\"top\"><br><b>Leaderboard ".noOfMatchesPlayed($_SESSION['tablePrefix'])." (48)</b><br>";
	print "".leaderboard($_SESSION['tablePrefix'])."</td>";
	
	if (noOfMatchesPlayed($_SESSION['tablePrefix']) > 10) {
	  print "<td width=\"25\">&nbsp;</td>";
		print "<td valign=\"top\"><br><b>Last 10 games</b><br>";
		print "".leaderboardLastNoOfMatches(10, $_SESSION['tablePrefix'])."</td>";
  }
	if (noOfMatchesPlayed($_SESSION['tablePrefix']) > 5) {
	  print "<td width=\"25\">&nbsp;</td>";
		print "<td valign=\"top\"><br><b>Last 5 games</b><br>";
		print "".leaderboardLastNoOfMatches(5, $_SESSION['tablePrefix'])."</td>";
	}
  print "<td width=\"25\">&nbsp;</td>";
	print "<td valign=\"top\"><br><b>More statistics</b><br>";
	print "".getAdditionalStatistics($_SESSION['tablePrefix'])."</td></tr>";
}
	print "<tr>\n";
    print "<td width=\"40\">&nbsp;</td>";
	print "<td colspan=\"7\">\n";
	print "</td></tr>";
?>
		</table>
  	</td>
<?php  	
	print getUserSection($_SESSION['gameloggedin'], $_SESSION['tablePrefix'], $_SESSION['zyklaDir'], $_SESSION['feedbackLink'], $_SESSION['officialGameSiteLink']);
?>
  	</tr>
    </table>
</body>
</html>



