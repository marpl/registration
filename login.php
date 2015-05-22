<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Administation</title></head>
<body>
<center>
    <h1>
        <center>Log-in
        </center>
    </h1>
    <?php
    $Username = $_POST[user];
    $Password = $_POST[pass];
    //Συνάρτηση σύνδεσης με τη βάση δεδομένων
    function doDB()
    {
        global $conn;
///CONNECTION
$conn = mysql_connect("localhost", "mary", "1234", "test2") or die(mysql_error());
mysql_select_db("test2", $conn) or die(mysql_error());
}

    function usernameChecker($Username, $Password)
    {
//έλεγχος αν το username του χρήστη δεν υπάρχει ήδη στη βάση
global $conn, $check_un_result;
$check = "select id from admins where Username='$Username' and Password='$Password'";
$check_un_result = mysql_query($check, $conn) or die(mysql_error());
}

    //Έλεγχος αν συμπληρώθηκαν και τα δύο πεδία
if (($_POST[user] == "") || ($_POST[pass] == "")) {
    echo "You have not entered all the required details.<br />" . "Please go back and try again.";
    exit;
}
//σύνδεση στη βάση δεδομένων

doDB();
//έλεγχος οτι το username περιέχεται στη λίστα
usernameChecker($_POST[user],$_POST[pass]);
if (mysql_num_rows($check_un_result) < 1) {

    //εμφάνιση μηνύματος αποτυχίας
echo "<p> Your Username and/or Password You've entered is wrong! Please go back and try again.</p>"; 
}
?>
</center>
</body>
</html>
