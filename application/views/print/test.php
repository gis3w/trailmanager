<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
<!DOCTYPE pdf SYSTEM "%resources%/dtd/doctype.dtd">
<pdf>
    <dynamic-page document-template="/home/www/cosoweb/public/pdf/template_base.pdf">
        <h1>Header</h1>
        <p>paragraph</p>
        <div color="red">Layer</div>
        <table>
            <tr>
                <td>Column</td>
                <td>Column</td>
            </tr>
        </table>
    </dynamic-page>
</pdf>