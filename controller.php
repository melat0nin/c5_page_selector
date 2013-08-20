<?php defined('C5_EXECUTE') or die(_('Access Denied.'));
 
Loader::model('attribute/types/default/controller');
 
/*
 * Page selector attribute
 *
 * Grabs a list of pages of a particular type.
 * Separate attributes are used to extend this class
 * and specify the correct page type handle using the
 * setHandle() method.
 *
 */
class PageSelectorAttributeTypeController extends DefaultAttributeTypeController {
 
    /*
     * Setup attribute form
     */
    public function form() {
        $this->set('fieldPostName', $this->field('value'));
 
        // Get all possible options (cIDs and collection names)
        $options = $this->getAvailablePages();
        $this->set('options', $options);
 
        // Get currently selection values (page IDs)
        $selected = is_object($this->attributeValue) ? $this->getAttributeValue()->getValue() : '';
        $selected = explode("\n", trim($selected));
        $this->set('selected', $selected);
 
        $availablePages = $this->getAvailablePages();
        $this->set('availablePages', $availablePages);
        $this->set('fieldPostName', $this->field('value'));
    }
 
    /*
     * Get available pages using handle provided handle
     */
    public function getAvailablePages() {
        Loader::model('page_list');
        $pl = new PageList;
 
        // Check for external handle or set manually
        // (1) You can filter by a particular page type handle by specifying it below, or use this attribute as
        //     a parent class for a set of child classes which specify the page type handle to filter by
        //     on an ad-hoc basis (this means there is one central controller containing the attribute's logic,
        //     which you can extend with further skeleton page selector attributes to provide page selectors for
        //     different page type handles
        // (2) To remove the filter, so all the site's pages are listed, comment out the next 3 lines
        //$ext_handle = $this->setHandle();
        $handle = ($ext_handle == '') ? 'define_handle' : $ext_handle;
        $pl->filterByCollectionTypeHandle($handle);
 
                    // Get the (filtered, if specified above) list of pages
        $pages = $pl->get();
 
        $retArray = array();
        foreach ($pages as $page) {
            $retArray[$page->getCollectionID()] = $page->getCollectionName();
        }
        asort($retArray);
        return $retArray;
    }
 
    /*
     * Serialise selected page IDs and save to DB
     */
    public function saveForm($data){
        if (empty($data['value'])) {
            $this->saveValue('');
	} else {
            // Using the manual serialization method rather than serialize() to
            // make searching more straightforward
            $valueArray = $data['value'];
            $valueString = implode("\n", $valueArray);
            $this->saveValue("\n{$valueString}\n");
	}
    }
 
    /*
     * Pass available options to search.php
     */
    public function search() {
        $this->set('fieldPostName', $this->field('value'));
        $options = $this->getAvailablePages();
        $this->set('options', $options);
    }
 
    /*
     * Derives page names from DB-stored page IDs for user-friendliness
     */
    public function getDisplayValue() {
        $results = $this->getValue();
        $results = explode("\n", trim($results));
 
        $string = '';
        foreach ($results as $result) {
            $page = Page::getByID($result);
            $string .= $page->getCollectionName() . '<br/>';
        }
        $string = substr($string, 0, strlen($string)-2);
        return $string;
    }
 
    /*
     * Provides search functionality for concrete5's Sitemap Page Search
     * This function is optional -- the attribute will work without it, but
     * the Sitemap Page Search won't work. Requires search.php.
     */
    public function searchForm($list) {
        $terms = $this->request('value');

	// If no options are set, return an unfiltered list of the site's pages,
	// otherwise build the DB search query, filter the site's pages by it
	// and return the result
	if (!is_array($terms)) {
            return $list;
	} else {
            $db = Loader::db();
            $tbl = $this->attributeKey->getIndexedSearchTable();
            $akHandle = $this->attributeKey->getAttributeKeyHandle();
            $criteria = array();
            foreach ($terms as $term) {
                $escapedTerm = $db->escape($term);
                $criteria[] = "({$tbl}.ak_{$akHandle} LIKE '%\n{$escapedTerm}\n%')";
            }
            $where = '(' . implode(' OR ', $criteria) . ')';
            $list->filter(false, $where);
            return $list;
	}
    }
 
}