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
        <ul class="nav navbar-nav">
			<?php foreach($main_menu as $name => $params): ?>
				<?php if($params['type'] === 'dropdown'): ?>
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon icon-<?php echo  $params['icon']?>"></i> <?php if(isset($params['name'])) echo __($params['name'])?><b class="caret"></b></a>
					<ul class="dropdown-menu">
						<?php foreach($params['items'] as $dditem): ?>
							<li><a id="<?php echo $dditem['id'] ?>Button" href="<?php echo  $dditem['url']?>"><i class="icon icon-<?php echo  $dditem['icon']?>"></i> <?php if(isset($dditem['name'])) echo __($dditem['name'])?></a></li>
						<?php endforeach; ?>
					</ul>
				</li>
				<?php else: ?>
					<li><a id="<?php echo $params['id'] ?>Button" href="<?php echo  $params['url']?>"><i class="icon icon-<?php echo  $params['icon']?>"></i> <?php if(isset($params['name'])) echo __($params['name'])?></a></li>
				<?php endif; ?>
            <?php endforeach; ?>
        </ul>
        
        <ul class="nav navbar-nav navbar-right">
			<?php if(isset($search) AND $search): ?>
				<li><a id="searchButton" href="#" data-toggle="modal"><i class="icon icon-search"></i> <?php echo __('Search') ?></a></li>
			<?php endif; ?>
            <?php 
                                                                foreach ($langs as $I18n => $lang):
			?>
                    <li><a href="?lang=<?php echo $I18n ?>"><i class="icon icon-flag"></i> <?php echo $lang ?></a></li>
            <?php
				endforeach;
			?>
            <?php if($frontend):?>
                <li><a href="#"><i class="icon icon-flag"></i> <?php echo __('Help') ?></li>
             <?php endif; ?>
                <?php if(isset($user) AND !$frontend): ?>
             <li>
            
            <span class=""><?php echo $user->user_data->nome. " ".$user->user_data->cognome."(".$user->username.")";?></span><br />
            
            <span><?php echo __('Version: '.SAFE::VERSION) ?></span>
            
             </li>
             <?php endif; ?>
        </ul>
    </div>
    </div>
</nav>