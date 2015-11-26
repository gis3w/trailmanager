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
            <div class="characteristics box">

            </div>

        </div>

        <?php if(isset($images_sheet)) echo $images_sheet; ?>


    </dynamic-page>
</pdf>