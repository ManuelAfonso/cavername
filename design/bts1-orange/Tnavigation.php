<?php
// nota: só trata um nível de submenus
include("options.php"); // include porque este código é executado no âmbito de uma função! Não se pode fazer include_once() senão dá erro
$buttonCollapse = "";
$divMenu = "<div>";
if (1 === $optionsNavigation[$obj->DivId]['collapse'])
{
	$buttonCollapse = "<button type='button' class='navbar-toggle' data-toggle='collapse' data-target='#nav-$obj->DivId'><span class='icon-bar'></span><span class='icon-bar'></span><span class='icon-bar'></span></button>";
	$divMenu = "<div class='collapse navbar-collapse' id='nav-$obj->DivId'>";
}
$obj->Html = "<!--begin navigation-->
<nav class='navbar {$optionsNavigation[$obj->DivId]['navbar']}'>
<div class='container-fluid'>
<div class='navbar-header'>
{$buttonCollapse}
<a class='navbar-brand {$optionsNavigation[$obj->DivId]['brand']}' href='$this->linkHome'>$this->siteTitle</a>
</div>
{$divMenu}
<ul class='nav navbar-nav {$optionsNavigation[$obj->DivId]['align']}'>". PHP_EOL;

foreach($obj->Data as $lk)
{
	if (count($lk->Submenu) > 0) 
	{
		$obj->Html .= "<li class='dropdown'><a class='dropdown-toggle' data-toggle='dropdown' href='$lk->Link'>$lk->Title<span class='caret'></span></a>". PHP_EOL;
		$obj->Html .= "<ul class='dropdown-menu'>". PHP_EOL;
		foreach($lk->Submenu as $sublk)
		{
			$obj->Html .= "<li><a href='$sublk->Link' $sublk->Target>$sublk->Title</a></li>". PHP_EOL;
		}
		$obj->Html .= "</ul>". PHP_EOL;
	}
	else
	{
		$obj->Html .= "<li><a href='$lk->Link' $lk->Target>$lk->Title</a></li>". PHP_EOL;
	}
}
$obj->Html .= "</ul>
</div>
</div>
</nav>
<!--end navigation-->". PHP_EOL;
?>
