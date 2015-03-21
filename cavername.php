<?php
/**
 * Definir controlo de erros e mensagens de debug
 */
{
define('CAVERNAME_DEBUG', false); // debug - cria uma lista de mensagens
define('CAVERNAME_DUMP', false); // debug - faz o output do objeto Cavername
ini_set('display_errors', false); // indica que n�o deve ser feito output dos erros
set_error_handler('CavernameErrorHandler'); // regista uma fun��o para tratar os erros
register_shutdown_function('CavernameShutdown'); // regista uma fun��o a executar quando o sistema termina
function CavernameErrorHandler($errno, $errstr, $errfile, $errline)
{
	// erros n�o fatais - acrescenta linha na lista de debug 
	if (CAVERNAME_DEBUG) CavernameMensagens::ErroPHP($errno, $errstr, $errfile, $errline);
	return true; // indica ao PHP para n�o fazer nada com o erro
}
function CavernameShutdown()
{
	$error = error_get_last();
    if ($error !== NULL) 
	{
		// erros fatais - mostra uma p�gina que pode ter a mensagem de erro se estiver em modo debug
		$cavername_error_message = '';
		if (CAVERNAME_DEBUG) $cavername_error_message = sprintf('[%1$s] %2$s %3$s %4$s', $error['type'], $error['message'], $error['file'], $error['line']);
		CavernameStrings::FatalError($cavername_error_message);
    }
}
}
/**
 * Defini��es que n�o � obrigat�rio costumizar
 */
{
	// nome da "base de dados"
	define('CAVERNAME_DB_NAME', 'cavername.db');
	// extens�es para a pesquisa de ficheiros
	define('CAVERNAME_EXTENSOES', 'php;htm;html;txt'); 
	// o nome da zona para onde vai o conte�do a colocar entre <HEAD> e </HEAD>
	define('CAVERNAME_HEAD_ZONE', 'htmlhead'); 
	// o nome da zona para onde v�o as mensagens de debug
	define('CAVERNAME_DEBUG_ZONE', 'debugzone'); 
	// o nome da zona para onde v�o as mensagens de erro do php
	define('CAVERNAME_ERROR_ZONE', 'errorzone'); 
	// localiza��o dos ficheiros (PODERIA ser fora do site mas complica uso de paths relativos)
	define('CAVERNAME_CONTEUDOS_FOLDER', dirname(__FILE__).'/conteudo/'); 
	// localiza��o dos temas (N�O PODE ser fora do site porque o browser vai pedir os CSS, JS, etc.)
	define('CAVERNAME_DESIGN_FOLDER', dirname(__FILE__).'/design/'); 
	// identifica��o para especificar o layout de uma p�gina nas regras de constru��o da p�gina
	define('CAVERNAME_LAYOUT_PSEUDOZONE', '__layout__'); 
	// macro que ser� substitu�da pelo id da p�gina
	define('CAVERNAME_SPECIAL_CONTENT_MAIN', '__maincontent__'); 
	// usado em algumas convers�es
	define('CAVERNAME_ENCODING', 'UTF-8');
	// id do conte�do para erros 404
	define('CAVERNAME_404', '404');
	// separador classe/fun��o 
	define('CAVERNAME_FUNC_SEP', ':');
	/**
	  * Procurar as macros, por exemplo [!link xpto] e substituir por <!--link xpto-->
	  * /
	  * \[!  texto a procurar
	  * (    inicio de uma sub pattern cujo conte�do se pode obter em $m[1]
	  * [    in�cio de uma character class que define o tipo de texto a encontrar
	  * ^\]  qualquer caractere excepto ]
	  * ]    fim da character class
	  * +    1 ou mais ocorr�ncias de caracteres do tipo anterior
	  * )    fim da subpattern
	  * \]   texto fixo a procurar \] => ]
	  * /    fim da express�o regular
	  */
	define('CAVERNAME_PREG_MACROS', '/\[!([^\]]+)\]/');
	/**
	  * Procurar o que est� antes e depois do body
	  *  /		inicio da express�o regular
	  *  .*		0 ou mais carateres
	  *  <body	texto a encontrar
	  *  [^>]*	qualquer conjunto de carateres excepto >
	  *  >		texto a encontrar
	  *  /		fim da express�o regular
	  *  i		ignorar case
	  *  s		incluir o \n nos carateres representados pelo .
	  */
	define('CAVERNAME_PREG_BEFOREBODY', '/.*<body[^>]*>/is');
	define('CAVERNAME_PREG_AFTERBODY', '/<\/body>.*/is');
	/**
	  * Substitui��o de fun��es, o nome da fun��o s� pode ter letras e n�meros
	  *   /         inicio da express�o regular
	  *   <!--      texto fixo a procurar 
	  *   (         inicio de uma sub pattern cujo conte�do se pode obter em $m[1]
	  *   [         in�cio de uma character class que define o tipo de texto a encontrar
	  *   A-Z       letras min�sculas
	  *   a-z       letras min�sculas
	  *   0-9       d�gitos
	  *   ]         fim da character class
	  *   +         1 ou mais ocorr�ncias de caracteres do tipo anterior
	  *   )         fim da subpattern
	  *   \s*       0 ou mais espa�os
	  *   (         inicio de uma sub pattern cujo conte�do se pode obter em $m[2]
	  *   .*?       qualquer caractere (0 ou mais) o ponto de interroga��o torna a express�o ungreedy
	  *             ou seja, vai tentar apanhar o menor n� de caracteres poss�vel
	  *   )         fim da subpattern
	  *   -->       texto fixo a procurar 
	  *   /         fim da express�o regular
	  */
	define('CAVERNAME_PREG_FUNCTIONS', '/<!--([A-Za-z0-9]+)\s*(.*?)-->/');
	/**
	  * Procura o primeiro heading, seja ele qual for: 1, 2, etc...
	  * /       inicio
	  * <       texto fixo
	  * (       inicio de uma sub pattern cujo conte�do se pode obter em $m[1]
	  * h       texto fixo
	  * [1-6]   1 a 6
	  * )       fim da subpattern
	  * [^>]*   qualquer conjunto de n carateres exceto >
	  * >       texto fixo
	  * (       inicio de uma sub pattern cujo conte�do se pode obter em $m[2]
	  * .*?     qualquer caractere (0 ou mais) o ponto de interroga��o torna a express�o ungreedy
	  *             ou seja, vai tentar apanhar o menor n� de caracteres poss�vel
	  * )       fim subpattern
	  * <\/     texto fixo </
	  * \1      procura aqui o que tiver sido encontrado eem $m[1]
	  * >       texto fixo
	  *  /	    fim da express�o regular
	  *  i	    ignorar case
	  *  s	    incluir o \n nos carateres representados pelo .
	  */
	define('CAVERNAME_PREG_H1', '/<h1[^>]*>(.*?)<\/h1>/is');
	define('CAVERNAME_PREG_TITLE', '/<title[^>]*>(.*?)<\/title>/is');
	/**
	  *  Procurar as tags IMG separando o conte�do do SRC
	  *  @		inicio
	  *  (		inicio subpattern 1
	  *  <img	texto fixo
	  *  \s+	um ou mais espa�os
	  *  .*?	qualquer caracter 0 ou mais
	  *  src	texto fixo
	  *  \s*	0 ou mais espa�os
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
	  *  @		fim da express�o regular
	  *  i	    ignorar case
	  *  s	    incluir o \n nos carateres representados pelo .
	  */
	define('CAVERNAME_PREG_IMGSRC', '@(<img\s+.*?src\s*=\s*[\'"])(.+?)([\'"].*?>)@is');
}
if (file_exists('cavername-extend.php')) include_once('cavername-extend.php');
/**
 * Classe singleton que controla o fluxo da prepara��o dos dados e do output para o cliente.
 */
class Cavername
{
	public $TituloSite = '';
	public $TituloArtigoPrincipal = '';
	public $Idioma = '';
	public $Zonas = array();
	/**
	 * O m�todo que implementa a Singleton Pattern
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
	 * Prepara toda a informa��o necess�ria para construir a p�gina
	 */
	public function Prepare()
	{
		// isto serve para evitar outputs indesejados
		ob_start(); 
		
		// Carregar valores da "base de dados"
		CavernameDB::Load();
		$this->Idioma = CavernameDB::$Config['idioma'];
		CavernameTema::$Id = CavernameDB::$Config['tema'];
		
		// Defini��es de sistema
		// CavernameDB::AddAlias('cavername-debug', '@CavernameMensagens:MensagensDebug');

		// Carregar informa��o do servidor e browser (caminhos, idioma, etc.)
		self::LoadAmbiente();
			
		// Carregar alguns valores que se podem encontrar guardados na sess�o (tema, idioma, utilizador)
		self::SessionStart();

		// Carregar as strings "localiz�veis" que se usam na aplica��o - depois de estar definido o idioma
		CavernameStrings::Set();

		// Carrega e processa os dados da query string
		CavernamePedido::ParseRequest();
		
		// Vai buscar o layout � base de dados em fun��o do pedido e prepara o tema (obt�m caminhos) 
		//     - depois de estar definida a p�gina porque � a� que se determina o layout
		CavernameTema::Prepare();
					
		// Prepara o c�digo adicional das funcionalidades extendidas (plugins)		
		if (method_exists('CavernameExtend', 'Registar'))
		{
			CavernameExtend::Registar();
		}
		// Pedir � "base de dados" os conte�dos a usar e depois busc�-los no disco ou executar alguma fun��o especial.
		// Desta forma, transforma-se uma string 'idA;idB;idC' numa lista de objetos criados com esses Ids.
		// Cada conte�do ser� carregado em mem�ria como um objeto do tipo CavernameConteudo. 
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
	 * Carregar par�metros do servidor e browser
	 */
	private function LoadAmbiente()
	{
		$selfDir = dirname($_SERVER['PHP_SELF']);
		if ("/" === $selfDir) $selfDir = ""; // para funcionar quando o site est� na ra�z
		define('CAVERNAME_SELF_DIR', $selfDir);		
		if (CAVERNAME_DEBUG) 
		{
			CavernameMensagens::Debug('URL=' . (!empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
			CavernameMensagens::Debug('PHP_SELF=' . $_SERVER['PHP_SELF']);
			CavernameMensagens::Debug('CAVERNAME_SELF_DIR=' . CAVERNAME_SELF_DIR);
		}		
		// Obter o idioma predefinido a partir do browser considerando o c�digo de 2 letras
        if ( isset($_SERVER['HTTP_ACCEPT_LANGUAGE'] ))
        {
			if (CAVERNAME_DEBUG) CavernameMensagens::Debug('HTTP_ACCEPT_LANGUAGE=' . $_SERVER['HTTP_ACCEPT_LANGUAGE']); // en-US,en;q=0.8,pt;q=0.6,es;q=0.4
            $l1 = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            $l2 = explode(';', $l1[0]);
            $l3 = explode('-', $l2[0]);
            $this->Idioma = $l3[0];
        }
	}
	/**
	 * Iniciar sess�o e carregar valores
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
	 * "Executa" o template e produz o output. No ficheiro index.php d� para configurar o modo Dump.
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
	 * Faz o output dos conte�dos de uma zona. � usado nos templates - Cavername::Out('zona')
	 */
	public static function Out($zone)
	{
		if (array_key_exists($zone, Cavername::One()->Zonas))
		{
			// zonas definidas na estrutura de p�ginas
			foreach(Cavername::One()->Zonas[$zone] as $conteudo) // CavernameConteudo
			{
				if (CAVERNAME_HEAD_ZONE === $zone)
				{
					// Manipular a tag <TITLE> para incluir o t�tulo do Artigo Principal
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
			// Zonas especiais com conte�do gerado pelo sistema que devem ser constru�das o mais tarde poss�vel.
			// No caso das mensagens de debug, por exemplo, a partir do momento em que se chama este c�digo
			// no template, deixa de ser poss�vel acrescentar mais mensagens.
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
	 * Constr�i uma string com o t�tulo do site e o t�tulo do artigo dentro da tag <title>
	 */
	public function TituloSiteComposto()
	{
		$s = $this->TituloSite;
		if ('' !== $this->TituloArtigoPrincipal)
		{
			$s .= ' - ' . $this->TituloArtigoPrincipal;
		}
		return sprintf(CAVERNAMEw_title, $s);
	}
}
/**
 * Uma classe para guardar dados do tema e implementar m�todos para localizar os ficheiros
 */
class CavernameTema
{
	public static $Id = '';
	private static $layoutPath = '';
	/**
	 * Localiza o ficheiro template na pasta cujo nome � o id do tema.
	 */
	public static function Prepare()
	{	
		// vai buscar o layout � base de dados em fun��o do pedido
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
	 * Imprime o conte�do da p�gina - chama o ficheiro do tema se existir, sen�o faz um output simples para testes
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
	 * Localiza o ficheiro passado por par�metro na pasta do tema e inclui-o se existir.
	 * � usada pelas classes ICavernameConteudoTemplate para incluir o template
	 * com layout dos excertos, pagina��o, etc...
	 * Cada um destes templates tem acesso ao conte�do com a vari�vel $obj
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
 * Esta classe cont�m os dados do utilizador logado, se existir. 
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
 * 1. dar mais seguran�a, tratando os par�metros
 * 2. centralizar o tratamento de par�metros para que possam ser facilmente usados noutros locais, tanto para a obten��o de valores como 
 *    para a constru��o de links.
 * O par�metro (a) � o id da p�gina a mostrar. Com este id sabemos qual o layout e os conte�dos que devem ser colocados em cada �rea.
 * O id indica-nos qual o conte�do a colocar na �rea principal.
 * Por exemplo: ?a=materiais/pv1999 refere-se ao ficheiro (pv1999.html) dentro da pasta (materiais)
 * O preenchimento das restantes �reas � feito em fun��o do id.
 * Para testar se o id de uma p�gina come�a por uma string, pode-se fazer: 0 === strpos($PaginaId, 'materiais'). 
 * Os restantes par�metros s�o guardados num array e podem ser acedidos com o m�odo Get
 */
class CavernamePedido
{
	private static $data = array();
	public static $PaginaId;
	/**
     * Esta fun��o trata todos os dados passados (GET ou POST)
	 * Copia para vari�vel local limpando os valores de aspas e tags (avoid XSS)
	 */
    public static function ParseRequest()
    {
		//if (CAVERNAME_DEBUG) CavernameMensagens::Debug('$_REQUEST=' . serialize($_REQUEST)); - comentado por causa das inje��es
		foreach($_REQUEST as $key => $value)
		{
			// para aceitar par�metros sem valor (como se fosse um bool)
			if ('' === $value) $value = '1';
			// O strip_tags � usado para prevenir a inser��o de c�digo malicioso, por exemplo:
			// http://localhost/dados/mh2/?a=<script>alert('Injected!');</script>
			$key = strip_tags($key);//, ENT_QUOTES, CAVERNAME_ENCODING);
			$value = strip_tags($value);//, ENT_QUOTES, CAVERNAME_ENCODING);
			self::$data[$key] = $value;
			if (CAVERNAME_DEBUG) CavernameMensagens::Debug($key.'='.$value);
		}
		// Identifica��o da p�gina, assumindo por omiss�o o valor definido na "base de dados"
		self::$PaginaId = CavernameDB::$Config['homepage'];
		if ('' === self::$PaginaId)
		{
			self::$PaginaId = '---';
		}
		if (array_key_exists('a', self::$data)) 
		{
			// retira pontos para garantir que n�o consegue aceder a pastas superiores e as barras que tiver a mais
			// mesmo que no URL seja passado %2E em vez do ponto, a string self::$data['a'] vem com o ponto.
			//   ��� The superglobals $_GET and $_REQUEST are already decoded: n�o � preciso usar urldecode()
			self::$PaginaId = str_replace('.', '', trim(self::$data['a'], '/'));
			if (CAVERNAME_DEBUG) CavernameMensagens::Debug('self::$PaginaId=' . self::$PaginaId);
		}
	}
	/**
	 * Esta fun��o devolve a convers�o do par�metro para o tipo pretendido. O tipo � deduzido do tipo da vari�vel $valorDefault
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
	 * Esta fun��o devolve o URL para a p�gina atual com UM dos par�metros modificados
	 */
	public static function NewWith($zonaId, $paramName, $valor)
	{
		$copia = $_GET; // usa o $_GET porque o array $data cont�m os dados do $_REQUEST, inclu�ndo o $_POST
		$copia[$zonaId . '-' . $paramName] = $valor;
		$newLink = '?';
		foreach($copia as $kp => $vp)
		{
			$newLink .= ('?' === $newLink ? '' : '&') . $kp . '=' . $vp;
		}
		return $newLink;
	}
	/**
     * Cria o URL de uma p�gina, centralizando nesta classe o tratamento do par�metro (a)
	 * Pode ser passado um par�metro. Para j� � suficiente mas � poss�vel que venha a ser necess�rio
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
 * Classe respons�vel por ler e ordenar o ficheiro cavername-db.
 */
class CavernameDB
{
	/**
	 * Configura��es gerais (valores por omiss�o)
	 */
	public static $Config = array('homepage' => '', 
								  'idioma' => '',
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
	 * Estrutura das p�ginas
	 * $pages cont�m uma lista de elementos (patterns) e para cada um deles vamos ver se se aplica � p�gina atual.
	 * Podem existir v�rios conte�dos na mesma zona. 
	 * Atribui cada valor encontrado � zona respetiva, eliminado o que l� estiver. 
	 * Nos casos em que haja prefixo ou sufixo (+) acrescenta o valor � zona, formando uma string separada por (;) 
	 * 		(Sendo uma string, � mais f�cil de acrescentar no in�cio).	 
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
			self::$pages[$pagina][$zona] = array_slice($p, 2); // array_slice: retorna os elementos a partir do 3� inclusive
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
						elseif ('+' === $vlr[0] || $index > 0) // a partir da 2� posi��o tem que acrescentar, sen�o apagava as anteriores
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
	 * Layout que se deve aplicar a cada p�gina
	 * Tem uma lista onde se podem usar asteriscos, por isso ir� considerar a �ltima v�lida que encontrar, aquando do pedido.
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
	 * Lista de conte�dos de confian�a
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
	 * M�todo para ler o ficheiro
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
					// identificar a sec��o
					$sec = trim(substr($line, 1, strlen($line)-2));
					continue;
				}
				// Todas as linhas t�m uma estrutura semelhante: partes separadas por espa�o, tab ou sinal de igual		
				$line = str_replace(array("\t","="), " ", $line);
				// Remover espa�os a mais
				while (true)
				{
					$line = str_replace('  ', ' ', $line, $count);
					if (0 === $count) break;
				}
				// Descodificar a linha conforme a sec��o
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
	 * M�todo para usar no Dump
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
     * $str LIKE $pattern, apenas para termina��es em *, por exemplo: organismos/ph LIKE organismos* � igual a TRUE
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
 * Esta classe cont�m os dados de um conte�do e � respons�vel
 * pela produ��o do HTML, seja carregando ou executando um ficheiro em disco,
 * seja executando uma fun��o, sempre com base num id que � passado no construtor.
 * O construtor � respons�vel pelo carregamento dos dados em bruto, ou seja, antes de aplicados os filtros.
 * Esta classe pode ser invocada em v�rios locais, nomeadamente quando h� includes ou excertos.
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
	
	public $Html = '';
	public $Data;

	private $principal = false; 
	private $extensao;
	/**
	 * O construtor procura e carrega o conte�do na propriedade $Html. S� aplica filtros e templates se assim for indicado.
	 */
	public function __construct($idConteudo, $idZona, $aplicarFiltrosTemplate = false)
	{
		// Inicializa��o
		{
			$this->Template = new CavernameConteudoTemplateNone();
			$this->Zona = $idZona;
			$this->Id = $idConteudo; 
			// troca a macro CAVERNAME_SPECIAL_CONTENT_MAIN pelo Id da p�gina solicitada
			if (CAVERNAME_SPECIAL_CONTENT_MAIN === $this->Id)
			{
				$this->Id = CavernamePedido::$PaginaId;
			}
			// verifica se se trata da �rea principal (isto tem que ser feito antes do redirecionamento)
			if ($this->Id === CavernamePedido::$PaginaId)
			{
				$this->principal = true;
			}
			// Verifica se existe um redirecionamento. A fun��o retorna o pr�prio id se n�o existir redirecionamento.
			// Isto n�o est� ao n�vel do pedido porque o conte�do pode
			// estar referenciado dentro de um outro documento
			$this->Id = CavernameDB::GetAlias($this->Id);
			// por omiss�o, aplicam-se filtros mas pode-se mudar no pr�prio conte�do com a macro <!--nofilters-->
			$this->ApplyFilters = true; 
		}
		// Obter os dados de um ficheiro ou atrav�s da execu��o de uma fun��o.
		// Os dados s�o guardados na propriedade Html e/ou na propriedade Data.
		{
			$this->DivId = $this->Zona . '--' . str_replace('@', '', str_replace('/', '-', $this->Id)); // valor por omiss�o
			if ('@' === substr($this->Id, 0, 1))
			{
				$this->executaFuncao();
			}
			else
			{
				$this->loadContentFromFile();				
			}
		}
		// Obter o t�tulo do site se for um conte�do da CAVERNAME_HEAD_ZONE.		
		{
			if (CAVERNAME_HEAD_ZONE === $this->Zona)
			{
				$s = $this->getTitle();
				if ('' !== $s) Cavername::One()->TituloSite = $s;
			}		
			// Obter o t�tulo da p�gina de uma tag H1 se existir.
			$this->Titulo = $this->getH1();
			if (true === $this->principal && $this->Titulo !== '')
			{
				Cavername::One()->TituloArtigoPrincipal = $this->Titulo;
			}
		}				
		// Controlo de recursividade - se o artigo solicitado j� foi processado � porque � "pai" do objeto atual.
		// Se o voltarmos a processar entraria num loop infinito.
		if (false === CavernameRecursiveControl::TestAndPush($this->Id))
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
	 * Procura pela tag H1 e extrai o conte�do
	 */
    private function getH1()
    {
		preg_match(CAVERNAME_PREG_H1, $this->Html, $matches);
		if (sizeof($matches) == 2) return strip_tags($matches[1]);
        return '';
    }
	/**
	 * Procura pela tag TITLE e extrai o conte�do
	 */
    private function getTitle()
    {
		preg_match(CAVERNAME_PREG_TITLE, $this->Html, $matches);
		if (sizeof($matches) == 2) return strip_tags($matches[1]);
        return '';
    }
	/**
	 * As fun��es s�o definidas no seguinte formato @classe.metodo e recebem este objeto como par�metro.
	 * Uma das coisas que pode fazer uma fun��o � definir o template a usar para tratar o Html/Data
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
	 * Procura e carrega/executa o ficheiro. Se for de confian�a faz include (executa), sen�o faz file_get_contents (l�)
	 */
	private function loadContentFromFile()
	{
		// Evitar o acesso a pastas indevidas. Isto � feito no tratamento do pedido mas como 
		// o objeto pode ser criado com um "include" definido noutro conteudo, � necess�rio fazer aqui tamb�m.
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
		// ler o conte�do do ficheiro OU executar se for de confian�a
		if (CavernameDB::IsTrusted($this->Id))
		{
			ob_start();
			// neste caso, o ficheiro PHP pode produzir output (� apanhado pelo ob_get_contents) ou atribuir
			// valores diretamente ao Html/Data
			// inclusive pode modificar a vari�vel Template e implementar novos ICavernameConteudoTemplate
			include($this->Path);			
			$this->Html = ob_get_contents();			
			ob_end_clean();
		}
		else
		{
			$this->Html = file_get_contents($this->Path);
			//$this->Html = mb_convert_encoding(file_get_contents($this->Path), CAVERNAME_ENCODING);
			//$this->Html = "[" . mb_detect_encoding($this->Html) . "<br />" . $this->Html;
		}
		// Remover tudo o que estiver fora da body tag, inclu�ndo a pr�pria tag, 
		// para o caso de se estar a carregar uma p�gina completa.
		$this->Html = preg_replace(CAVERNAME_PREG_BEFOREBODY, '<!--content removed-->', $this->Html);
		$this->Html = preg_replace(CAVERNAME_PREG_AFTERBODY, '<!--content removed-->', $this->Html);	

		// Normaliza��o das macros que se podem escrever de duas formas:
		// [!macro-name macro-parameters] e <!--macro-name macro-parameters-->
		// A primeira � �til para usar em editores de HTML
		$this->Html = preg_replace(CAVERNAME_PREG_MACROS, '<!--$1-->', $this->Html);		
		
		// Verifica se se devem aplicar filtros
		if (false !== strpos($this->Html, '<!--nofilters-->')) 
		{
			$this->ApplyFilters = false;
		}
		
		// Para identificar tipos espec�ficos (templates) com base no conte�do ou outras manipula��es do objeto.
		if (method_exists('CavernameExtend', 'ExtendConteudo'))
		{
			CavernameExtend::ExtendConteudo($this);
		}					
	}
	/**
	 * Fazer algumas substitui��es no texto e execu��o de macros
	 */
	public function AplicarFiltrosGerais()
	{	
		if (false === $this->ApplyFilters )
		{
			return;
		}
		// Quando se tratar de um conte�do lido de um ficheiro vamos tentar corrigir os caminhos das imagens.
		if ('' !== $this->Url)
		{			
			// substitui a express�o <!--docurl--> pela pasta  do ficheiro que foi lido
			$this->Html = str_ireplace('<!--docurl-->', dirname($this->Url) . '/', $this->Html);
			
			// Procura as tags IMG e extrai o atributo SRC. Se for um url relativo acrescenta o url do ficheiro que foi lido
			{
			// <img src="nestapasta.jpg" /> produz o seguinte array M = ('<img src="nestapasta.jpg" />', '<img src="', 'nestapasta.jpg', '" />')
			$FuncFixUrl = 'if(parse_url($m[2], PHP_URL_SCHEME) || substr($m[2],0,1) === \'/\') return $m[0];
						   return $m[1] . \'' . dirname($this->Url) . '/\' . $m[2] . $m[3];';
			$this->Html = preg_replace_callback(CAVERNAME_PREG_IMGSRC, create_function('$m', $FuncFixUrl), $this->Html);
			}
		}		
		// Tratamento de macros que s�o substitu�das pelo resultado da execu��o de fun��es com o mesmo nome.
		// O nome das macros s� pode ter letras ou n�meros.
		// O n�mero de par�metros s� pode ser 1 (devido � express�o regular), mas a fun��o pode fazer 
		// explode da string de acordo com o separador convencionado.
		// Para que as fun��es tenham acesso ao conte�do atual, existe uma refer�ncia static na classe CavernameFuncoes.
		$anterior = CavernameFuncoes::GetCurrentCavernameConteudo();
		CavernameFuncoes::SetCurrentCavernameConteudo($this);
		$this->Html = preg_replace_callback(CAVERNAME_PREG_FUNCTIONS, 
		                                    create_function('$m','return CavernameFuncoes::PREG_FunctionsCallback($m[0], $m[1], $m[2]);'), 
											$this->Html);         
		CavernameFuncoes::SetCurrentCavernameConteudo($anterior);
	}
	/**
	 * Construir o html de acordo com o tipo de bloco. 
	 * Nem sempre o tipo de bloco depende do conte�do. O mesmo conte�do pode ser mostrado como excerto, paginado ou apenas um �ndice.
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
	 * Procura um ficheiro com base no id, idioma e extens�es poss�veis.
	 * Retorna o caminho completo do ficheiro.
	 * Ordem de apresenta��o:
	 *	 {id}.{lang}.{ext}.draft  se {id}.{lang}.{ext} existir
	 *	 {id}.{lang}.{ext}.cache  se {id}.{lang}.{ext} existir
	 *	 {id}.{lang}.{ext}        se {id}.{lang}.{ext} existir
	 *	 {id}.{ext}.draft         se {id}.{ext} existir
	 *	 {id}.{ext}.cache         se {id}.{ext} existir
	 *	 {id}.{ext}               se {id}.{ext} existir
	 */
    private function procuraFicheiro()
    {		
		// Procura o ficheiro no idioma definido no sistema	
		$resultado = $this->procuraPorExtensoes(CAVERNAME_CONTEUDOS_FOLDER . $this->Id . '.' . Cavername::One()->Idioma);		
		// Se n�o encontrar, pesquisa sem idioma
		if ('' === $resultado) $resultado = $this->procuraPorExtensoes(CAVERNAME_CONTEUDOS_FOLDER . $this->Id);		
		// Se n�o encontrar, termina - n�o procura cache nem draft
		if ('' === $resultado)
		{
			$resultado = $this->procuraPorExtensoes(CAVERNAME_CONTEUDOS_FOLDER . CAVERNAME_404 . '.' . Cavername::One()->Idioma);		
			if ('' === $resultado) $resultado = $this->procuraPorExtensoes(CAVERNAME_CONTEUDOS_FOLDER . CAVERNAME_404);
			return $resultado;
		}				
		/*
		// Verifica se existe alguma vers�o draft - se encontrar termina: falta testar utilizador
		$pesquisa = $resultado . '.draft';
		if (file_exists($pesquisa))
		{
			return $pesquisa;
		}        
        // Verifica se o ficheiro de cache � mais recente que o ficheiro original - se for termina: falta ver como gerar a cache e para qu�.
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
	 * das extens�es definidas no sistema
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
				$this->extensao = $ext;
                return $test;
            }
        }
        return '';
    }
	/**
	 * Faz o output deste conte�do. Quando vai para dentro do HEAD n�o pode levar HEAD.
	 */
	public function Out($comDiv = true)
	{
		if($comDiv) printf(CAVERNAMEw_content_item, $this->DivId, $this->Html);		
		else print($this->Html);
	}
}
/**
 * Cont�m uma lista de fun��es static usadas na substitui��o de macros por texto din�mico.
 * Para al�m das fun��es static, existe um array de alternativas que � alimentado no
 * ficheiro cavername-extend
 */
class CavernameFuncoes
{
	/**
	 * Array p�blico de fun��es, para que se possam acrescentar "m�todos" a esta classe
	 * no ficheiro cavername-extend.
	 * Quando se executa a fun��o PREG_FunctionsCallback, se n�o existir a fun��o pretendida
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
	 * Isto foi criado para dar acesso ao pr�prio conte�do que est� 
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
	 * Devolve o resultado da execu��o de uma fun��o, se existir.
	 * Se a fun��o n�o existir, retorna o valor original.
	 */
    public static function PREG_FunctionsCallback($original, $macro, $arg)
    {
		// faz este teste para reduzir o n� de mensagens desnecess�rias de debug
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
		// para libertar o objeto de mem�ria e fazer o pop do controlo de recursividade.
		return $original;
    }
	/**
	 * Retorna o t�tulo do site... usado normalmente no topo, com algum destaque
	 */
    private static function SiteTitle($arg, $original = '')
    {
        return Cavername::One()->TituloSite;
    }
	/**
	 * Retorna o id do conte�do... usado no conteudo 404
	 */
    private static function IdConteudo($arg, $original = '')
    {
		return self::$currentCavernameConteudo->Id;
    }
	/**
	 * Devolve o link para outro conte�do (apenas o URL)
	 */
    private static function Link($to, $original = '')
    {
		return CavernamePedido::Create($to);
    }
	/**
	 * Retorna um link para outro artigo, cujo texto � composto pelo t�tulo desse artigo
	 * Intencionalmente n�o aplica filtros nem templates.
	 * se um conte�do quiser ter t�tulo e n�o o quiser mostrar basta colcoar <h1 style='display:none'>T�tulo</h1>
	 */
    private static function TitleFrom($id, $original)
    {
		$obj = new CavernameConteudo($id, self::$currentCavernameConteudo->Zona, false);
        if ('' === $obj->Titulo)
        {
			if (false === CAVERNAME_DEBUG) CavernameMensagens::Debug('<h1> not found');
            return $original;
        }
		return sprintf(CAVERNAMEw_link, self::Link($id), $obj->Titulo);
    }
}
/**
 * Esta classe cont�m uma pilha para fazer o controlo de recursividade infinita.
 * Por acidente pode-se incluir um artigo noutro e vice-versa ao mesmo tempo e o sistema
 * n�o est� preparado para tratar isso.
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
		// faz sempre o push porque o destrutor do objeto faz sempre o pop, ou seja, conv�m que este elemento exista sempre
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
 * As classes seguintes implementam tratamentos espec�ficos que � preciso dar aos conte�dos.
 * Por exemplo: obter uma p�gina, um cap�tulo, um excerto ou transformar um CSV numa tabela.
 * Cada um deles tem uma parte onde trata os dados e depois invoca um template para gerar o HTML.
 *
 * Build e Render vs. apenas Build.
 * Separar o Build e o Render dava muito jeito porque dessa forma todos os conte�dos eram constru�dos antes 
 * de criar o Html de cada um.
 * Isso permitia, por exemplo, que um conte�do que dependesse dos restantes (mensagens de debug, p.ex.) s� fosse
 * renderizado no fim.
 * Infelizmente isso n�o finciona porque existem conte�dos que s�o inclu�dos noutros, p.ex.:
			<h1>Este � uma p�gina.</h1>
			<p>Tem texto e aqui no meio tem um include.</p>
			[!include sentido]
			<p>E um excerto.</p>
			[!excerpt main]			
 * Neste caso n�o temos como fazer o render destes sub-conte�dos. Quando se faz o parse do conte�do principal, o sistema
 *  faz a substitui��o de [!include sentido] pelo conte�do com Id=sentido. N�o guarda uma inst�ncia do objeto {sentido}.
 *  Para que isso fosse poss�vel era preciso fazer um segundo parse.
 */
interface ICavernameConteudoTemplate
{
	public function Build(CavernameConteudo $obj);
}
class CavernameConteudoTemplateNone implements ICavernameConteudoTemplate
{
	public function Build(CavernameConteudo $obj){}
}
/**
 * Constr�i uma lista de mensagens.
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
 * Uma classe onde se faz a defini��o dos textos usados pelo sistema.
 * Este c�digo fica isolado nesta classe para que se possa colocar em ficheiros separados com mais facilidade se isso se vier a justificar.
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
		// N�o precisam de tradu��o
		{		
		define('CAVERNAMEw_content_item', '<div id=\'%1$s\'>%2$s</div>' . PHP_EOL);
		define('CAVERNAMEw_link', '<a href=\'%1$s\'>%2$s</a>');
		define('CAVERNAMEw_title', '<title>%1$s</title>');
		}
	}
	public static function FatalError($cavername_error_message)
	{
		// esta n�o depende do idioma... o erro pode dar antes...
		print("<!doctype html><html lang='en'><head><meta charset='utf-8'><title>Fatal Error</title><meta name='viewport' content='width=device-width, initial-scale=1'><style>
* {line-height: 1.2; margin: 0;} html {display: table; font-family: sans-serif; height: 100%;text-align: center;width: 100%;} body {display: table-cell;vertical-align: middle;margin: 2em auto;} h1 {color: #555;font-size: 2em;font-weight: 400;}
</style></head><body><h1>Fatal error</h1><p>Sorry, but it\'s not posible to present the page you were trying to view.</p><p>$cavername_error_message</p></body></html>");
	}
}
?>