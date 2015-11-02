<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE pdf SYSTEM "%resources%/dtd/doctype.dtd">
<pdf>
    <page page-size="<?php echo $pdf_page_size ?>-landscape">
        <?php echo $header1 ?>
        <div class="map-image"><img src="<?php echo $tmp_dir.$mapURL ?>" /></div>
    </page>
    <dynamic-page  page-size="<?php echo $pdf_page_size ?>">
        <?php echo $header1 ?>
        <!--CATEGORY AND CHARACTERISTICS-->
        <div>
            <div class="category box">
                <h3><?php echo __('Category') ?></h3>
                <div class="body-box">
                    <div>
                        <img class="typology-section" src="<?php echo $tmp_dir.'/'.$img_marker_dir.'/'.$poi->typology->marker ?>" />
                        <div class="typology-name"><?php echo __($poi->typology->name) ?></div>
                    </div>
                    <?php foreach ($typologies as $typology): ?>
                    <?php if(file_exists($tmp_dir.'/'.$img_marker_dir.'/'.$typology->marker)): ?>
                    <div>
                        <img class="typology-section" src="<?php echo $tmp_dir.'/'.$img_marker_dir.'/'.$typology->marker ?>" />
                        <div class="typology-name"><?php echo __($typology->name) ?></div>
                    </div>
                    <?php endif ?>
                    <?php endforeach ?>
                </div>
            </div>

            <div class="characteristics box">
                <h3><?php echo __('Characteristics') ?></h3>
                <div class="body-box">
                    <div><?php echo __('Coordinates') ?>: <?php echo __('Lat').': '.round($lat,3).', '.__('Lon').': '.round($lon,3) ?></div>
                    <div><?php echo __('Quota') ?>: <?php echo $poi->quota ?> m</div>


                </div>
            </div>

        </div>
        <?php if($poi->description): ?>
        <!--DESCRIPTION-->
        <div class="outside-column" >
            <h2><?php echo __('Description') ?></h2>
            <p><?php echo $poi->description ?></p>
        </div>
        <?php endif ?>

        <!--IMAGES-->
        <?php if(isset($images_sheet)) echo $images_sheet; ?>


    </dynamic-page>
</pdf>