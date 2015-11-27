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
                    <?php if(file_exists($tmp_dir.'/'.$img_marker_dir.'/'.$poi->typology->icon)): ?>
                        <div>
                            <img class="typology-section" src="<?php echo $tmp_dir.'/'.$img_marker_dir.'/'.$poi->typology->icon ?>" />
                            <div class="typology-name"><?php echo __($poi->typology->name) ?></div>
                        </div>
                    <?php endif ?>
                    <?php foreach(['pt_inter',
                                      'strut_ric',
                                      'aree_attr',
                                      'insediam',
                                      'pt_acqua',
                                      'pt_socc'] as $code): ?>
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
                    <div><?php echo __('Reporter') ?>: <?php echo $poi->highliting_user->user_data->nome ?> <?php $poi->highliting_user->user_data->cognome ?></div>
                    <div><?php echo __('Supervisor') ?>: <?php echo $poi->supervisor_user->user_data->nome ?> <?php $poi->supervisor_user->user_data->cognome ?></div>
                    <div><?php echo __('Executor') ?>: <?php echo $poi->executor_user->user_data->nome ?> <?php $poi->executor_user->user_data->cognome ?></div>
                    <div><?php echo __('State')?>: <?php echo $currentState ?></div>
                </div>
            </div>

        </div>

        <?php if(isset($poi->description)): ?>
            <div>
                <h3><?php echo __('Description') ?></h3>
                <p><?php echo $poi->description ?></p>
            </div>
        <?php endif ?>

        <?php if(isset($poi->ending)): ?>
            <div>
                <h3><?php echo __('Ending') ?></h3>
                <p><?php echo $poi->ending ?></p>
            </div>
        <?php endif ?>

        <!--NOTES-->
        <?php if(isset($notes)): ?>
            <div>
                <h3><?php echo __('Notes') ?></h3>
                <?php echo $notes ?>
            </div>
        <?php endif ?>
    </dynamic-page>
    <dynamic-page  page-size="<?php echo $pdf_page_size ?>">
        <!--IMAGES-->
        <?php if(isset($images_sheet)) echo $images_sheet; ?>
    </dynamic-page>

</pdf>