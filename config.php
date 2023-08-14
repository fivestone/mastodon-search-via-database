<?php

// PostgreSQL info
// It is recommented to create a user with read-only previledge, other than the mastodon user.
$DB_HOST        = "127.0.0.1";
$DB_PORT        = "5432";
$DB_NAME        = "mastodon_db";
$DB_USER        = "mastodon_user";
$DB_PASSWORD    = "mastodon_password";

// only used to generate links of toots
$INSTANCE_DOMAIN = "mastodon.fivest.one";

$max_results = 50;

// not used yet, maybe to limit the query options while opened to non-admin users
$is_for_admin   = true;

$connection_string = "host=$DB_HOST port=$DB_PORT dbname=$DB_NAME user=$DB_USER password=$DB_PASSWORD";
?>
