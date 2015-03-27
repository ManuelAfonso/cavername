<?php
$obj->Html = "<div class='cvrn-message-list'>";
foreach ($obj->Data as $m)
{			
	$obj->Html .= $m . "<br />" . PHP_EOL;
}
$obj->Html .= "</div>";		
?>