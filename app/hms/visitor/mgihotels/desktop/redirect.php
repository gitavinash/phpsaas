<?php

$location = strtolower($location);

$newURL = "/{$location}/hotel-in-{$location}/?date={$date}&room={$room}&guests={$guests}";

header("Location: {$newURL}", TRUE, 301);
exit();