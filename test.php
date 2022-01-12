<?php
$haystack = "SELECT * FROM users WHERE username='username' AND password='password'";
$demo = "sfd' df";
$has_equ = strpos($haystack, '=') !== false;
$has_exm = strpos($haystack, '!') !== false;
$has_and = stripos($haystack, 'and') !== false;
$has_or = stripos($haystack, 'or') !== false;
$has_com1 = strpos($haystack, '"') !== false;
$has_com2 = strpos($demo, "'") !== false;
echo $has_and;
print_r(explode("'",$haystack));
print_r(count(explode("'",$haystack)));
echo 'User IP Address - '.$_SERVER['REMOTE_ADDR'];  

?>