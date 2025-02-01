<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="./css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <?php
    session_start();

    if(isset($_SESSION['return_msg']))
    {?>
        <div class="alert alert-info"><?php echo $_SESSION['return_msg']; ?><hr></div>
    <?php
        unset($_SESSION['return_msg']);
    }
    ?>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <form action="verify.php" method="post" class="border p-4 bg-light">
                <div class="form-group">
                    <label for="user_email">UserName:</label>
                    <input type="text" class="form-control" id="user_email" name="user_email" required>
                </div>
                <div class="form-group">
                    <label for="user_password">Password:</label>
                    <input type="password" class="form-control" id="user_password" name="user_password" required>
                </div>
                <div class="form-group">
                    <img src="checkcode.php" onclick="this.src='checkcode.php?'+new Date().getTime();" class="img-fluid mb-2">
                    <label for="checkcode">Checkcode:</label>
                    <input type="text" class="form-control" id="checkcode" name="checkcode" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Submit</button>
            </form>
        </div>
    </div>
</div>
<script src="./js/jquery-3.5.1.min.js"></script>
<script src="./js/popper.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
</body>
</html>
