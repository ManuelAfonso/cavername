<?php
require_once('cavername.php');
/**
 * Carrega e processa a informação 
 */
Cavername::Prepare();
/**
 * "Devolve" a página para o cliente. Se se quiser incluir o cavername numa página simples, sem temas, este passo é opcional, 
 *       basta fazer Cavername::Out('zona') no local apropriado
 */
Cavername::Serve();
?>
