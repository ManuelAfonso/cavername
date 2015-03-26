<?php
if ('' !== $obj->Titulo)
{
	$novoTitulo = "<span class='cvrn-content-excerpt-title'><a href='$this->read_more_link'>$obj->Titulo</a></span>";
	$obj->Html = preg_replace(CAVERNAME_PREG_H1, $novoTitulo, $obj->Html);	
}
// acrescenta a etiqueta Ler mais ou equivalente
$obj->Html = "<div class='cvrn-content-excerpt'>
$obj->Html
<p><a class='btn btn-default' href='$this->read_more_link' role='button'>".CAVERNAMEs_read_more." &raquo;</a></p>
</div>";		
?>
