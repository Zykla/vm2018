<?php 
session_start();

/*
	Adminsida för tipset
	Author: Mattias Melin
	Changed: 2010-05-11
*/
// Include function collection
include_once("settings.php");
include_once("functions.php");
include("adminfunctions.php");
if ($_SESSION['gameloggedin'] == "Mattias Melin")
{
    print "<html>\n";
    print "\t<head>\n";
    print "\t\t<title>$titleText</title>\n";
    print "\t</head>\n";
    print "\t<body BGCOLOR=\"".$_SESSION['bgColor']."\" background=\"".$_SESSION['backgroundImage']."\">\n";
    print "<table><tr><td width=\"90%\" valign=\"top\">";
    print "<table border=\"0\"><tr><td width=\"40\">&nbsp;</td><td>";
    print "\t\t<h1>".$_SESSION['titleText']."</h1>\n";
	$betalat_tipparid = $_POST['betalat_tipparid'];
	  if ($betalat_tipparid != "") {
	  	$updatesql = "update ".$_SESSION['tablePrefix']."tippare set betalat=now() where id=$betalat_tipparid";
	  	mysqli_query($_SESSION['link'],$updatesql);
	  	print "Tippare ".$betalat_tipparid." har betalat!";
		}
    print displayUnPayedUsers($_SESSION['tablePrefix']);
    print displayUsersWithoutTips($_SESSION['tablePrefix']);
    print "\t</td></tr></table>\n";
    print "\t</td>\n";
    print getUserSection($_SESSION['gameloggedin'], $_SESSION['tablePrefix'], $_SESSION['zyklaDir'], $_SESSION['feedbackLink'], $_SESSION['officialGameSiteLink']);
    print "\t</tr></table>\n";
    print "\t</body>\n";
    print "</html>\n";
}
else
{
?>
	<HTML>
		<META http-equiv="refresh" content="0; url=index.php">
		<LINK REL="stylesheet" TYPE="text/css" HREF="css\common.css">
		<BODY>
		</BODY>
	</HTML>
<?php 
}
?>
