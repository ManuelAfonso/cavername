<?php
if (file_exists('cavername-extend.php')) include_once('cavername-extend.php');
/**
 * Definir controlo de erros e mensagens de debug
 */
{
define('CAVERNAME_DEBUG', false); // debug - cria uma lista de mensagens
define('CAVERNAME_DUMP', false); // debug - faz o output do objeto Cavername
function CavernameErrorHandler($errno, $errstr, $errfile, $errline)
{
	// erros não fatais - acrescenta linha na lista de debug 
	if (CAVERNAME_DEBUG) CavernameMensagens::ErroPHP($errno, $errstr, $errfile, $errline);
	return true; // indica ao PHP para não fazer nada com o erro
}
function CavernameShutdown()
{
	$error = error_get_last();
    if ($error !== NULL) 
	{
		// erros fatais - mostra uma página que pode ter a mensagem de erro se estiver em modo debug
		$cavername_error_message = '';
		if (CAVERNAME_DEBUG) $cavername_error_message = sprintf('[%1$s] %2$s %3$s %4$s', $error['type'], $error['message'], $error['file'], $error['line']);
		CavernameStrings::FatalError($cavername_error_message);
    }
}
set_error_handler('CavernameErrorHandler'); // regista uma função para tratar os erros
register_shutdown_function('CavernameShutdown'); // regista uma função a executar quando o sistema termina
ini_set('display_errors', false); // indica que não deve ser feito output dos erros
libxml_use_internal_errors(true); // indica que os erros dos ficheiros XML são tratados pelo script
}
/**
 * Definições que não é obrigatório costumizar
 */
{
	// nome da "base de dados"
	define('CAVERNAME_DB_NAME', 'cavername.db');
	// extensões para a pesquisa de ficheiros
	define('CAVERNAME_EXTENSOES', 'php;htm;html;txt;xml'); 
	// o nome da zona para onde vai o conteúdo a colocar entre <HEAD> e </HEAD>
	define('CAVERNAME_HEAD_ZONE', 'htmlhead'); 
	// o nome da zona para onde vão as mensagens de debug
	define('CAVERNAME_DEBUG_ZONE', 'debugzone'); 
	// o nome da zona para onde vão as mensagens de erro do php
	define('CAVERNAME_ERROR_ZONE', 'errorzone'); 
	// localização dos ficheiros (PODERIA ser fora do site mas complica uso de paths relativos)
	define('CAVERNAME_CONTEUDOS_FOLDER', dirname(__FILE__).'/conteudo/'); 
	// localização dos temas (NÃO PODE ser fora do site porque o browser vai pedir os CSS, JS, etc.)
	define('CAVERNAME_DESIGN_FOLDER', dirname(__FILE__).'/design/'); 
	// macro que será substituída pelo id da página
	define('CAVERNAME_SPECIAL_CONTENT_MAIN', '__maincontent__'); 
	// id do conteúdo para erros 404
	define('CAVERNAME_404', '404');
	// separador classe/função 
	define('CAVERNAME_FUNC_SEP', ':');
	/**
	  * Procurar as macros, por exemplo [!link xpto] e substituir por <!--link xpto-->
	  * /
	  * \[!  texto a procurar
	  * (    inicio de uma sub pattern cujo conteúdo se pode obter em $m[1]
	  * [    início de uma character class que define o tipo de texto a encontrar
	  * ^\]  qualquer caractere excepto ]
	  * ]    fim da character class
	  * +    1 ou mais ocorrências de caracteres do tipo anterior
	  * )    fim da subpattern
	  * \]   texto fixo a procurar \] => ]
	  * /    fim da expressão regular
	  */
	define('CAVERNAME_PREG_MACROS', '/\[!([^\]]+)\]/');
	/**
	  * Procurar o que está antes e depois do body
	  *  /		inicio da expressão regular
	  *  .*		0 ou mais carateres
	  *  <body	texto a encontrar
	  *  [^>]*	qualquer conjunto de carateres excepto >
	  *  >		texto a encontrar
	  *  /		fim da expressão regular
	  *  i		ignorar case
	  *  s		incluir o \n nos carateres representados pelo .
	  */
	define('CAVERNAME_PREG_BEFOREBODY', '/.*<body[^>]*>/is');
	define('CAVERNAME_PREG_AFTERBODY', '/<\/body>.*/is');
	/**
	  * Substituição de funções, o nome da função só pode ter letras e números
	  *   /         inicio da expressão regular
	  *   <!--      texto fixo a procurar 
	  *   (         inicio de uma sub pattern cujo conteúdo se pode obter em $m[1]
	  *   [         início de uma character class que define o tipo de texto a encontrar
	  *   A-Z       letras minúsculas
	  *   a-z       letras minúsculas
	  *   0-9       dígitos
	  *   ]         fim da character class
	  *   +         1 ou mais ocorrências de caracteres do tipo anterior
	  *   )         fim da subpattern
	  *   \s*       0 ou mais espaços
	  *   (         inicio de uma sub pattern cujo conteúdo se pode obter em $m[2]
	  *   .*?       qualquer caractere (0 ou mais) o ponto de interrogação torna a expressão ungreedy
	  *             ou seja, vai tentar apanhar o menor nº de caracteres possível
	  *   )         fim da subpattern
	  *   -->       texto fixo a procurar 
	  *   /         fim da expressão regular
	  */
	define('CAVERNAME_PREG_FUNCTIONS', '/<!--([A-Za-z0-9]+)\s*(.*?)-->/');
	/**
	  * Procura o primeiro heading, seja ele qual for: 1, 2, etc...
	  * /       inicio
	  * <       texto fixo
	  * (       inicio de uma sub pattern cujo conteúdo se pode obter em $m[1]
	  * h       texto fixo
	  * [1-6]   1 a 6
	  * )       fim da subpattern
	  * [^>]*   qualquer conjunto de n carateres exceto >
	  * >       texto fixo
	  * (       inicio de uma sub pattern cujo conteúdo se pode obter em $m[2]
	  * .*?     qualquer caractere (0 ou mais) o ponto de interrogação torna a expressão ungreedy
	  *             ou seja, vai tentar apanhar o menor nº de caracteres possível
	  * )       fim subpattern
	  * <\/     texto fixo </
	  * \1      procura aqui o que tiver sido encontrado eem $m[1]
	  * >       texto fixo
	  *  /	    fim da expressão regular
	  *  i	    ignorar case
	  *  s	    incluir o \n nos carateres representados pelo .
	  */
	define('CAVERNAME_PREG_H1', '/<h1[^>]*>(.*?)<\/h1>/is');
	define('CAVERNAME_PREG_TITLE', '/<title[^>]*>(.*?)<\/title>/is');
	/**
	  *  Procurar as tags IMG separando o conteúdo do SRC
	  *  @		inicio
	  *  (		inicio subpattern 1
	  *  <img	texto fixo
	  *  \s+	um ou mais espaços
	  *  .*?	qualquer caracter 0 ou mais
	  *  src	texto fixo
	  *  \s*	0 ou mais espaços
	  *  =		texto fixo	
	  *  \s*
	  *  [\'"]	pelica ou aspas
	  *  )		fim subpattern
	  *  (		inicio subpattern 2
	  *  .+?	1 ou mais caracteres
	  *  )		fim subpattern
	  *  (		inicio subpattern 3
	  *  [\'"]	pelica ou aspas
	  *  .*?	0 ou mais carateres
	  *  >		texto fixo
	  *  )		fim subpattern
	  *  @		fim da expressão regular
	  *  i	    ignorar case
	  *  s	    incluir o \n nos carateres representados pelo .
	  */
	define('CAVERNAME_PREG_IMGSRC', '@(<img\s+.*?src\s*=\s*[\'"])(.+?)([\'"].*?>)@is');
}
/**
 * Classe singleton que controla o fluxo da preparação dos dados e do output para o cliente.
 */
class Cavername
{
	public $TituloSite = '';
	public $TituloArtigoPrincipal = '';
	public $Idioma = '';
	public $Zonas = array();
	/**
	 * O método que implementa a Singleton Pattern
	 */
	public static function One()
	{
		static $onlyInstance = null;
		if (null === $onlyInstance) {
			$onlyInstance = new Cavername();
		}
		return $onlyInstance;
	}
	/**
	 * Prepara toda a informação necessária para construir a página
	 */
	public function Prepare()
	{
		// isto serve para evitar outputs indesejados
		ob_start(); 
		
		// Carregar valores da "base de dados"
		CavernameDB::Load();
		$this->Idioma = CavernameDB::$Config['idioma'];
		CavernameTema::$Id = CavernameDB::$Config['tema'];
		
		// Definições de sistema
		CavernameDB::AddAlias('cavername-debug', '@CavernameMensagens:MensagensDebug');
		CavernameDB::AddAlias('cavername-bilingue', '@CavernameConteudoTemplateBilingue:Bilingue');

		// Carregar informação do servidor e browser (caminhos, idioma, etc.)
		self::LoadAmbiente();
			
		// Carregar alguns valores que se podem encontrar guardados na sessão (tema, idioma, utilizador)
		self::SessionStart();

		// Carregar as strings "localizáveis" que se usam na aplicação - depois de estar definido o idioma
		CavernameStrings::Set();

		// Carrega e processa os dados da query string
		CavernamePedido::ParseRequest();
		
		// Vai buscar o layout à base de dados em função do pedido e prepara o tema (obtém caminhos) 
		//     - depois de estar definida a página porque é aí que se determina o layout
		CavernameTema::Prepare();
					
		// Prepara o código adicional das funcionalidades extendidas (plugins)		
		if (method_exists('CavernameExtend', 'Registar'))
		{
			CavernameExtend::Registar();
		}
		// Pedir à "base de dados" os conteúdos a usar e depois buscá-los no disco ou executar alguma função especial.
		// Desta forma, transforma-se uma string 'idA;idB;idC' numa lista de objetos criados com esses Ids.
		// Cada conteúdo será carregado em memória como um objeto do tipo CavernameConteudo. 
		foreach(CavernameDB::GetZonasForCurrentRequest() as $idZona => $conteudos)
		{
			$this->Zonas[$idZona] = array();
			$conteudos = explode(';', $conteudos);
			foreach($conteudos as $idConteudo)
			{
				array_push($this->Zonas[$idZona], new CavernameConteudo($idConteudo, $idZona, true)); 
				CavernameRecursiveControl::Clear();
			}			
		}		
		// isto serve para evitar outputs indesejados
		ob_clean();
	}
	/**
	 * Carregar parâmetros do servidor e browser
	 */
	private function LoadAmbiente()
	{
		$selfDir = dirname($_SERVER['PHP_SELF']);
		if ("/" === $selfDir) $selfDir = ""; // para funcionar quando o site está na raíz
		define('CAVERNAME_SELF_DIR', $selfDir);		
		if (CAVERNAME_DEBUG) 
		{
			CavernameMensagens::Debug('URL=' . (!empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
			CavernameMensagens::Debug('PHP_SELF=' . $_SERVER['PHP_SELF']);
			CavernameMensagens::Debug('CAVERNAME_SELF_DIR=' . CAVERNAME_SELF_DIR);
		}		
		// Obter o idioma predefinido a partir do browser considerando o código de 2 letras (apenas para sites multi-idioma)
        if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && '1' === CavernameDB::$Config['multiidioma'] )
        {
			if (CAVERNAME_DEBUG) CavernameMensagens::Debug('HTTP_ACCEPT_LANGUAGE=' . $_SERVER['HTTP_ACCEPT_LANGUAGE']); // en-US,en;q=0.8,pt;q=0.6,es;q=0.4
            $l1 = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $l2 = explode(';', $l1[0]);
            $l3 = explode('-', $l2[0]);
            $this->Idioma = $l3[0];
        }
	}
	/**
	 * Iniciar sessão e carregar valores
	 */
	private function SessionStart()
	{
        session_start();
		if (CAVERNAME_DEBUG) CavernameMensagens::Debug('$_SESSION=' . serialize($_SESSION));
		if (isset($_SESSION['Cavername-Tema']))
		{
			CavernameTema::$Id = $_SESSION['Cavername-Tema'];
		}		
		if (isset($_SESSION['Cavername-Idioma']))
		{
			$this->Idioma = $_SESSION['Cavername-Idioma'];
		}		
		if (isset($_SESSION['Cavername-User']))
		{
			CavernameUser::SetFromSession($_SESSION['Cavername-User']);
		}		
	}	
	/**
	 * "Executa" o template e produz o output. No ficheiro index.php dá para configurar o modo Dump.
	 */
	public function Serve()
	{
		if (true === CAVERNAME_DUMP)
		{
			echo '<pre>';
			print_r(Cavername::One());
			echo '<hr />';
			$a = get_defined_constants(true);
			print_r($a['user']);
			echo '<hr />';
			CavernameMensagens::Dump();
			echo '<hr />';
			CavernameDB::Dump();
			echo '</pre>';
		}
		else
		{			
			CavernameTema::Output();
		}
    }	
	/**
	 * Faz o output dos conteúdos de uma zona. É usado nos templates - Cavername::Out('zona')
	 */
	public static function Out($zone)
	{
		if (array_key_exists($zone, Cavername::One()->Zonas))
		{
			// zonas definidas na estrutura de páginas
			foreach(Cavername::One()->Zonas[$zone] as $conteudo) // CavernameConteudo
			{
				if (CAVERNAME_HEAD_ZONE === $zone)
				{
					// Manipular a tag <TITLE> para incluir o título do Artigo Principal
					$conteudo->Html = preg_replace(CAVERNAME_PREG_TITLE, Cavername::One()->TituloSiteComposto(), $conteudo->Html);	
					$conteudo->Out(false);
				}
				else
				{
					$conteudo->Out(true);
				}
			}
		}
		else
		{
			// Zonas especiais com conteúdo gerado pelo sistema que devem ser construídas o mais tarde possível.
			// No caso das mensagens de debug, por exemplo, a partir do momento em que se chama este código
			// no template, deixa de ser possível acrescentar mais mensagens.
			if (CAVERNAME_DEBUG && CAVERNAME_DEBUG_ZONE === $zone)
			{
				$conteudo = new CavernameConteudo('@CavernameMensagens:MensagensDebug', CAVERNAME_DEBUG_ZONE, true);
				$conteudo->Out(true);
			}
			if (CAVERNAME_DEBUG && CAVERNAME_ERROR_ZONE === $zone)
			{
				$conteudo = new CavernameConteudo('@CavernameMensagens:MensagensErro', CAVERNAME_ERROR_ZONE, true);
				$conteudo->Out(true);
			}
		}
	}
	/**
	 * Constrói uma string com o título do site e o título do artigo dentro da tag <title>
	 */
	private function TituloSiteComposto()
	{
		$s = $this->TituloSite;
		if ('' !== $this->TituloArtigoPrincipal)
		{
			$s .= ' - ' . $this->TituloArtigoPrincipal;
		}
		return sprintf(CAVERNAMEw_title, $s);
	}
	public static function Idioma()
	{
		echo Cavername::One()->Idioma;
	}
}
/**
 * Uma classe para guardar dados do tema e implementar métodos para localizar os ficheiros
 */
class CavernameTema
{
	public static $Id = '';
	private static $layoutPath = '';
	/**
	 * Localiza o ficheiro template na pasta cujo nome é o id do tema.
	 */
	public static function Prepare()
	{	
		// vai buscar o layout à base de dados em função do pedido
		$layout = CavernameDB::GetLayoutForCurrentRequest();

		// procura na pasta do tema 
		if ('' !== self::$Id && '' !== $layout)
		{
			$test = CAVERNAME_DESIGN_FOLDER . self::$Id . '/' . $layout;
			if (file_exists($test))
			{
				self::$layoutPath = $test;
				if (CAVERNAME_DEBUG) CavernameMensagens::Debug('layoutPath=' . $test);			
			}
			else
			{
				if (CAVERNAME_DEBUG) CavernameMensagens::Debug($test . ' not found');
			}			
		}
	}
	/**
	 * Imprime o conteúdo da página - chama o ficheiro do tema se existir, senão faz um output simples para testes
	 */
	public static function Output()
	{
		if ('' !== self::$layoutPath)
		{
			require_once(self::$layoutPath);
		}
		else
		{
			print('<!doctype html><html><head>');
			Cavername::Out(CAVERNAME_HEAD_ZONE);
			print('</head><body>');
			print('<div style="color:red;">'); Cavername::Out(CAVERNAME_ERROR_ZONE); print('</div><hr />');
			print('<div>'); Cavername::Out(CAVERNAME_DEBUG_ZONE); print('</div><hr />');
			foreach(array_keys(Cavername::One()->Zonas) as $idZona)
			{
				if (CAVERNAME_HEAD_ZONE !== $idZona) 
				{
					print('<div>'); Cavername::Out($idZona); print('</div><hr />');
				}
			}
			print('</body></html>');
		}
	}
	/**
	 * Localiza o ficheiro passado por parâmetro na pasta do tema e inclui-o se existir.
	 * É usada pelas classes ICavernameConteudoTemplate para incluir o template
	 * com layout dos excertos, paginação, etc...
	 * Cada um destes templates tem acesso ao conteúdo com a variável $obj
	 */
	public static function IncludeTemplate($templateName)
	{
		if ('' !== self::$Id )
		{
			$caminho = CAVERNAME_DESIGN_FOLDER . self::$Id . '/' . $templateName;
			if (file_exists($caminho))
			{
				return $caminho;
			}
		}		
		return '';
	}
	/**
	 * Devolve o url (pasta) do tema para poder ir buscar CSSs e JSs
	 */
	public static function Url()
	{
		echo dirname( str_replace(dirname(__FILE__), CAVERNAME_SELF_DIR, self::$layoutPath) );
	}
}
/**
 * Esta classe contém os dados do utilizador logado, se existir. 
 */
class CavernameUser
{
    public static $Id = '';
	//private $utilizador_valido = false;
    public static function SetFromSession($id)
    {
		self::$Id = $Name = $id;
		// TODO: obter os restantes dados e validar utilizador
		// $utilizador_valido = true;
    }
}
/**
 * Esta classe tem 2 objetivos:
 * 1. dar mais segurança, tratando os parâmetros
 * 2. centralizar o tratamento de parâmetros para que possam ser facilmente usados noutros locais, tanto para a obtenção de valores como 
 *    para a construção de links.
 * O parâmetro (a) é o id da página a mostrar. Com este id sabemos qual o layout e os conteúdos que devem ser colocados em cada área.
 * O id indica-nos qual o conteúdo a colocar na área principal.
 * Por exemplo: ?a=materiais/pv1999 refere-se ao ficheiro (pv1999.html) dentro da pasta (materiais)
 * O preenchimento das restantes áreas é feito em função do id.
 * Para testar se o id de uma página começa por uma string, pode-se fazer: 0 === strpos($PaginaId, 'materiais'). 
 * Os restantes parâmetros são guardados num array e podem ser acedidos com o méodo Get
 */
class CavernamePedido
{
	private static $data = array();
	public static $PaginaId;
	/**
     * Esta função trata todos os dados passados (GET ou POST)
	 * Copia para variável local limpando os valores de aspas e tags (avoid XSS)
	 */
    public static function ParseRequest()
    {
		//if (CAVERNAME_DEBUG) CavernameMensagens::Debug('$_REQUEST=' . serialize($_REQUEST)); - comentado por causa das injeções
		foreach($_REQUEST as $key => $value)
		{
			// para aceitar parâmetros sem valor (como se fosse um bool)
			if ('' === $value) $value = '1';
			// O strip_tags é usado para prevenir a inserção de código malicioso, por exemplo:
			// http://localhost/dados/mh2/?a=<script>alert('Injected!');</script>
			$key = strip_tags($key);
			$value = strip_tags($value);
			self::$data[$key] = $value;
			if (CAVERNAME_DEBUG) CavernameMensagens::Debug($key.'='.$value);
		}
		// Identificação da página, assumindo por omissão o valor definido na "base de dados"
		self::$PaginaId = CavernameDB::$Config['homepage'];
		if ('' === self::$PaginaId)
		{
			self::$PaginaId = '---';
		}
		if (array_key_exists('a', self::$data)) 
		{
			// retira pontos para garantir que não consegue aceder a pastas superiores e as barras que tiver a mais
			// mesmo que no URL seja passado %2E em vez do ponto, a string self::$data['a'] vem com o ponto.
			//   »»» The superglobals $_GET and $_REQUEST are already decoded: não é preciso usar urldecode()
			self::$PaginaId = str_replace('.', '', trim(self::$data['a'], '/'));
			if (CAVERNAME_DEBUG) CavernameMensagens::Debug('self::$PaginaId=' . self::$PaginaId);
		}
	}
	/**
	 * Esta função devolve a conversão do parâmetro para o tipo pretendido. O tipo é deduzido do tipo da variável $valorDefault
	 */
	public static function Get($zonaId, $paramName, $valorDefault)
	{
		$value = $valorDefault;
		$parameter = $zonaId . '-' . $paramName;
		if (array_key_exists($parameter, self::$data)) 
		{
			if (is_int($valorDefault)) $value = intval(self::$data[$parameter]);
			elseif (is_float($valorDefault)) $value = floatval(self::$data[$parameter]);
			elseif (is_bool($valorDefault)) $value = (strtolower(self::$data[$parameter])=='true' || self::$data[$parameter]=='1');
			else $value = self::$data[$parameter];
		}
		return $value;
	}
	/**
	 * Esta função devolve o URL para a página atual com UM dos parâmetros modificados
	 */
	public static function NewWith($zonaId, $paramName, $valor)
	{
		$copia = $_GET; // usa o $_GET porque o array $data contém os dados do $_REQUEST, incluíndo o $_POST
		$copia[$zonaId . '-' . $paramName] = $valor;
		$newLink = '?';
		foreach($copia as $kp => $vp)
		{
			$newLink .= ('?' === $newLink ? '' : '&') . $kp . '=' . $vp;
		}
		return $newLink;
	}
	/**
     * Cria o URL de uma página, centralizando nesta classe o tratamento do parâmetro (a)
	 * Pode ser passado um parâmetro. Para já é suficiente mas é possível que venha a ser necessário
	 * alterar para um array.
	 */
 	public static function Create($pagId, $zonaId = '', $paramName = '', $valor = '')
	{
		$newLink = ('?a=' . $pagId);		
		if ('' !== $zonaId && '' !== $paramName)
		{
			$newLink .= '&' . $zonaId . '-' . $paramName . ('' !== $valor ? '=' . $valor : '');
		}
		return $newLink;
	}
}
/**
 * Classe responsável por ler e ordenar o ficheiro cavername-db.
 */
class CavernameDB
{
	/**
	 * Configurações gerais (valores por omissão)
	 */
	public static $Config = array('homepage' => '', 
								  'idioma' => '',
								  'multiidioma' => '',
								  'tema' => '');
	private static function trata_config($line)
	{
		list($nome, $valor) = array_pad(explode(' ', $line), 2, '');
		if (array_key_exists(strtolower($nome), self::$Config))
		{
			self::$Config[strtolower($nome)] = $valor;
			return true;
		}
		return false;
	}
	/*
	 * Estrutura das páginas
	 * $pages contém uma lista de elementos (patterns) e para cada um deles vamos ver se se aplica à página atual.
	 * Podem existir vários conteúdos na mesma zona. 
	 * Atribui cada valor encontrado à zona respetiva, eliminado o que lá estiver. 
	 * Nos casos em que haja prefixo ou sufixo (+) acrescenta o valor à zona, formando uma string separada por (;) 
	 * 		(Sendo uma string, é mais fácil de acrescentar no início).	 
	 */
	public static $pages = array();
	private static function trata_pages($line)
	{
		$line = str_replace(array('CAVERNAME_HEAD_ZONE','CAVERNAME_SPECIAL_CONTENT_MAIN'), array(CAVERNAME_HEAD_ZONE, CAVERNAME_SPECIAL_CONTENT_MAIN), $line);
		$p = explode(' ', $line);
		if (count($p) < 2)
		{
			return false;
		}
		$pagina = strtolower($p[0]);
		$zona = $p[1];
		if (false === array_key_exists($pagina, self::$pages))
		{
			self::$pages[$pagina] = array();
		}
		if (count($p) > 2)
		{
			self::$pages[$pagina][$zona] = array_slice($p, 2); // array_slice: retorna os elementos a partir do 3º inclusive
		}
		else
		{
			self::$pages[$pagina][$zona] = array(''); // fica vazia
		}
		return true;
	}
	public static function GetZonasForCurrentRequest()
	{
		$zonasForCurrentRequest = array();
		foreach(self::$pages as $pattern => $zonas)
		{
			if (self::str_like(CavernamePedido::$PaginaId, $pattern))
			{
				foreach($zonas as $zona => $valores)
				{
					foreach ($valores as $index => $vlr)
					{
						if ('' === $vlr) // remove a zona se existir
						{
							if (array_key_exists($zona, $zonasForCurrentRequest))
							{
								array_splice($zonasForCurrentRequest, array_search($zona,array_keys($zonasForCurrentRequest)), 1);
							}							
						}
						elseif ('+' === $vlr[0] || $index > 0) // a partir da 2ª posição tem que acrescentar, senão apagava as anteriores
						{
							$zonasForCurrentRequest[$zona] .= ";" . trim($vlr, '+');
						}
						elseif ('+' === $vlr[strlen($vlr)-1])
						{
							$zonasForCurrentRequest[$zona] = trim($vlr, '+') . ';' . $zonasForCurrentRequest[$zona];
						}
						else
						{
							$zonasForCurrentRequest[$zona] = $vlr;
						}
					}
				}
			}
		}
		return $zonasForCurrentRequest;
	}
	/**
	 * Layout que se deve aplicar a cada página
	 * Tem uma lista onde se podem usar asteriscos, por isso irá considerar a última válida que encontrar, aquando do pedido.
	 */
	private static $layout = array();
	private static function trata_layout($line)
	{
		list($pagina, $layoutFile) = array_pad(explode(' ', $line), 2, '');
		if ('' === $layoutFile)
		{
			return false;
		}
		self::$layout[strtolower($pagina)] = $layoutFile;
		return true;
	}
	public static function GetLayoutForCurrentRequest()
	{
		$aplica_se = '';
		foreach(self::$layout as $pattern => $layoutFile)
		{
			if (self::str_like(CavernamePedido::$PaginaId, $pattern))
			{
				$aplica_se = $layoutFile;
			}
		}
		return $aplica_se;
	}
	/**
	 * Redirecionamentos de um id para outro
	 */
	private static $alias = array();
	private static function trata_alias($line)
	{
		list($origem, $destino) = array_pad(explode(' ', $line), 2, '');
		if ('' === $destino)
		{
			return false;
		}
		self::$alias[strtolower($origem)] = $destino;
		return true;
	}
	public static function GetAlias($id)
	{
		if (array_key_exists(strtolower($id), self::$alias))
		{
			return self::$alias[strtolower($id)];
		}		
		return $id;
	}
	public static function AddAlias($origem, $destino)
	{
		self::$alias[strtolower($origem)] = $destino;
	}
	/**
	 * Lista de conteúdos de confiança
	 */
	private static $trusted = array();
	private static function trata_trusted($line)
	{
		$p = explode(' ', $line);
		if (1 === count($p))
		{
			self::$trusted[] = strtolower($p[0]);
			return true;
		}
		return false;
	}
	public static function IsTrusted($id)
	{
		return in_array(strtolower($id), self::$trusted);
	}
	/**
	 * Método para ler o ficheiro
	 */
	public static function Load()
	{
		if (file_exists(CAVERNAME_DB_NAME))
		{
			$db = file(CAVERNAME_DB_NAME);
			$sec = '';
			foreach($db as $line)
			{
				$line = trim($line);
				if (0 === strlen($line) || '#' === $line[0])
				{
					continue;
				}
				if ('[' === $line[0] && ']' === $line[strlen($line)-1])
				{
					// identificar a secção
					$sec = trim(substr($line, 1, strlen($line)-2));
					continue;
				}
				// Todas as linhas têm uma estrutura semelhante: partes separadas por espaço, tab ou sinal de igual		
				$line = str_replace(array("\t","="), " ", $line);
				// Remover espaços a mais
				while (true)
				{
					$line = str_replace('  ', ' ', $line, $count);
					if (0 === $count) break;
				}
				// Descodificar a linha conforme a secção
				$funk = array('CavernameDB', 'trata_' . strtolower($sec));
				if (is_callable($funk))
				{
					if (false === call_user_func_array($funk, array($line)))
					{
						if (CAVERNAME_DEBUG) CavernameMensagens::Debug("Invalid line [$line] on section [$sec]");
					}				
				}
				else
				{
					if (CAVERNAME_DEBUG) CavernameMensagens::Debug("Unknown section [$sec]");
				}
			}
		}
		else
		{
			if (CAVERNAME_DEBUG) CavernameMensagens::Debug("Missing " . CAVERNAME_DB_NAME);
		}
	}
	/**
	 * Método para usar no Dump
	 */
	public static function Dump()
	{
		print_r(self::$Config);
		print_r(self::$pages);
		print_r(self::$layout);
		print_r(self::$alias);
		print_r(self::$trusted);
	}
	/**
     * $str LIKE $pattern, apenas para terminações em *, por exemplo: organismos/ph LIKE organismos* é igual a TRUE
	 */
	private static function str_like($str, $pattern)
	{
		if ('*' === $pattern)
		{
			return true;
		}
		if ('*' === $pattern[strlen($pattern)-1])
		{
			if (CAVERNAME_DEBUG) CavernameMensagens::Debug($str ."|". $pattern);
			return (strlen($str) >= strlen($pattern)-1 && 0 === substr_compare($str, $pattern, 0, strlen($pattern)-1, true));
		}
		else
		{
			return ($pattern === $str);
		}
	}
}
/**
 * Esta classe contém os dados de um conteúdo e é responsável
 * pela produção do HTML, seja carregando ou executando um ficheiro em disco,
 * seja executando uma função, sempre com base num id que é passado no construtor.
 * O construtor é responsável pelo carregamento dos dados em bruto, ou seja, antes de aplicados os filtros.
 * Esta classe pode ser invocada em vários locais, nomeadamente quando há includes ou excertos.
 */
class CavernameConteudo
{
	public $Zona;
	public $Id;
	public $DivId;
	public $Titulo;
	public $Path = '';
	public $Url = '';
	public $ApplyFilters;
	public $Template;
	public $Idioma;
	
	public $Html = '';
	public $Data;
	public $Extensao = '';

	private $principal = false; 
	/**
	 * O construtor procura e carrega o conteúdo na propriedade $Html. Só aplica filtros e templates se assim for indicado.
	 */
	public function __construct($idConteudo, $idZona, $aplicarFiltrosTemplate = false, $idioma = "")
	{
		// Inicialização
		{
			$this->Template = new CavernameConteudoTemplateNone();
			$this->Zona = $idZona;
			$this->Id = $idConteudo; 
			if ($idioma == "")
			{
				$this->Idioma = Cavername::One()->Idioma;
			}
			else
			{
				$this->Idioma = $idioma;
			}
			// troca a macro CAVERNAME_SPECIAL_CONTENT_MAIN pelo Id da página solicitada
			if (CAVERNAME_SPECIAL_CONTENT_MAIN === $this->Id)
			{
				$this->Id = CavernamePedido::$PaginaId;
			}
			// verifica se se trata da área principal (isto tem que ser feito antes do redirecionamento)
			if ($this->Id === CavernamePedido::$PaginaId)
			{
				$this->principal = true;
			}
			// Verifica se existe um redirecionamento. A função retorna o próprio id se não existir redirecionamento.
			// Isto não está ao nível do pedido porque o conteúdo pode
			// estar referenciado dentro de um outro documento
			$this->Id = CavernameDB::GetAlias($this->Id);
			// por omissão, aplicam-se filtros mas pode-se mudar no próprio conteúdo com a macro <!--nofilters-->
			$this->ApplyFilters = true; 
		}
		// Obter os dados de um ficheiro ou através da execução de uma função.
		// Os dados são guardados na propriedade Html e/ou na propriedade Data.
		{
			$this->DivId = $this->Zona . '--' . str_replace('@', '', str_replace('/', '-', $this->Id)); // valor por omissão
			if ('@' === substr($this->Id, 0, 1))
			{
				$this->executaFuncao();
			}
			else
			{
				$this->loadContentFromFile();				
			}
		}
		// Obter o título do site se for um conteúdo da CAVERNAME_HEAD_ZONE.		
		{
			if (CAVERNAME_HEAD_ZONE === $this->Zona)
			{
				$s = $this->getTitle();
				if ('' !== $s) Cavername::One()->TituloSite = $s;
			}		
			// Obter o título da página de uma tag H1 se existir.
			$this->Titulo = $this->getH1();
			if (true === $this->principal && $this->Titulo !== '')
			{
				Cavername::One()->TituloArtigoPrincipal = $this->Titulo;
			}
		}				
		// Controlo de recursividade - se o artigo solicitado já foi processado é porque é "pai" do objeto atual.
		// Se o voltarmos a processar entraria num loop infinito.
		if (false === CavernameRecursiveControl::TestAndPush($this->Id.$this->Idioma))
		{
			$this->ApplyFilters = false; 
		}
		// Aplica filtros e template se assim foi solicitado
		if ($aplicarFiltrosTemplate)
		{
			$this->AplicarFiltrosGerais();
			$this->AplicarTemplate();
		}
	}
	public function __destruct()
	{
		CavernameRecursiveControl::Pop();
	}	
	/**
	 * Procura pela tag H1 e extrai o conteúdo
	 */
    private function getH1()
    {
		preg_match(CAVERNAME_PREG_H1, $this->Html, $matches);
		if (sizeof($matches) == 2) return strip_tags($matches[1]);
        return '';
    }
	/**
	 * Procura pela tag TITLE e extrai o conteúdo
	 */
    private function getTitle()
    {
		preg_match(CAVERNAME_PREG_TITLE, $this->Html, $matches);
		if (sizeof($matches) == 2) return strip_tags($matches[1]);
        return '';
    }
	/**
	 * As funções são definidas no seguinte formato @classe.metodo e recebem este objeto como parâmetro.
	 * Uma das coisas que pode fazer uma função é definir o template a usar para tratar o Html/Data
	 */
	private function executaFuncao()
	{
		$funk = explode(CAVERNAME_FUNC_SEP, substr($this->Id, 1));
        if (is_callable($funk))
        {
			call_user_func_array($funk, array($this));
        }
        else
        {
			if (CAVERNAME_DEBUG)
			{
				if (CAVERNAME_DEBUG) CavernameMensagens::Debug('executaFuncao: is_callable=false: '. $this->Id);
				$this->Html = $this->Id;
			}
        }
	}
	/**
	 * Procura e carrega/executa o ficheiro. Se for de confiança faz include (executa), senão faz file_get_contents (lê)
	 */
	private function loadContentFromFile()
	{
		// Evitar o acesso a pastas indevidas. Isto é feito no tratamento do pedido mas como 
		// o objeto pode ser criado com um "include" definido noutro conteudo, é necessário fazer aqui também.
		$this->Id = str_replace('.', '', trim($this->Id, '/')); 
		// procurar o ficheiro
		{
			$this->Path = $this->procuraFicheiro();
			if ('' === $this->Path)
			{
				if (CAVERNAME_DEBUG) 
				{
					if (CAVERNAME_DEBUG) CavernameMensagens::Debug('Load failed: ' . $this->Id);
					$this->Html = $this->Id;
				}
				return;
			}
			$this->Url = str_replace(dirname(__FILE__), CAVERNAME_SELF_DIR, $this->Path);
			if (CAVERNAME_DEBUG) CavernameMensagens::Debug('$this->Url: ' . $this->Url);
		}	
		// ler o conteúdo do ficheiro OU executar se for de confiança
		if (CavernameDB::IsTrusted($this->Id))
		{
			ob_start();
			// neste caso, o ficheiro PHP pode produzir output (é apanhado pelo ob_get_contents) ou atribuir
			// valores diretamente ao Html/Data
			// inclusive pode modificar a variável Template e implementar novos ICavernameConteudoTemplate
			include($this->Path);			
			$this->Html = ob_get_contents();			
			ob_end_clean();
		}
		else
		{
			$this->Html = file_get_contents($this->Path);
		}
		// Remover tudo o que estiver fora da body tag, incluíndo a própria tag, 
		// para o caso de se estar a carregar uma página completa.
		$this->Html = preg_replace(CAVERNAME_PREG_BEFOREBODY, '<!--content removed-->', $this->Html);
		$this->Html = preg_replace(CAVERNAME_PREG_AFTERBODY, '<!--content removed-->', $this->Html);	

		// Normalização das macros que se podem escrever de duas formas:
		// [!macro-name macro-parameters] e <!--macro-name macro-parameters-->
		// A primeira é útil para usar em editores de HTML
		$this->Html = preg_replace(CAVERNAME_PREG_MACROS, '<!--$1-->', $this->Html);		
		
		// Verifica se se devem aplicar filtros
		if (false !== strpos($this->Html, '<!--nofilters-->')) 
		{
			$this->ApplyFilters = false;
		}
		
		// Para identificar tipos específicos (templates) com base no conteúdo ou outras manipulações do objeto.
		if (method_exists('CavernameExtend', 'ExtendConteudo'))
		{
			CavernameExtend::ExtendConteudo($this);
		}					
	}
	/**
	 * Fazer algumas substituições no texto e execução de macros
	 */
	public function AplicarFiltrosGerais()
	{	
		if (false === $this->ApplyFilters )
		{
			return;
		}
		// Quando se tratar de um conteúdo lido de um ficheiro vamos tentar corrigir os caminhos das imagens.
		if ('' !== $this->Url)
		{			
			// substitui a expressão <!--docurl--> pela pasta  do ficheiro que foi lido
			$this->Html = str_ireplace('<!--docurl-->', dirname($this->Url) . '/', $this->Html);
			
			// Procura as tags IMG e extrai o atributo SRC. Se for um url relativo acrescenta o url do ficheiro que foi lido
			{
			// <img src="nestapasta.jpg" /> produz o seguinte array M = ('<img src="nestapasta.jpg" />', '<img src="', 'nestapasta.jpg', '" />')
			$FuncFixUrl = 'if(parse_url($m[2], PHP_URL_SCHEME) || substr($m[2],0,1) === \'/\') return $m[0];
						   return $m[1] . \'' . dirname($this->Url) . '/\' . $m[2] . $m[3];';
			$this->Html = preg_replace_callback(CAVERNAME_PREG_IMGSRC, create_function('$m', $FuncFixUrl), $this->Html);
			}
		}		
		// Tratamento de macros que são substituídas pelo resultado da execução de funções com o mesmo nome.
		// O nome das macros só pode ter letras ou números.
		// O número de parâmetros só pode ser 1 (devido à expressão regular), mas a função pode fazer 
		// explode da string de acordo com o separador convencionado.
		// Para que as funções tenham acesso ao conteúdo atual, existe uma referência static na classe CavernameFuncoes.
		$anterior = CavernameFuncoes::GetCurrentCavernameConteudo();
		CavernameFuncoes::SetCurrentCavernameConteudo($this);
		$this->Html = preg_replace_callback(CAVERNAME_PREG_FUNCTIONS, 
		                                    create_function('$m','return CavernameFuncoes::PREG_FunctionsCallback($m[0], $m[1], $m[2]);'), 
											$this->Html);         
		CavernameFuncoes::SetCurrentCavernameConteudo($anterior);
	}
	/**
	 * Construir o html de acordo com o tipo de bloco. 
	 * Nem sempre o tipo de bloco depende do conteúdo. O mesmo conteúdo pode ser mostrado como excerto, paginado ou apenas um índice.
	 */
	public function AplicarTemplate()
	{
		if (false === $this->ApplyFilters || $this->Template instanceof CavernameConteudoTemplateNone)
		{
			return;
		}
		$this->Template->Build($this);
	}
	/**
	 * Procura um ficheiro com base no id, idioma e extensões possíveis.
	 * Retorna o caminho completo do ficheiro.
	 * Ordem de apresentação:
	 *	 {id}.{lang}.{ext}        se {id}.{lang}.{ext} existir
	 *	 {id}.{ext}               se {id}.{ext} existir
	 */
    private function procuraFicheiro()
    {		
		// Procura o ficheiro no idioma definido no sistema	
		$resultado = $this->procuraPorExtensoes(CAVERNAME_CONTEUDOS_FOLDER . $this->Id . '.' . $this->Idioma);		
		// Se não encontrar, pesquisa sem idioma
		if ('' === $resultado) $resultado = $this->procuraPorExtensoes(CAVERNAME_CONTEUDOS_FOLDER . $this->Id);		
		// Se não encontrar, termina - não procura cache nem draft
		if ('' === $resultado)
		{
			$resultado = $this->procuraPorExtensoes(CAVERNAME_CONTEUDOS_FOLDER . CAVERNAME_404 . '.' . $this->Idioma);		
			if ('' === $resultado) $resultado = $this->procuraPorExtensoes(CAVERNAME_CONTEUDOS_FOLDER . CAVERNAME_404);
			return $resultado;
		}				
		/*
		// Verifica se existe alguma versão draft - se encontrar termina: falta testar utilizador
		$pesquisa = $resultado . '.draft';
		if (file_exists($pesquisa))
		{
			return $pesquisa;
		}        
        // Verifica se o ficheiro de cache é mais recente que o ficheiro original - se for termina: falta ver como gerar a cache e para quê.
        $pesquisa = $resultado . '.cache';
        if (file_exists($pesquisa))
        {
			if (filemtime($pesquisa) >= filemtime($resultado))
			{
				return $pesquisa;
			}
			else
			{
				// apaga a cache
				unlink($pesquisa);
			}
        }
		 */
        return $resultado;
    }	
	/**
	 * Procura o primeiro ficheiro pelo nome completo indicado, testando cada uma 
	 * das extensões definidas no sistema
	 */
    private function procuraPorExtensoes($pesquisa)
    {
		static $extensoes = null;
		if (null === $extensoes) $extensoes = explode(';', CAVERNAME_EXTENSOES);
        foreach ($extensoes as $ext)
        {
			$test = $pesquisa . '.' . $ext;
            if (file_exists($test))
            {
				$this->Extensao = $ext;
                return $test;
            }
        }
        return '';
    }
	/**
	 * Faz o output deste conteúdo. Quando vai para dentro do HEAD não pode levar HEAD.
	 */
	public function Out($comDiv = true)
	{
		if($comDiv) printf(CAVERNAMEw_content_item, $this->DivId, $this->Html);		
		else print($this->Html);
	}
}
/**
 * Contém uma lista de funções static usadas na substituição de macros por texto dinâmico.
 * Para além das funções static, existe um array de alternativas que é alimentado no
 * ficheiro cavername-extend
 */
class CavernameFuncoes
{
	/**
	 * Array público de funções, para que se possam acrescentar "métodos" a esta classe
	 * no ficheiro cavername-extend.
	 * Quando se executa a função PREG_FunctionsCallback, se não existir a função pretendida
	 * procura-a no array.
	 */
	static $funcoesExtend = array();
	public static function AddFunction($macroName, $functionName)
	{
		if ('' === $functionName)
		{
			self::$ignore[] = strtolower($macroName);
		}
		else
		{
			self::$funcoesExtend[strtolower($macroName)] = $functionName;
		}
	}
	static $ignore = array('content', // <!--content removed-->
						   'more',
						   'nofilters');
	/**
	 * Isto foi criado para dar acesso ao próprio conteúdo que está 
	 * a ser tratado. 
	 */
	static $currentCavernameConteudo = null;
	public static function SetCurrentCavernameConteudo($obj)
	{
		self::$currentCavernameConteudo = $obj;
	}
	public static function GetCurrentCavernameConteudo()
	{
		return self::$currentCavernameConteudo;
	}
	/**
	 * Devolve o resultado da execução de uma função, se existir.
	 * Se a função não existir, retorna o valor original.
	 */
    public static function PREG_FunctionsCallback($original, $macro, $arg)
    {
		// faz este teste para reduzir o nº de mensagens desnecessárias de debug
		if (in_array(strtolower($macro), self::$ignore)) return $original; 
        //
		if (is_callable(array('CavernameFuncoes', $macro)))
		{
			return call_user_func_array(array('CavernameFuncoes', $macro), array($arg, $original));
		}
		if (array_key_exists(strtolower($macro), self::$funcoesExtend))
		{
			return call_user_func_array(explode(CAVERNAME_FUNC_SEP, self::$funcoesExtend[strtolower($macro)]), array($arg, $original, self::$currentCavernameConteudo));
		}
		if (CAVERNAME_DEBUG) CavernameMensagens::Debug('PREG_FunctionsCallback: is_callable=false: '. $macro . ' em [' . self::$currentCavernameConteudo->Id . ']');
		// para libertar o objeto de memória e fazer o pop do controlo de recursividade.
		return $original;
    }
	/**
	 * Retorna o título do site... usado normalmente no topo, com algum destaque
	 */
    private static function SiteTitle($arg, $original = '')
    {
        return Cavername::One()->TituloSite;
    }
	/**
	 * Retorna o id do conteúdo... usado no conteudo 404
	 *
	 * PRIVATE porque precisa de self::$currentCavernameConteudo
	 */
    private static function IdConteudo($arg, $original = '')
    {
		return self::$currentCavernameConteudo->Id;
    }
	/**
	 * Devolve o link para outro conteúdo (apenas o URL)
	 */
    private static function Link($to, $original = '')
    {
		return CavernamePedido::Create($to);
    }
	/**
	 * Retorna um link para outro artigo, cujo texto é composto pelo título desse artigo
	 * Intencionalmente não aplica filtros nem templates.
	 * se um conteúdo quiser ter título e não o quiser mostrar basta colcoar <h1 style='display:none'>Título</h1>
	 *
	 * PRIVATE porque precisa de self::$currentCavernameConteudo
	 */
    private static function TitleFrom($id, $original)
    {
		$obj = new CavernameConteudo($id, self::$currentCavernameConteudo->Zona, false);
        if ('' === $obj->Titulo)
        {
			if (CAVERNAME_DEBUG) CavernameMensagens::Debug('<h1> not found');
            return $original;
        }
		return sprintf(CAVERNAMEw_link, self::Link($id), $obj->Titulo);
    }
}
/**
 * Esta classe contém uma pilha para fazer o controlo de recursividade infinita.
 * Por acidente pode-se incluir um artigo noutro e vice-versa ao mesmo tempo e o sistema
 * não está preparado para tratar isso.
 */
class CavernameRecursiveControl
{
	static $pilha = array();
	public static function Clear()
	{
		//if (CAVERNAME_DEBUG) CavernameMensagens::Debug(str_repeat('-', max(count(self::$pilha)-1,0)*5) . 'CavernameRecursiveControl::Clear');		
		self::$pilha = array();
	}
	public static function TestAndPush($id)
	{
		$ret = true;
		if (in_array($id, self::$pilha)) 
		{
			if (CAVERNAME_DEBUG) CavernameMensagens::Debug('TestAndPush failed para [' . $id . '] na pilha [' . implode('] ; [', self::$pilha) . ']');
			$ret = false;
		}
		// faz sempre o push porque o destrutor do objeto faz sempre o pop, ou seja, convém que este elemento exista sempre
		//if (CAVERNAME_DEBUG) CavernameMensagens::Debug(str_repeat('-', count(self::$pilha)*5) . 'CavernameRecursiveControl::TestAndPush=' . $id);		
		array_push(self::$pilha, $id);
		return $ret;
	}
	public static function Pop()
	{
		//if (CAVERNAME_DEBUG) CavernameMensagens::Debug(str_repeat('-', (count(self::$pilha)-1)*5) . 'CavernameRecursiveControl::Pop=' . self::$pilha[count(self::$pilha)-1]);		
		array_pop(self::$pilha);
	}
}
/**
 * As classes seguintes implementam tratamentos específicos que é preciso dar aos conteúdos.
 * Por exemplo: obter uma página, um capítulo, um excerto ou transformar um CSV numa tabela.
 * Cada um deles tem uma parte onde trata os dados e depois invoca um template para gerar o HTML.
 *
 * Build e Render vs. apenas Build.
 * Separar o Build e o Render dava muito jeito porque dessa forma todos os conteúdos eram construídos antes 
 * de criar o Html de cada um.
 * Isso permitia, por exemplo, que um conteúdo que dependesse dos restantes (mensagens de debug, p.ex.) só fosse
 * renderizado no fim.
 * Infelizmente isso não finciona porque existem conteúdos que são incluídos noutros, p.ex.:
			<h1>Este é uma página.</h1>
			<p>Tem texto e aqui no meio tem um include.</p>
			[!include sentido]
			<p>E um excerto.</p>
			[!excerpt main]			
 * Neste caso não temos como fazer o render destes sub-conteúdos. Quando se faz o parse do conteúdo principal, o sistema
 *  faz a substituição de [!include sentido] pelo conteúdo com Id=sentido. Não guarda uma instância do objeto {sentido}.
 *  Para que isso fosse possível era preciso fazer um segundo parse.
 */
interface ICavernameConteudoTemplate
{
	public function Build(CavernameConteudo $obj);
}
interface ICavernameConteudoTemplateWithNavigation
{
	public function BuildWithoutNavigation(CavernameConteudo $obj);
	public function SetMainContentTemplate(ICavernameConteudoTemplate $template);
}
class CavernameConteudoTemplateNone implements ICavernameConteudoTemplate
{
	public function Build(CavernameConteudo $obj){}
}
/**
 * Constrói uma lista de mensagens.
 */
class CavernameConteudoTemplateMessageList implements ICavernameConteudoTemplate
{
	public function Build(CavernameConteudo $obj)
	{
		if (count($obj->Data)>0)
		{
			$this->Render($obj);
		}
	}
	// 
	function Render(CavernameConteudo $obj)
	{
		$t = CavernameTema::IncludeTemplate('Tmessage-list.php');
		if ('' === $t)
		{
			$obj->Html = "<div>";
			foreach ($obj->Data as $m)
			{			
				$obj->Html .= $m . "<br />" . PHP_EOL;
			}
			$obj->Html .= "</div>";		
		}
		else
		{
			include($t);
		}
	}
}
/**
 * Classe com listas de mensagens, para uso geral.
 */
class CavernameMensagens
{
	private static $debug = array();
	private static $erros = array();
	// static $user = array(); implementar quando for preciso
	public static function Debug($mensagem)
	{
		self::$debug[] = $mensagem;
	}
	public static function ErroPHP($errno, $errstr, $errfile, $errline)
	{
		self::$erros[] = sprintf('[%1$s] %2$s %3$s %4$s', $errno, $errstr, $errfile, $errline);
	}
	public static function MensagensDebug(CavernameConteudo $obj)
	{	
		$obj->Template = new CavernameConteudoTemplateMessageList();
		$obj->Data = self::$debug;
	}
	public static function MensagensErro(CavernameConteudo $obj)
	{	
		$obj->Template = new CavernameConteudoTemplateMessageList();
		$obj->Data = self::$erros;
	}
	public static function Dump()
	{
		print_r(self::$debug);
		print_r(self::$erros);
	}
}
/**
 * Uma classe onde se faz a definição dos textos usados pelo sistema.
 * Este código fica isolado nesta classe para que se possa colocar em ficheiros separados com mais facilidade se isso se vier a justificar.
 */
class CavernameStrings
{
	public static function Set()
	{
		switch (Cavername::One()->Idioma)
		{
			case 'pt':
			{
				define('CAVERNAMEs_read_more', 'Ler mais');
				define('CAVERNAMEs_pages_first', 'Primeira');
				define('CAVERNAMEs_pages_previous', 'Anterior');
				define('CAVERNAMEs_pages_next', 'Seguinte');
				define('CAVERNAMEs_pages_last', '&Uacute;ltima');
				define('CAVERNAMEs_pages_current', 'P&aacute;gina %1$d de %2$d ');
				define('CAVERNAMEs_texto_completo', 'Texto completo');
				break;
			}
			default:
			{
				define('CAVERNAMEs_read_more', 'Read more');
				define('CAVERNAMEs_pages_first', 'First');
				define('CAVERNAMEs_pages_previous', 'Previous');
				define('CAVERNAMEs_pages_next', 'Next');
				define('CAVERNAMEs_pages_last', 'Last');
				define('CAVERNAMEs_pages_current', 'Page %1$d of %2$d ');		
				define('CAVERNAMEs_texto_completo', 'Full text');
			}
		}
		// Strings usadas para "envolver" elementos para que possam ser tratados no CSS
		// Não precisam de tradução
		{		
		define('CAVERNAMEw_content_item', '<div id=\'%1$s\'>%2$s</div>' . PHP_EOL);
		define('CAVERNAMEw_link', '<a href=\'%1$s\'>%2$s</a>');
		define('CAVERNAMEw_title', '<title>%1$s</title>');
		}
	}
	public static function FatalError($cavername_error_message)
	{
		// esta não depende do idioma... o erro pode dar antes...
		print("<!doctype html><html lang='en'><head><meta charset='utf-8'><title>Fatal Error</title><meta name='viewport' content='width=device-width, initial-scale=1'><style>
* {line-height: 1.2; margin: 0;} html {display: table; font-family: sans-serif; height: 100%;text-align: center;width: 100%;} body {display: table-cell;vertical-align: middle;margin: 2em auto;} h1 {color: #555;font-size: 2em;font-weight: 400;}
</style></head><body><h1>Fatal error</h1><p>Sorry, but it\'s not posible to present the page you were trying to view.</p><p>$cavername_error_message</p></body></html>");
	}
}
?>
