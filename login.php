<html>

<head>
    <title>Login Page</title>
</head>

<body>

<div>
    <?php
    session_start();

    if(isset($_SESSION['return_msg']))
    {?>
        <div><?php echo $_SESSION['return_msg']; ?><hr></div>
    <?php
        unset($_SESSION['return_msg']);
    }
    ?>
            <form action="verify.php" method="post">
                <label>UserName: </label><input type="text" name="user_email" /><br /><br />
                <label>Password: </label><input type="password" name="user_password" /><br/><br />
                <img src="checkcode.php"  onclick="this.src='checkcode.php?'+new Date().getTime();" width="200" height="80"><br/>
                <label>验证码: </label><input type="text" name="checkcode"><br/>
                <input type="submit" value=" Submit " /><br />
            </form>
</div>

</body>

</html>