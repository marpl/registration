<?php

function doDB()
{
    global $conn;
//σύνδεση στο server και επιλογή της βάσης δεδομένων
    $conn = mysql_connect("localhost", "mary", "1234", "test2") or die(mysql_error());
    mysql_select_db("test2", $conn) or die(mysql_error());
}

function emailChecker($Email)
{
//έλεγχος αν η διεύθυνση email του χρήστη δεν υπάρχει ήδη στη βάση
    global $conn, $check_result;
    $check = "select id from admins where Email='$Email'";
    $check_result = mysql_query($check, $conn) or die(mysql_error());
}

function usernameChecker($Username)
{
//έλεγχος αν το username του χρήστη δεν υπάρχει ήδη στη βάση
    global $conn, $check_un_result;
    $check = "select id from admins where Username='$Username'";
    $check_un_result = mysql_query($check, $conn) or die(mysql_error());
}

//η ακόλουθη if εξακριβώνει άν ο χρήστης θέλει να δει τη φόρμα ή οχι
if ($_POST[op] != "ds") {
//αν ναι δημιουργία της ενότητας για την εμφάνιση της φόρμας
    $display_block = "<form method=POST action=\"$_SERVER[PHP_SELF]\">
<p><strong> Username: </strong><br>
<input type=text name=\"Username\" size=40 maxlength=150>
<p><strong> Email Address: </strong><br>
<input type=email name=\"Email\" size=40 maxlength=150>
<p><strong> Password: </strong><br>
<input type=password name=\"Password\" size=40 maxlength=150>
<p><strong> Enter Your Password Again: </strong><br>
<input type=password name=\"Password2\" size=40 maxlength=150>
<p><strong> Type of User: </strong><br>
<input type=radio name=\"type\" value=\"admin\" checked> Administrator
<input type=radio name=\"type\" value=\"user\"> User
<p><strong> Action: </strong><br>
<input type=radio name=\"action\" value=\"sub\" checked> Subscribe
<input type=radio name=\"action\" value=\"unsub\"> Unsubscribe

<input type=\"hidden\" name=\"op\" value=\"ds\">
<p><input type=submit name=\"submit\" value=\"Submit Form\"></p><p><a href=\"index . html\" /><input type=button name=\"back\" value=\"Back\"></a></p>
</form>";
} else if (($_POST[op] == "ds
") && ($_POST[action] == "sub")
) {
//προσπάθεια εγγραφής νέου συνδρομητή στη λίστα και επικύρωση του username, email και κωδικού
    if (($_POST[Username] == "") || ($_POST[Email] == "") || ($_POST[Password] == "") || ($_POST[Password2] == "")) {
        echo "You have not entered all
the required details.<br />" . "Please go back and try again.";
        exit;
    } //έλεγχος για το αν είναι οι ίδιοι οι passwords και αν είναι μεταξύ 6 και 16 ψηφία.
    else if ((strlen($_POST[Password]) < 6) || (strlen($_POST[Password]) > 16)) {
        echo "Your password must
be between 6 and 16 characters. Please go back and try again.";
        exit;
    } else if ($_POST[Password] != $_POST[Password2]) {
        echo "Your passowrds you've entered doesn't match. Please go back and try again.";
        exit;
    }
//έλεγχος για το αν είναι οι ίδιοι οι pass

//σύνδεση στη βάση δεδομένων
    doDB();
//έλεγχος οτι το username περιέχεται στη λίστα
    usernameChecker($_POST[Username]);

//έλεγχος οτι η διέυθυνση email περιέχεται στη λίστα
    emailChecker($_POST[Email]);
//εύρεση του αριθμού των αποτελεσμάτων εκτέλεσης ενέργειας
    if ((mysql_num_rows($check_result) < 1) && (mysql_num_rows($check_un_result) < 1)) {
//προσθήκη εγγραφής
//$sql="insert into admins values('','$_POST[Username]','$_POST[Password]','$_POST[Email]')";
//$result = mys
        ql_query($sql, $conn) or die(mysql_error());
//$display_block = "<p> Thanks for signing up.</p>"; 
//αποστολή email
        $name = $_POST[Username];
        $email = $_POST[Email];
        $password = $_POST[Password];
        if ($_POST[type] == "admin") {
            $new = "Administrator";
        } else if ($_POST
            [type] == "user"
        ) {
            $new = "User";
        }
        $to = "mary@marybytes.com";
        $subject = "New registration";
        $mailcontent = "Customer name: " . $name . "\n" . "Customer email: " . $email . "\n" . "Customer password: " . $password . "\n" . "Customer type:\n" . $new . "\n";
//κλήση συνάρτησης mail() για αποστολή αλληλογραφίας
        mail($to, $subject, $mailcontent);

        $display_block = "<p> Your registration form was successfully sent for approval! Please check your email shortly.</p>";
    } else {
//εμφάνιση μηνύματος αποτυχίας
        $display_block = "<p>The Username
and/or Email you entered already exists! Please go back and try again.</p>";
    }
} else if (($_POST[op] == "ds") && ($_POST[action] == "unsub")) {
//προσπάθεια διαγραφής από τη λίστα και επικύρωση της διεύθυνσης email
    if (($_POST[Username] == "") && ($_POST[Email] == ""
        ) && ($_POST[Password] == "") && ($_POST[Password2] == "")
    ) {
        header("Location: manage.php");
        exit;
    }
//σύνδεση στη βάση δεδομένων
    doDB();
//έλεγχος οτι η διεύθυνση email και το username περιέχονται στη λίστα
    emailChecker($_POST[Email]);
    usernameChecker($_POST[Username]);
//εύρεση του αριθμού των αποτελεσμάτων και εκτέλεση ενέργειας
    if ((mysql_num_rows($check_result) < 1) && (mysql_num_rows($check_un_result) < 1)) {
//εμφάνιση failure message
        $display_block = "<p>Couldn't find your username and/or email address!</p> <p>No action was taken.</p>";
    } else {

//διαγραφή διεύθυνσης email και username από τη βάση
        $id = mysql_result($check_result, 0, "id");
        $sql = "delete from admins where id='$id'";
        $result = mysql_query($sql, $conn) or die(mysql_error());
        $display_block = "<p> You're unub
scribed!</p>";
    }
}
?>
<html>
<head>
    <meta charset="utf
-
8">
    <title>Register New Admin/
        User
    </title></head>
<body><h1>Subscribe/Unsubscribe</h1>
<?php
echo "$display_block";
?>
</body>
</html>
