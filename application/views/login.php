<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<div id="login" class="login">
    <div id="logo_login">
        <?php echo Html::image($img_path.$logo_main,array('alt'=>'cosoWEB')); ?>
    </div>
       <div id="signin">
        <?php if (isset($message)):?>
            <div class="alert alert-error">
                <p><?php echo $message ?></p>
            </div>
        <?php endif; ?>
        <?php if(isset($change) AND !$change): ?>
           <div>
                <?php echo html::showErr(array('type'=>'msg','msg'=>Kohana::message('auth', 'msg_change_newpass'))); ?>
           </div>
        <?php endif; ?>
        <div id="login_form" class="">
        <?php echo$form;  ?>
        </div>
    </div>
</div>

<div id="powered_by" class="powered_by">
      
      <p><i>powered by</i></p>
      <?php echo Html::anchor('http://www.agrofauna.it',Html::image($img_path."logo_mazzi_200w.png",array('alt'=>'Studio Mazzi')),array('title' => 'Studio Mazzi','class' => 'logo_mazzi_login'));?>
      <?php echo Html::anchor('http://www.gis3w.it',Html::image($img_path."logo_gis3w_h60.png",array('alt'=>'GIS3W')),array('title' => 'GIS3W','class' => 'logo_gis3w_login'));?>
      <p>&nbsp;<p>
      <p>Safe3 gestisce i dati delle persone e delle aziende nel rispetto del Decreto Legislativo 30 giugno 2003, n. 196 "Codice in materia di protezione dei dati personali".</p>
  </div>