<?php

    if(isset($_REQUEST['hub_challenge'])) {
        echo $_REQUEST['hub_challenge'];
        exit();
    }

?>