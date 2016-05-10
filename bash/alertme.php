<?php
// the message
$msg = "[aphidbaby]\nAn error has occurred!";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

// send email
mail("kzeidler@gmail.com","My subject",$msg);
?>