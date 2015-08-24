<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<!--IMAGES-->
<div>
    <h2><?php echo __('Pictures') ?></h2>
<?php foreach ($images as $image): ?>
    <div class="image-container">
        <img class="image-image" src="<?php echo $images_resized[$image->file] ?>" />
        <?php if(isset($image->description) AND $image->description !=''): ?>
            <p class="image-description"><?php echo $image->description ?></p>
        <?php endif ?>
    </div>
<?php endforeach; ?>
</div>