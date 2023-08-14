<?php

if (!($_SERVER['REQUEST_METHOD'] === 'POST'))
{
    header("Location: login.php");
    exit;
}

session_start();

if (strtolower($_SESSION["checkcode"]) != strtolower($_POST["checkcode"]))
{
    $_SESSION['return_msg'] = "验证码错误。";
    header("Location: login.php");
    exit;
}

$user_email = $_POST["user_email"];
$user_password = $_POST["user_password"];

if ($user_email == "" or $user_password == "")
{
    $_SESSION['return_msg'] = "login error.";
    header("Location: login.php");
    exit;
}

    $sql = <<<EOF
select u.account_id, u.encrypted_password,'@'||a.username||COALESCE('@'||a."domain",'') 
from users u left join accounts a on u.account_id =a.id 
where email='$user_email';
EOF;

require_once ('config.php');

$db = pg_connect($connection_string);
$search_result = pg_query($db, $sql);
if (!$search_result) {
    echo pg_last_error($db);
    exit;
}
pg_close($db);

if (!($row = pg_fetch_row($search_result)))
{
    $_SESSION['return_msg'] = "User error.";
    header("Location: login.php");
    exit;
}

if (password_verify($user_password, $row[1]) == false)
{
    $_SESSION['return_msg'] = "Password error.";
    header("Location: login.php");
    exit;
}

$_SESSION['account_id'] = $row[0];
$_SESSION['account_name'] = $row[2];

header("Location: index.php");
exit;

?>