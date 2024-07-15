<?php

if (strpos(ACTIVE_URL, 'hotel-in') !== false) {
    $link_type = 'hotel_search';
    require_once 'hotel-search.php';
}
exit();