<?php 
/*
	Sida för att skapa användare för tipset
	Author: Mattias Melin
	Changed: 2014-05-15
*/
// Include function collection
include_once("settings.php");
include_once("functions.php");

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
<?php
	$username = $_POST['username'];
	$namn = $_POST['namn'];
	$email = $_POST['email'];
	$pwd = $_POST['pwd'];
    print "\t\t<h1>".$_SESSION['titleText']."</h1>\n";
$sql = "select * from ".$_SESSION['tablePrefix']."tippare where userid='$username'";
if (mysqli_query($_SESSION['link'], $sql) && mysqli_affected_rows($_SESSION['link']) > 0)
{
	print "The user \"$username\" already exists! Pick another userid and try again.<br>\n";
	$error = true;
}
else
{
	if ($namn == "" || $email == "" || $username == "" || $pwd == "")
	{
		print "All fields must be filled, try again.!\n";
		$error = true;
	}
	else
	{
		$sql = "insert into ".$_SESSION['tablePrefix']."tippare (namn, emailadress, userid, pwd) values ('$namn', '$email', '$username', '$pwd')";
		if ($debug)
		{
			print "SQL=$sql\n";
		}
		if (mysqli_query($_SESSION['link'],$sql))
		{
			print "User: \"$username\" has been created!<br>\n";
			print "Pay <b>50 SEK</b> to Mattias Melin.<br>\n";
			print "You can start entering your tip by go to <a href=\"index.php\">the startpage</a> and log in.\n";

            $mailSubject = "Your user for ".$_SESSION['competition']." ".date("Y-m-d H:i");

            // To send HTML mail, you can set the Content-type header. 
            $headers .= "MIME-Version: 1.0\n";
            $headers .= "Content-type: text/plain";
            $headers .= "; charset=UTF-8\n";
            $headers .= "Content-Transfer-Encoding: 8bit\n";
    
            // additional headers 
            $headers .= "From: ".$_SESSION['emailFromText']." <mattias.melin@zykla.net>\n";
            $headers .= "Bcc: mattias.melin@zykla.net\n";
            $headers .= "Reply-To: mattias.melin@ist.com\n";
            $headers .= "X-Mailer: PHP/" . phpversion()."\n";
            
                       
            $message = "Ypur user for ".$_SESSION['competition']." has been created.\n".
 			           "UserId: ".$username."\n".
    			       "Password: ".$pwd."\n".
    			       "Name: ".$namn."\n".
    			       "Email: ".$email."\n";
            
            // and now mail it 
            if ($email != "" && mail($email, $mailSubject, $message, $headers))
            {
                $messageToShow = "<b>Your user details has been sent to $email.</b>";
            }
            else
            {
                $messageToShow = "<b>We were not able to send your user details to \"$email\".<br>Check the email address and contact Mattias Melin to correct it.</b>";
            }
            print "<br>".$messageToShow;
            
		}
		else
		{
			print "Could not create user.\n";
		}
	}
}
if ($error)
{
    print "<form action=\"createuser.php\" method=\"post\">\n";
	print "\t<table>\n";
	print "\t\t<tr>\n";
	print "\t\t\t<td>Name</td>\n";
	print "\t\t\t<td><input type=\"text\" name=\"namn\" size=\"50\" value=\"$namn\"/></td>\n";
	print "\t\t</tr>\n";
	print "\t\t<tr>\n";
	print "\t\t\t<td>Email</td>\n";
	print "\t\t\t<td><input type=\"text\" name=\"email\" size=\"50\" value=\"$email\"/></td>\n";
	print "\t\t</tr>\n";
	print "\t\t<tr>\n";
	print "\t\t\t<td>Username</td>\n";
	print "\t\t\t<td><input type=\"text\" name=\"username\" size=\"50\" value=\"$username\"/></td>\n";
	print "\t\t</tr>\n";
	print "\t\t<tr>\n";
	print "\t\t\t<td>Password</td>\n";
	print "\t\t\t<td><input type=\"password\" name=\"pwd\" size=\"50\"/></td>\n";
	print "\t\t</tr>\n";
	print "\t\t<tr>\n";
	print "\t\t\t<td>&nbsp;</td>\n";
	print "\t\t\t<td><button type=\"submit\">Create user</button></td>\n";
	print "\t\t</tr>\n";
	print "\t</table>\n";
	print "</form>\n";
}
?>		
  	</td>
  	</tr>
    </table>
</body>
</html>
