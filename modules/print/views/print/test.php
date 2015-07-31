<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE pdf SYSTEM "%resources%/dtd/doctype.dtd">
<pdf>
    <dynamic-page>
        <h1>Header</h1>
        <p>paragraph</p>
        <div color="red">Layer</div>
        <div>
            <img src="dir:<?php APPPATH.'../public/img/logo_trail_40h.png' ?>" />
        </div>
        <table>
            <tr>
                <td>Column</td>
                <td>Column</td>
            </tr>
        </table>
    </dynamic-page>
</pdf>