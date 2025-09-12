<?php

if (isset($link)) {
    $link = str_replace(SITE_URL, '', $link);
    echo $link;
} else {
    echo "Tets";
}