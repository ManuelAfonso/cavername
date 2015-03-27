<h1>Testes PHP </h1>
<?php
echo '__FILE__: ', __FILE__, '<br />';
echo '$_SERVER[\'PHP_SELF\']: ', $_SERVER['PHP_SELF'], '<br />';
echo '$_SERVER[\'REQUEST_URI\']: ', $_SERVER['REQUEST_URI'], '<br />';
echo '$_SERVER[\'DOCUMENT_ROOT\']: ', $_SERVER['DOCUMENT_ROOT'], '<br />';
echo 'getcwd(): ', getcwd(), '<br />';
echo '<br />';
echo 'dirname(__FILE__): ', dirname(__FILE__), '<br />';
echo 'basename(__FILE__): ', basename(__FILE__), '<br />';
echo '<br />';
echo 'dirname(\'PHP_SELF\'): ', dirname($_SERVER['PHP_SELF']), '<br />';
echo 'basename(\'PHP_SELF\'): ', basename($_SERVER['PHP_SELF']), '<br />';
echo '<br />';
?>