<?php

$description = html_entity_decode($description);
$description = preg_replace('/<div[^>]*class=["\'][^"\']*rte-selection-tools-floating[^"\']*["\'][^>]*>.*?<\/div>/is', '', $description);
$description = preg_replace('/<style[^>]*id=["\']rte-selection-tools-style["\'][^>]*>.*?<\/style>/is', '', $description);
$description = preg_replace('/<div[^>]*id=["\']toolbar-[^"\']+["\'][^>]*>.*?<\/div>/is', '', $description);
$description = preg_replace('/\scontenteditable=["\'][^"\']*["\']/i', '', $description);
$description = preg_replace('/\sclass=["\']\s*rte-selection-[^"\']*["\']/i', '', $description);

$bodyContent = str_replace('<div style="text-align: justify;">', "<p>", str_replace('</div><div style="text-align: justify;">', "</p><p>", $description));
$bodyContent = str_replace('<p><br></p>', '', str_replace('</div>', '</p>', str_replace('<div>', '<p>', $bodyContent)));
