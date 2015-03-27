<?php
$pt = '';
if ($this->chapter_number > 1)
{
	$pt = "<div class='cvrn-pages-title'>$obj->Titulo</div>" . PHP_EOL;
	$np = "<li class='previous'><a href='$this->chapter_previous_url'><span aria-hidden='true'>&larr;</span>&nbsp;$this->chapter_previous</a></li> ";
}
else
{
	$np = "<li class='previous'><a href='$this->chapter_complete_url'>" . CAVERNAMEs_texto_completo . "</a></li> ";
}
if ($this->chapter_number < $this->chapter_count)
{		
	$nn = "<li class='next'><a href='$this->chapter_next_url'>$this->chapter_next&nbsp;<span aria-hidden='true'>&rarr;</span></a></li> ";	
}
else
{
	$nn = '';
}
$obj->Html = $pt 
			 . "<nav><ul class='pager'>$np $nn</ul></nav>" . PHP_EOL
			 . "<div class=\'cvrn-chapters-page\'>$obj->Html</div>" . PHP_EOL
			 . "<nav><ul class='pager'>$np $nn</ul></nav>" . PHP_EOL; 			
?>