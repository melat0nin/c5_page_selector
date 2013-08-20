<?php defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
 
// Arrays of (1) possible pages, and (2) currently selected pages
$options_array = array();
$selected_ids = array();
 
// If $selected is an array there must be pages selected already -- get their IDs
if (is_array($selected)) {
    foreach($selected as $key => $value){
        $selected_ids[] = $value;
    }
}
 
// If there is at least 1 page that can be selected, display a list. Otherwise warn the user that
// there are no options to choose from. This is determined by the filtering methods applied to
// the pagelist object in getAvailablePages() in controller.php
if (count($options) > 0) {
    echo '<fieldset>';
 
    // Loop through available options and output checkboxes, setting to checked where page IDs
    // match those in the array of selected page IDs
    foreach ($options as $key=>$option) {
        $selected = '';
        if ( in_array($key, $selected_ids)) $selected = ' checked';
    ?>
 
        <label class="checkbox inline">
            <input type="checkbox" name="<?=$fieldPostName?>[]" value="<?=$key?>"<?=$selected?> />
            <?=$option?>
        </label>
 
<?php }
    echo '</fieldset>';
} else {
 
    echo '<strong style="line-height:30px">No options have been defined yet.</strong>';
 
}