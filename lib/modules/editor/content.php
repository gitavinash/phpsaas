<?php

    $bodyContent=str_replace('<div style="text-align: justify;">', "<p>", str_replace('</div><div style="text-align: justify;">', "</p><p>", html_entity_decode($description)));
    $bodyContent=str_replace('<p><br></p>', '', str_replace('</div>', '</p>', str_replace('<div>', '<p>', $bodyContent)));