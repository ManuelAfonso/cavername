<!DOCTYPE html>
<html lang="pt-PT">
<head>
  <?php Cavername::Out(CAVERNAME_HEAD_ZONE);?>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
  <link href="<?php CavernameTema::Url();?>/style.css" rel="stylesheet" type="text/css" media="screen" />
  <link href="<?php CavernameTema::Url();?>/print.css" rel="stylesheet" type="text/css" media="print" />
</head>
<body>
  <div id="erros">
    <?php Cavername::Out('errorzone');?>
  </div>
  <div class="jumbotron">
    <div class="container"><?php Cavername::Out('title');?></div>
  </div>
  <div class="container">
    <div class="row">
      <section class="col-sm-4" id="content-fp"><?php echo Cavername::Out('mainzone');?></section>
	  <aside class="col-sm-4 sidebar"><?php echo Cavername::Out('extra');?></aside>
	  <aside class="col-sm-4 sidebar"><?php echo Cavername::Out('extra-fp');?></aside>
    </div>
  </div>
  <footer id="footer">
    <?php Cavername::Out('final');?>
  </footer>
  <div id="debug">
    <?php Cavername::Out(CAVERNAME_DEBUG_ZONE);?>
  </div>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
</body>
</html>
