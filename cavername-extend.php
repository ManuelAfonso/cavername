<?php
define('CAVERNAME_PREG_H2', '/<h2[^>]*>(.*?)<\/h2>/is');
class CavernameExtend
{
	/**
	 * Ese método "acrescenta" funções à classe CavernameFuncoes, acrescentando
	 * funcionalidades à substituição de macros.
	 */
	public static function Registar()
	{
		/**
		 * Indicar à função PREG_FunctionsCallback para ignorar estas macros
		 */
		CavernameFuncoes::AddFunction('pagebreak', '');
		CavernameFuncoes::AddFunction('chapters', '');
		/**
		 * Indicar à função PREG_FunctionsCallback para utilizar estas funções na substituição de conteúdo
		 */
		CavernameFuncoes::AddFunction('include', 'CavernameConteudoTemplateInclude:Callback'); 
		CavernameFuncoes::AddFunction('excerpt', 'CavernameConteudoTemplateExcerpt:Callback');		
		CavernameFuncoes::AddFunction('chapterindex', 'CavernameConteudoTemplateChapterIndex:Callback');
	}
	/**
	 * Função chamada depois de ler um documento e que permite
	 * identificar o tipo de template com base no texto ou
	 * outra propriedade do conteúdo.
	 */
	public static function ExtendConteudo(CavernameConteudo $obj)
	{
		if(	false !== strpos($obj->Html, '<!--pagebreak-->'))
		{
			$obj->Template = new CavernameConteudoTemplatePages();
		}		
		elseif(	false !== strpos($obj->Html, '<!--chapters-->'))
		{
			$obj->Template = new CavernameConteudoTemplateChapters();
		}		
		elseif(	false !== strpos($obj->Html, '<!--csvtable-->'))
		{
			// TODO: $obj->Template = new CavernameConteudoTemplateCSVTable();
			// podia ser por extensão do ficheiro mas isso não serviria para um conteudo gerado por PHp (query p.ex.)
		}		
	}
}
/** ========================================================================================================== include
 * Retorna o conteúdo de outro ficheiro (está pensado para pequenos ficheiros). 
 */
class CavernameConteudoTemplateInclude implements ICavernameConteudoTemplate
{
	public static function Callback($id, $original, $parent)
	{
		$obj = new CavernameConteudo($id, $parent->Zona, false);
		$obj->AplicarFiltrosGerais();
		$obj->AplicarTemplate(); // aplica template original para paginar, por exemplo
		$obj->Template = new CavernameConteudoTemplateInclude();
		$obj->AplicarTemplate(); // para acrescentar uma div à volta
		return $obj->Html;
	}
	public function Build(CavernameConteudo $obj)
	{
		$this->Render($obj);
	}
	private function Render(CavernameConteudo $obj)
	{
		$t = CavernameTema::IncludeTemplate('Tinclude.php');
		if ('' === $t)
		{
			$obj->Html = "<div>$obj->Html</div>";		
		}
		else
		{
			include($t);
		}
	}
}
/** ========================================================================================================== excerpt
 * Retorna parte do conteúdo de outro ficheiro, delimitado pela macro <!--more-->
 */
class CavernameConteudoTemplateExcerpt implements ICavernameConteudoTemplate
{
	//
	public static function Callback($id, $original, $parent)
	{ 
		$obj = new CavernameConteudo($id, $parent->Zona, false);
		$obj->AplicarFiltrosGerais();
		$obj->Template = new CavernameConteudoTemplateExcerpt();
		$obj->AplicarTemplate();
		return $obj->Html;
	}
	public function Build(CavernameConteudo $obj)
	{
        $pos = strpos($obj->Html, '<!--more-->');
        if ($pos === false)
        {
			if (false === CAVERNAME_DEBUG) CavernameMensagens::Debug('<!--more--> not found');
            return;
        }
		$this->read_more_link = CavernamePedido::Create($obj->Id);
		$obj->Html = substr($obj->Html, 0, $pos);		
		$this->Render($obj);
	}
	private function Render(CavernameConteudo $obj)
	{
		$t = CavernameTema::IncludeTemplate('Texcerpt.php');
		if ('' === $t)
		{
			// se o conteúdo tiver um título H1, substitui-o por um link
			if ('' !== $obj->Titulo)
			{
				$novoTitulo = "<a href='$this->read_more_link'>$obj->Titulo</a>";
				$obj->Html = preg_replace(CAVERNAME_PREG_H1, $novoTitulo, $obj->Html);	
			}
			// acrescenta a etiqueta Ler mais ou equivalente
			$obj->Html = "<div>$obj->Html<p><a href='$this->read_more_link'>".CAVERNAMEs_read_more."</a></p></div>";		
		}
		else
		{
			include($t);
		}
	}
}
/** ========================================================================================================== ChapterIndex
 * Cria um índice de capítulos
 */
class CavernameConteudoTemplateChapterIndex implements ICavernameConteudoTemplate
{
	public static function Callback($id, $original, $parent)
	{
		$obj = new CavernameConteudo($id, $parent->Zona, false);
		$obj->AplicarFiltrosGerais();
		if (! $obj->Template instanceof CavernameConteudoTemplateChapters)
		{
			if (false === CAVERNAME_DEBUG) CavernameMensagens::Debug('no chapters');
			return $original;
		}
		$obj->Template = new CavernameConteudoTemplateChapterIndex();		
		$obj->AplicarTemplate();
		return $obj->Html;
	}
	public function Build(CavernameConteudo $obj)
	{
        preg_match_all(CAVERNAME_PREG_H2, $obj->Html, $matches, PREG_PATTERN_ORDER);
        if (sizeof($matches) != 2)
        {
			if (false === CAVERNAME_DEBUG) CavernameMensagens::Debug('<h2> not found');
			$obj->Html = '';
            return;
        }      
		$obj->template = 'chapter-index';
		$this->chapter_index_list = array();
        foreach($matches[1] as $k => $h)
        {
			$this->chapter_index_list[] = array(CavernamePedido::Create($obj->Id, $obj->Zona, 'c', $k+1), strip_tags($h));
        }        
		$obj->Html = '';						
		$this->Render($obj);
	}
	private function Render(CavernameConteudo $obj)
	{
		$t = CavernameTema::IncludeTemplate('Tchapter-index.php');
		if ('' === $t)
		{
			$obj->Html = "<div><h1>$obj->Titulo</h1><ul>";
			foreach($this->chapter_index_list as $item)
			{
				$obj->Html .= "<li><a href='$item[0]'>$item[1]</a></li>" . PHP_EOL;
			}        
			$obj->Html .= "</ul></div>" . PHP_EOL;		
		}
		else
		{
			include($t);
		}
	}
}
/** ========================================================================================================== conteúdos paginados
 * Obtém o conteúdo da página requisitada
 */
class CavernameConteudoTemplatePages implements ICavernameConteudoTemplate
{
	public function Build(CavernameConteudo $obj)
	{
		// Este parâmetro é composto por <zona>-p=<nº da página>
		$this->pageNumber = CavernamePedido::Get($obj->Zona, 'p', 1);
		// Quando se passa -1 mostra o texto completo
		if (-1 === $this->pageNumber) 
		{
			$obj->Template = new CavernameConteudoTemplateNone();
			return;
		}
		// obter o texto correspondente à página
		$needle = '<!--pagebreak-->';
		$this->pageCount = substr_count($obj->Html, $needle) + 1;
		$contador = 1;
		$pos_ini = 0;
		$pos_fim = strlen($obj->Html);
		while ($contador <= $this->pageNumber)
		{
			$pos_fim = strpos($obj->Html, $needle, $pos_ini);
			if ($pos_fim === false)
			{
				$pos_fim = strlen($obj->Html);
				break;
			}
			if ($contador == $this->pageNumber)
			{
				break;
			}
			$pos_ini = $pos_fim + strlen($needle);
			$contador ++;		
		}
		$obj->Html = substr($obj->Html, $pos_ini, $pos_fim-$pos_ini);
		// guardar propriedades para navegação 
		$this->page_complete_url = CavernamePedido::NewWith($obj->Zona, 'p', -1);
		$this->page_first_url = $this->page_previous_url = $this->page_next_url = $this->page_last_url = '';
		if ($this->pageNumber > 1)
		{
			$this->page_first_url = CavernamePedido::NewWith($obj->Zona, 'p', 1);
			$this->page_previous_url = CavernamePedido::NewWith($obj->Zona, 'p', $this->pageNumber-1);
		}
		if ($this->pageNumber < $this->pageCount)
		{
			$this->page_next_url = CavernamePedido::NewWith($obj->Zona, 'p', $this->pageNumber+1);
			$this->page_last_url = CavernamePedido::NewWith($obj->Zona, 'p', $this->pageCount);	
		}
		$this->Render($obj);
	}
	private function Render(CavernameConteudo $obj)
	{
		$t = CavernameTema::IncludeTemplate('Tpages.php');
		if ('' === $t)
		{
			$pt = $nf = $np = $nc = $nn = $nl = $tc = '';
			if ($this->pageNumber > 1)
			{
				$nf = "<a href='$this->page_first_url'>".CAVERNAMEs_pages_first."</a>";
				$np = "<a href='$this->page_previous_url'>".CAVERNAMEs_pages_previous."</a>";
				$pt = "<p>$obj->Titulo</p>" . PHP_EOL;
			}
			else
			{
				$nf = CAVERNAMEs_pages_first;
				$np = CAVERNAMEs_pages_previous;
			}
			if ($this->pageNumber < $this->pageCount)
			{
				$nn = "<a href='$this->page_next_url'>".CAVERNAMEs_pages_next."</a>";
				$nl = "<a href='$this->page_last_url'>".CAVERNAMEs_pages_last."</a>";
			}
			else
			{
				$nn = CAVERNAMEs_pages_next;
				$nl = CAVERNAMEs_pages_last;
			}
			$nc = sprintf(CAVERNAMEs_pages_current, $this->pageNumber, $this->pageCount);
			$tc = "<a href='$this->page_complete_url'>".CAVERNAMEs_texto_completo."</a>";
			$obj->Html = $pt 
						 . "<p>$nf | $np | $nc | $nn | $nl | $tc</p>" . PHP_EOL
						 . $obj->Html . PHP_EOL
						 . "<p>$nf | $np | $nc | $nn | $nl | $tc</p>" . PHP_EOL;
		}
		else
		{
			include($t);
		}
	}
}
/** ========================================================================================================== conteúdos com capítulos
 * Obtém o conteúdo do capítulo requisitado
 */
class CavernameConteudoTemplateChapters implements ICavernameConteudoTemplate
{
	public function Build(CavernameConteudo $obj)
	{
		// Este parâmetro é composto por <zona>-c=<nº da capítulo>
		$this->chapter_number = CavernamePedido::Get($obj->Zona, 'c', 1);
		// Quando se passa -1 mostra o texto completo
		if (-1 === $this->chapter_number) 
		{
			$obj->Template = new CavernameConteudoTemplateNone();
			return;
		}
		// obter o texto correspondente ao capítulo
        $inicio = 0;
        $fim = strlen($obj->Html);
        $this->chapter_previous = '';
        $this->chapter_next = '';
        preg_match_all(CAVERNAME_PREG_H2, $obj->Html, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
		$this->chapter_count = sizeof($matches[0]);
        if ($this->chapter_count <= 1)
        {
            return $this->Html;
        }
        if ($this->chapter_count < $this->chapter_number)
        {
            $this->chapter_number = $this->chapter_count;
        }
        $pos = $this->chapter_number - 1;        
        if ($this->chapter_number > 1)
        {
            $inicio = $matches[0][$pos][1];
            $this->chapter_previous = strip_tags($matches[1][$pos-1][0]);
        }
        if ($this->chapter_number < $this->chapter_count)
        {
            $fim = $matches[0][$pos+1][1];
            $this->chapter_next = strip_tags($matches[1][$pos+1][0]);
        }
		$obj->Html = substr($obj->Html, $inicio, $fim - $inicio);
		// guardar propriedades para navegação 
		if ($this->chapter_number > 1)
		{
			$this->chapter_previous_url = CavernamePedido::NewWith($obj->Zona, 'c', $this->chapter_number-1);
		}
		if ($this->chapter_number < $this->chapter_count)
		{		
			$this->chapter_next_url = CavernamePedido::NewWith($obj->Zona, 'c', $this->chapter_number+1);
		}
		$this->chapter_complete_url = CavernamePedido::NewWith($obj->Zona, 'c', -1);
		$this->Render($obj);
	}
	private function Render(CavernameConteudo $obj)
	{
		$t = CavernameTema::IncludeTemplate('Tchapters.php');
		if ('' === $t)
		{
			$np = $nn = $pt = '';
			if ($this->chapter_number > 1)
			{
				$np = "« <a href='$this->chapter_previous_url'>$this->chapter_previous</a> |";
				$pt = "<p>$obj->Titulo</p>" . PHP_EOL;
			}
			if ($this->chapter_number < $this->chapter_count)
			{		
				$nn = "| <a href='$this->chapter_next_url'>$this->chapter_next</a> »";
			}
			$tc = "<a href='$this->chapter_complete_url'>" . CAVERNAMEs_texto_completo . "</a>";
			$obj->Html = $pt 
						 . "<p>$np $tc $nn</p>" . PHP_EOL
						 . $obj->Html . PHP_EOL
						 . "<p>$np $tc $nn</p>" . PHP_EOL; 			
		}
		else
		{
			include($t);
		}
	}
}
?>
