<?php

// Extract the challenge value from the request
$challenge = $_REQUEST['hub_challenge'];

// Respond with the challenge value
echo $challenge;

?>