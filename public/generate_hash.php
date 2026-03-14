<?php
// Password generation utility
echo "Hash for APS@123:<br>";
echo password_hash('APS@123', PASSWORD_BCRYPT);
?>