<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>

<!--IMAGES-->
<?php if (count($images_resized) > 0): ?>
<div>
    <h2><?php echo __('Pictures') ?></h2>
<?php foreach ($images as $image): ?>
    <?php if (isset($images_resized[$image->file])): ?>
    <div class="image-container">
        <img class="image-image" src="<?php echo $images_resized[$image->file] ?>" />
        <?php if(isset($image->description) AND $image->description !=''): ?>
            <p class="image-description"><?php echo $image->description ?></p>
        <?php endif ?>
    </div>
    <?php endif ?>
<?php endforeach; ?>
</div>
<?php endif ?>