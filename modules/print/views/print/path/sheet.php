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
                <h3><?php echo __('Characteristics') ?></h3>
                <div class="body-box">
                    <div><?php echo __('ex_se') ?>: <?php echo $path->ex_se ?></div>
                    <div><?php echo __('diff') ?>: <?php echo $path->difficulty->code.' - '.$path->difficulty->description ?></div>
                    <div class="item-image"><img src="<?php echo $tmp_dir.'/public/img/lunghezza.png' ?>" /><div><?php echo __('Length') ?>: <?php echo $path->l ?> m</div></div>
                    <div class="item-image"><img src="<?php echo $tmp_dir.'/public/img/dislivello.png' ?>" /><div><?php echo __('Altitude gap') ?>: <?php echo $path->diff_q ?> m</div></div>
                    <div><?php echo __('q_init') ?>: <?php echo $path->q_init ?> m</div>
                    <div><?php echo __('q_end') ?>: <?php echo $path->q_end ?> m</div>
                    <?php if($path->rev_time): ?>
                    <div><?php echo __('time') ?>: <?php echo $path->time ?> min</div>
                    <?php endif ?>
                    <?php if($path->rev_time): ?>
                    <div><?php echo __('rev_time') ?>: <?php echo $path->rev_time ?> min</div>
                    <?php endif ?>
                    <?php if($path->walkable): ?>
                        <div><?php echo __('Walkable path') ?>: <?php echo $path->walkable->description ?></div>
                    <?php endif ?>

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
            <p><?php echo $path->descriz ?></p>
        </div>
        <!--LOC-->
        <?php if(isset($path->loc)): ?>
        <div class="outside-column" >
            <h2><?php echo __('Loc') ?></h2>
            <p><?php echo $path->loc ?></p>
        </div>
        <?php endif ?>
        <?php if(isset($path->em_natur)): ?>
        <!--EM_NATUR-->
        <div class="outside-column" >
            <h2><?php echo __('Em_natur') ?></h2>
            <p><?php echo $path->em_natur ?></p>
        </div>
        <?php endif ?>
        <?php if(isset($path->em_paes)): ?>
        <!--EM_PAES-->
        <div class="outside-column" >
            <h2><?php echo __('Em_paes') ?></h2>
            <p><?php echo $path->em_paes ?></p>
        </div>
        <?php endif ?>
        <?php if(isset($path->ev_stcul)): ?>
        <!--EV_STCUL-->
        <div class="outside-column" >
            <h2><?php echo __('Ev_stcul') ?></h2>
            <p><?php echo $path->ev_stcul ?></p>
        </div>
        <?php endif ?>
        <!--OP_ATTR-->
        <?php if(isset($path->op_attr)): ?>
        <div class="outside-column" >
            <h2><?php echo __('Op_attr') ?></h2>
            <p><?php echo $path->op_attr ?></p>
        </div>
        <?php endif ?>

        <?php if(isset($images_sheet)) echo $images_sheet; ?>


    </dynamic-page>
</pdf>