<?php
defined('C5_EXECUTE') or die("Access Denied.");

/*
 * Loop through the available options and output checkboxes
 */
foreach ($options as $key=> $option) { ?>
 
    <label class="checkbox inline" style="margin:2px 0; float: none; display: block">
        <input type="checkbox" name="<?=$fieldPostName?>[]" value="<?=$key?>"/> <?=$option?>
    </label>
 
<?php
}