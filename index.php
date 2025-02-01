<?php

require_once ('header.php');

$search_result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_words = isset($_POST["search_words"]) ? $_POST["search_words"] : "";
    $search_words_array = explode(' ', $search_words);
    $search_words_sql = "";

    foreach ($search_words_array as $search_words_1) {
        if ($search_words_1 != '') {
            $search_words_sql = $search_words_sql . " and (text like '%" . $search_words_1 . "%' or spoiler_text like '%" . $search_words_1 . "%')";
        }
    }

    $date_start_sql = "";
    $date_end_sql = "";
    $time_order_sql = $_POST['time_order'];

    $date_start = $_POST['date_start'];
    if ($date_start != null)
    {
        $date_start_sql = "and s.created_at>='".$date_start."'";
    }
    $date_end = $_POST['date_end'];
    if ($date_end != null)
    {
        $date_end_sql = "and s.created_at<='".$date_end."'";
    }

    $search_types = $_POST['search_types'];
    if ($search_types ==null)
    {
        $search_types = "all";
    }

    $search_types_sql = "";
    switch ($search_types)
    {
        case 'all':
            $search_types_sql = "and s.visibility<=1";
            break;
        case 'mine':
            $search_types_sql = "and s.account_id=" . $_SESSION['account_id'];
            break;
        case 'following':
            $search_types_sql = "and s.visibility<=2 and account_id in (select target_account_id FROM follows where follows.account_id=" . $_SESSION['account_id'] . ")";
            break;
        case 'local':
            $search_types_sql = "and s.visibility<=1 and s.local is true";
            break;
        case 'to_me':
            $search_types_sql = "and s.in_reply_to_account_id=" . $_SESSION['account_id'];
            break;
        case 'favourites':
            $search_types_sql = "and exists (select * from favourites f where f.account_id=" . $_SESSION['account_id'] . " and f.status_id =s.id)";
            break;
        default:
            $search_types_sql = "and s.visibility<=1";
    }

    $attach_sql = "";
    if($_POST['only_reply']=="1")
    {
        $attach_sql = $attach_sql . " and s.reply is true";
    }
    if($_POST['no_reply']=="1")
    {
        $attach_sql = $attach_sql . " and s.reply is false";
    }
    if($_POST['has_media']=="1")
    {
        $attach_sql = $attach_sql . " and exists (select * from media_attachments ma where ma.status_id=s.id)";
    }

    $sql = <<<EOF
select account_id, s.uri, to_char(s.created_at, 'YYYY-MM-DD'), "text", visibility, '@'||a.username||COALESCE('@'||a."domain",''), s.id as account_name
FROM statuses s left join accounts a on s.account_id=a.id 
where reblog_of_id is null $search_words_sql $date_start_sql $date_end_sql $search_types_sql $attach_sql
order by s.created_at $time_order_sql
LIMIT 20;
EOF;

    $db = pg_connect($connection_string);
    $search_result = pg_query($db, $sql);
    if (!$search_result)
    {
        echo pg_last_error($db);
        exit;
    }
    pg_close($db);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <link href="./css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <form action="index.php" method="post" class="border p-4 bg-light">
                <div class="form-group">
                    <label for="logout"><?php echo $_SESSION['account_name']; ?> - <a href="logout.php">logout</a><?php // echo ' - ' + $sql; ?></label>
                </div>
                <div class="form-group">
                    <label for="search_words">Search Words:</label>
                    <input type="text" class="form-control" id="search_words" name="search_words" value="<?php echo $search_words; ?>"> separate keywords with space
                </div>
                <div class="form-group">
                    <label for="date_start">Start Date:</label>
                    <input type="date" class="form-control" id="date_start" name="date_start" value="<?=$_POST['date_start'] ?>">
                </div>
                <div class="form-group">
                    <label for="date_end">End Date:</label>
                    <input type="date" class="form-control" id="date_end" name="date_end" value="<?=$_POST['date_end'] ?>">
                </div>
                <div class="form-group">
                    <label for="time_order">Time Order:</label>
                    <select class="form-control" id="time_order" name="time_order">
                        <option value="asc" <?php if($_POST['time_order']=="asc") {echo "selected";} ?>>Ascending</option>
                        <option value="desc" <?php if($_POST['time_order']!="asc") {echo "selected";} ?>>Descending</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="search_types">Search Types:</label><br>
                    <input type="radio" name="search_types" value="all" <?php if($_POST['search_types']=="" || $_POST['search_types']=="all") {echo "checked";} ?>>all
                    <input type="radio" name="search_types" value="mine" <?php if($_POST['search_types']=="mine") {echo "checked";} ?>>mine
                    <input type="radio" name="search_types" value="following" <?php if($_POST['search_types']=="following") {echo "checked";} ?>>following
                    <input type="radio" name="search_types" value="local" <?php if($_POST['search_types']=="local") {echo "checked";} ?>>local
                    <input type="radio" name="search_types" value="to_me" <?php if($_POST['search_types']=="to_me") {echo "checked";} ?>>to me
                    <input type="radio" name="search_types" value="favourites" <?php if($_POST['search_types']=="favourites") {echo "checked";} ?>>favourites
                    <input type="radio" disabled name="search_types" value="rt" <?php if($_POST['search_types']=="favourites") {echo "checked";} ?>>my RT
                </div>
                <div class="form-group">
                    <label for="attach_sql">Attachments:</label><br>
                    <input type="checkbox" name="only_reply" value="1" <?php if($_POST['only_reply']=="1") {echo "checked";} ?>>only reply
                    <input type="checkbox" name="no_reply" value="1" <?php if($_POST['no_reply']=="1") {echo "checked";} ?>>no reply
                    <input type="checkbox" name="has_media" value="1" <?php if($_POST['has_media']=="1") {echo "checked";} ?>>media
                </div>
                <button type="submit" class="btn btn-primary btn-block">Search</button>
            </form>
        </div>
    </div>
</div>

<div class="container mt-5">
<?php
    while($row = pg_fetch_row($search_result))
    {
        $internal_url = "https://".$INSTANCE_DOMAIN."/web/".$row[5]."/".$row[6];
    ?>
    <div class="card mb-3">
        <div class="card-body">
            <p class="card-text">
            <?=$row[2] ?> <a href="<?=$internal_url ?>">⎋</a> <?=$row[5] ?> <a href="<?=$row[1] ?>">🔗</a><br>
            <?=$row[3] ?>
            </p>
        </div>
    </div>
    <?php
    }
    ?>
</div>


<script src="./js/jquery-3.5.1.min.js"></script>
<script src="./js/popper.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
</body>
</html>
