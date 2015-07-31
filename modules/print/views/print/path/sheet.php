<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE pdf SYSTEM "%resources%/dtd/doctype.dtd">
<pdf>
    <dynamic-page>
        <?php echo $header1 ?>
        <h1>Title</h1>
        <div class="map">
            <img src="<?php echo $mapURL ?>" />
        </div>
        <div>content</div>
    </dynamic-page>
</pdf>