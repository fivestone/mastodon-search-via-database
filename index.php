<?php

require_once ('header.php');

$search_result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_words = isset($_POST["search_words"]) ? $_POST["search_words"] : "";
    $search_words_array = explode(' ', $search_words);
    $search_words_sql = "";

    foreach ($search_words_array as $search_words_1) {
        if ($search_words_1 != '') {
            $search_words_sql = $search_words_sql . " and text like '%" . $search_words_1 . "%'";
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
    $search_types_sql = match ($search_types) {
        'all' => "and s.visibility<=1",
        'mine' => "and s.account_id=" . $_SESSION['account_id'],
        'following' => "and s.visibility<=2 and account_id in (select target_account_id FROM follows where follows.account_id=" . $_SESSION['account_id'] . ")",
        'local' => "and s.visibility<=1 and s.local is true",
        'to_me' => "and s.in_reply_to_account_id=" . $_SESSION['account_id'],
        'favourites' => "and exists (select * from favourites f where f.account_id=" . $_SESSION['account_id'] . " and f.status_id =s.id)",
        default => "and s.visibility<=1",
    };

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

<html>
<head>
    <meta http-equiv="Content-Language" content="zh-cn">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Mastodon Query</title>

    <style>a{TEXT-DECORATION:none;}</style>


</head>
<body>
<div>
    <?php echo $_SESSION['account_name']; ?> - <a href="logout.php">logout</a><hr>
</div>
<form method="post">
    <input type="text" maxlength="50" id="search_words" name="search_words" value="<?php echo $search_words; ?>"> separate keywords with space<br />
    from <input type="date" name="date_start" value="<?=$_POST['date_start'] ?>"> to <input type="date" name="date_end" value="<?=$_POST['date_end'] ?>">
    <select name="time_order" >
        <option value="desc" <?php if($_POST['time_order']!="asc") {echo "selected";} ?>>newer first</option>
        <option value="asc" <?php if($_POST['time_order']=="asc") {echo "selected";} ?>>older first</option>
    </select>
    <br/>

    <input type="radio" name="search_types" value="all" <?php if($_POST['search_types']=="" || $_POST['search_types']=="all") {echo "checked";} ?>>all
    <input type="radio" name="search_types" value="mine" <?php if($_POST['search_types']=="mine") {echo "checked";} ?>>mine
    <input type="radio" name="search_types" value="following" <?php if($_POST['search_types']=="following") {echo "checked";} ?>>following
    <input type="radio" name="search_types" value="local" <?php if($_POST['search_types']=="local") {echo "checked";} ?>>local
    <input type="radio" name="search_types" value="to_me" <?php if($_POST['search_types']=="to_me") {echo "checked";} ?>>to me
    <input type="radio" name="search_types" value="favourites" <?php if($_POST['search_types']=="favourites") {echo "checked";} ?>>favourites
    <input type="radio" disabled name="search_types" value="rt" <?php if($_POST['search_types']=="favourites") {echo "checked";} ?>>my RT
    <br/>
    <input type="checkbox" name="only_reply" value="1" <?php if($_POST['only_reply']=="1") {echo "checked";} ?>>only reply
    <input type="checkbox" name="no_reply" value="1" <?php if($_POST['no_reply']=="1") {echo "checked";} ?>>no reply
    <input type="checkbox" name="has_media" value="1" <?php if($_POST['has_media']=="1") {echo "checked";} ?>>media
    <br/>
    <input type="submit" />
</form>
<div>
    <?php
    while($row = pg_fetch_row($search_result))
    {
        $internal_url = "https://".$INSTANCE_DOMAIN."/web/".$row[5]."/".$row[6];
    ?>
<hr>
        <p>
            <?=$row[2] ?> <a href="<?=$internal_url ?>">⎋</a> <?=$row[5] ?> <a href="<?=$row[1] ?>">🔗</a><br />
            <?=$row[3] ?>
        </p>
    <?php
    }
    ?>
</div>

</body>
</html>

