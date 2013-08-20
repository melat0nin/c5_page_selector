<?php
defined('C5_EXECUTE') or die("Access Denied.");

$th = Loader::helper('text');

/*
 * Loop through the available options and output checkboxes
 */
foreach ($options as $key=>$option) : ?>
 
    <label class="checkbox inline" style="margin:2px 0; float: none; display: block">
        <input type="checkbox" name="<?php echo $fieldPostName; ?>[]" value="<?php echo $key; ?>"/>
        <?php echo $th->entities($option); ?>
    </label>
 
<?php
endforeach;