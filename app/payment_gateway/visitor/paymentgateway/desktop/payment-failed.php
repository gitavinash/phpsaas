<?php

require_once 'header.php';

echo "<h1 class='red' >Error</h1><p>Your transaction is failed.</p><p>Order Id : OD" . base64_decode($id) . "</p>";

require_once 'footer.php';