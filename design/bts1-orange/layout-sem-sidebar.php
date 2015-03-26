<?php require('common.php'); ?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <?php Cavername::Out(CAVERNAME_HEAD_ZONE);?>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?php echo $bootstrapCss;?>">
  <link href="<?php CavernameTema::Url();?>/style.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="<?php CavernameTema::Url();?>/print.css" rel="stylesheet" type="text/css" media="print" />
</head>
<body>
  <div id="erros">
    <?php Cavername::Out('errorzone');?>
  </div>
  <?php Cavername::Out('navigation');?>
  <header class="page-header hidden-xs">
    <div class="container"><?php Cavername::Out('title');?></div>
  </header>
  <div class="container">
	<div class="row">
		<section class="col-sm-8" id="content"><?php Cavername::Out('mainzone');?></section>
		<aside class="col-sm-4 sidebar"><?php Cavername::Out('extra');?></aside>
	</div>		
  </div><!-- container -->
  <footer id="footer">
    <?php Cavername::Out('final');?>
  </footer>
  <div id="debug">
    <?php Cavername::Out(CAVERNAME_DEBUG_ZONE);?>
  </div>
  <script src="<?php echo $jqueryJs;?>"></script>
  <script src="<?php echo $bootstrapJs;?>"></script>
</body>
</html>
