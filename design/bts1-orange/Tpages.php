<?php
if ($this->pageNumber > 1)
{
	$classefp = "";
	$pt = "<div class='cvrn-pages-title'>$obj->Titulo</div>" . PHP_EOL;
}
else
{
	$classefp = " class='disabled'";
	$pt = '';
}
if ($this->pageNumber < $this->pageCount)
{
	$classenl = "";
}
else
{
	$classenl = " class='disabled'";
}
$nf = "<li$classefp><a href='$this->page_first_url'>".CAVERNAMEs_pages_first."</a></li>";
$np = "<li$classefp><a href='$this->page_previous_url'>".CAVERNAMEs_pages_previous."</a></li>";
$nc = "<li class='disabled'><a href='#'>".sprintf(CAVERNAMEs_pages_current, $this->pageNumber, $this->pageCount). "</a></li>";
$nn = "<li$classenl><a href='$this->page_next_url'>".CAVERNAMEs_pages_next."</a></li>";
$nl = "<li$classenl><a href='$this->page_last_url'>".CAVERNAMEs_pages_last."</a></li>";
$tc = "<li><a href='$this->page_complete_url'>".CAVERNAMEs_texto_completo."</a></li>";
$obj->Html = $pt 
			 . "<nav><ul class='pagination navbar-right'>$nf$np$nc$nn$nl$tc</ul></nav>" . PHP_EOL
			 . "<div class='cvrn-pages-page'>$obj->Html</div>" . PHP_EOL
			 . "<nav><ul class='pagination navbar-right'>$nf$np$nc$nn$nl$tc</ul></nav>" . PHP_EOL;	 
?>
