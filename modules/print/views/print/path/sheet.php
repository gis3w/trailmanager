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
            <!--
            <div class="category box">
                <h3><?php echo __('Category') ?></h3>

            </div>
            -->
            <div class="characteristics box">
                <h3><?php echo __('Characteristics') ?></h3>
                <div class="body-box">
                    <div><?php echo __('diff_current') ?>: <?php echo $path->difficulty_current->code.' - '.$path->difficulty_current->description ?></div>
                    <div class="item-image"><img src="<?php echo $tmp_dir.'/public/img/lunghezza.png' ?>" /><div><?php echo __('Length') ?>: <?php echo $path->length ?></div></div>
                    <div class="item-image"><img src="<?php echo $tmp_dir.'/public/img/dislivello.png' ?>" /><div><?php echo __('Altitude gap') ?>: <?php echo $path->altitude_gap ?></div></div>
                    <div><?php echo __('q_init_current') ?>: <?php echo $path->q_init_current ?> m</div>
                    <div><?php echo __('q_end_current') ?>: <?php echo $path->q_end_current ?> m</div>
                    <div><?php echo __('time_current') ?>: <?php echo $path->time_current ?> min</div>
                    <div><?php echo __('rev_time_current') ?>: <?php echo $path->rev_time_current ?> min</div>
                </div>
            </div>
            <?php if(isset($heights_profile_img)): ?>

            <div class=" box heights-profile-chart">
                <h3><?php echo __('Heightsprofilepath') ?></h3>
                <div class="body-box">
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
        <!--LOC_CURRENT-->
        <?php if(isset($path->loc_current)): ?>
        <div class="outside-column" >
            <h2><?php echo __('Loc_current') ?></h2>
            <p><?php echo $path->loc_current ?></p>
        </div>
        <?php endif ?>
        <?php if(isset($path->em_natur_current)): ?>
        <!--EM_NATUR_CURRENT-->
        <div class="outside-column" >
            <h2><?php echo __('Em_natur_current') ?></h2>
            <p><?php echo $path->em_natur_current ?></p>
        </div>
        <?php endif ?>
        <?php if(isset($path->em_paes_current)): ?>
        <!--EM_PAES_CURRENT-->
        <div class="outside-column" >
            <h2><?php echo __('Em_paes_current') ?></h2>
            <p><?php echo $path->em_paes_current ?></p>
        </div>
        <?php endif ?>
        <?php if(isset($path->ev_stcul_current)): ?>
        <!--EV_STCUL_CURRENT-->
        <div class="outside-column" >
            <h2><?php echo __('Ev_stcul_current') ?></h2>
            <p><?php echo $path->ev_stcul_current ?></p>
        </div>
        <?php endif ?>

        <?php if(isset($images_sheet)) echo $images_sheet; ?>


    </dynamic-page>
</pdf>