<?php 
    include_once("settings.php");
    include("functions.php");
    // send wml headers 
    header("Content-type: text/vnd.wap.wml"); 
    echo("<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>"); 
    echo("<!DOCTYPE wml PUBLIC \"-//WAPFORUM//DTD WML 1.1//EN\" \"http://www.wapforum.org/DTD/wml_1.1.xml\">");  
?>
<wml>
 <card id="logincard" title="EM-Tipset">
<?php 
    print "<p>Leaderboard efter ".noOfMatchesPlayed($tablePrefix)." av 48 matcher.</p>";
    print "<p>".leaderboard($tablePrefix)."</p>";
?>
 </card>
</wml>