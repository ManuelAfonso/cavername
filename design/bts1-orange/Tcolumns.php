<?php
$obj->Html = "<div class='cvrn-columns'>";
foreach($this->rows as $row)
{	
	$obj->Html .= "<div class='row'>";
	$nn = 12 / count($row);
	$contador = 1;
	foreach($row as $col)
	{
		if (count($row) === 1) $kls = 'cvrn-column-only';
		elseif ($contador === 1) $kls = 'cvrn-column-first';
		elseif ($contador === count($row)) $kls = ' cvrn-column-last';
		else $kls = 'cvrn-column-mid';
		$obj->Html .= "<div class='col-md-$nn $kls'>$col</div>";
		$contador ++;
	}
	$obj->Html .= "</div>";
}
$obj->Html .= "</div>";
?>