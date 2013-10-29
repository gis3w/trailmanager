<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
        <a class="navbar-brand" href="#" style="padding: 0px"><?php echo Html::image($img_path."nav_bar_logo_24w.png",array('alt'=>'Safe3', 'class'=>'img-responsive')); ?></a>
    </div>
	<div class="collapse navbar-collapse navbar-ex1-collapse">
		<ul class="nav navbar-nav">
			<?php foreach($main_menu as $name => $params): ?>
				<li><a id="<?php echo $params['id'] ?>Button" href="<?php echo  $params['url']?>"><i class="icon icon-<?php echo  $params['icon']?>"></i> <?php if(isset($params['name'])) echo __($params['name'])?></a></li>
			<?php endforeach; ?>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <?php 
				foreach ($langs as $I18n => $lang):
			?>
                    <li><a href="/?lang=<?php echo $I18n ?>"><?php echo $lang ?></a></li>
            <?php
				endforeach;
			?>
            <li><span class=""><?php echo $user->user_data->nome. " ".$user->user_data->cognome."(".$user->username.")";?></span></li>
        </ul>
    </div>
</nav>
<nav class="navbar navbar-inverse navbar-fixed-bottom">
	<div class="navbar-header" style="text-align: center;">
        <a class="navbar-brand"   href="#" style="">Studio Mazzi</a>
        <a class="navbar-brand"  href="#" style="">gis3w</a>
    </div>
</nav>
