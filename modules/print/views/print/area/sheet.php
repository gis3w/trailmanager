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
        <!--TITLE-->
        <div>
            <h1><?php echo $area->title ?></h1>
        </div>
        <!--CATEGORY AND CHARACTERISTICS-->
        <div>
            <div class="category box">
                <h3><?php echo __('Category') ?></h3>
            </div>
        </div>
        <!--DESCRIPTION-->
        <div class="outside-column" >
            <h2><?php echo __('Description') ?></h2>
            <p><?php echo $area->description ?></p>
        </div>

        <!--IMAGES-->
        <?php if(isset($images_sheet)) echo $images_sheet; ?>


    </dynamic-page>
</pdf>