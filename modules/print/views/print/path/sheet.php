<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE pdf SYSTEM "%resources%/dtd/doctype.dtd">
<pdf>
    <dynamic-page  page-size="<?php echo $pdf_page_size ?>">
        <?php echo $header1 ?>
        <div class="map-image"><img src="<?php echo $tmp_dir.$mapURL ?>" /></div>
        <!--TITLE-->
        <div>
            <h1><?php echo $path->title ?></h1>
        </div>
        <!--CATEGORY AND CHARACTERISTICS-->
        <div>
            <div class="category box">
                <h3><?php echo __('Category') ?></h3>

            </div>
            <div class="characteristics box">
                <h3><?php echo __('Characteristics') ?></h3>
                    <div><img src="<?php echo $tmp_dir.'/public/img/lunghezza.png' ?>" /></div><div><?php echo __('Length') ?>: <?php echo $path->length ?></div>
                    <div><img src="<?php echo $tmp_dir.'/public/img/dislivello.png' ?>" /></div><div><?php echo __('Altitude gap') ?>: <?php echo $path->altitude_gap ?></div>

            </div>
            <?php if(isset($heights_profile_img)): ?>
            <div class="heights-profile-chart">
                <div>
                    <img src="<?php echo $heights_profile_img ?>" />
                </div>
            </div>
            <?php endif ?>
        </div>
        <!--DESCRIPTION-->
        <div class="outside-column" >
            <h2><?php echo __('Description') ?></h2>
            <p><?php echo $path->description ?></p>
        </div>

        <!--IMAGES-->
        <div>
            <h2><?php echo __('Pictures') ?></h2>
            <?php $images = $path->images->find_all() ?>
            <?php foreach ($images as $image): ?>
                <div class="image-container">
                    <img class="image-image" src="<?php echo APPPATH.'../'.$img_base_dir.'/'.$image->file ?>" />
                    <?php if(isset($image->description) AND $image->description !=''): ?>
                        <p class="image-description"><?php echo $image->description ?></p>
                    <?php endif ?>
                </div>
            <?php endforeach; ?>

        </div>


    </dynamic-page>
</pdf>