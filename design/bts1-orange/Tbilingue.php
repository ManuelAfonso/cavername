<?php
$obj->Html = "<div class='cvrn-bilingue'>";
foreach ($this->rows as $bloc)
{
	$obj->Html .= "<div class='row'>
				   <div class='col-md-6 cvrn-bilingue-first'>{$bloc[1]}</div>
				   <div class='col-md-6 cvrn-bilingue-second'>{$bloc[2]}</div>
				   </div>";
}
$obj->Html .= "</div>";
?>