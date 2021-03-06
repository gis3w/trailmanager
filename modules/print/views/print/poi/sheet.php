<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE pdf SYSTEM "%resources%/dtd/doctype.dtd">
<pdf>
    <dynamic-page  page-size="<?php echo $pdf_page_size ?>">
        <?php echo $header1 ?>
        <div class="map-image"><img src="<?php echo $tmp_dir.$mapURL ?>" /></div>
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
                    <?php foreach(['pt_inter',
                                  'strut_ric',
                                  'aree_attr',
                                  'insediam',
                                  'pt_acqua',
                                  'pt_socc',
                                  'fatt_degr'] as $code): ?>
                    <?php if(isset($poi->{$code.'_code'}->description)): ?>
                        <div><?php echo __($code) ?>: <?php echo $poi->{$code.'_code'}->description ?></div>
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
        <?php if($poi->note): ?>
        <!--DESCRIPTION-->
        <div class="outside-column" >
            <h2><?php echo __('Note') ?></h2>
            <p><?php echo $poi->note ?></p>
        </div>
        <?php endif ?>
        <?php if($poi->note_man): ?>
            <!--DESCRIPTION-->
            <div class="outside-column" >
                <h2><?php echo __('Maintenance note') ?></h2>
                <p><?php echo $poi->note_man ?></p>
            </div>
        <?php endif ?>

        <!--IMAGES-->
        <?php if(isset($images_sheet)) echo $images_sheet; ?>


    </dynamic-page>
</pdf>