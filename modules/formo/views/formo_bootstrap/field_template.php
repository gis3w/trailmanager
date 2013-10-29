<div class="field control-group formo-<?=$field->get('driver')?><?php if ($error = $field->error()) echo ' error'; ?>" id="field-container-<?=$field->alias()?>">
	<?php if ($title): ?>
		<span class="title"><?=$title?></span>
	<?php elseif ($label = $field->label()): ?>
		<label for="<?=$field->attr('id')?>" class="control-label"><?=$label?></label>
	<?php endif; ?>
                <div class="controls">
	<?=$field->open().$field->html().$field->render_opts().$field->close()?>
                </div>
	<?php if ($msg = $field->error()): ?>
		<span class="help-block"><?=$msg?></span>
	<?php elseif ($msg = $field->get('message')): ?>
		<span class="help-block"><?=$msg?></span>
	<?php endif; ?>
</div>