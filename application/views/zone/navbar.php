<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container-fluid">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main_navbar_admin">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
      </button>
        <a class="navbar-brand" href="#" style="padding: 0px"><?php echo Html::image($img_path.$logo_navbar,array('alt'=>'TuristicGIS', 'class'=>'img-responsive')); ?></a>
    </div>
    <div class="collapse navbar-collapse" id="main_navbar_admin">
        <ul class="nav navbar-nav" role="navigation">
                <?php foreach($main_menu as $name => $params): ?>
                        <li><a id="<?php echo $params['id'] ?>Button" href="<?php echo  $params['url']?>"><i class="icon icon-<?php echo  $params['icon']?>"></i> <?php if(isset($params['name'])) echo __($params['name'])?></a></li>
                <?php endforeach; ?>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <?php 
                                                                foreach ($langs as $I18n => $lang):
			?>
                    <li><a href="?lang=<?php echo $I18n ?>"><i class="icon icon-flag"></i> <?php echo $lang ?></a></li>
            <?php
				endforeach;
			?>
            <li><span class=""><?php echo $user->user_data->nome. " ".$user->user_data->cognome."(".$user->username.")";?></span></li>
        </ul>
    </div>
    </div>
</nav>

