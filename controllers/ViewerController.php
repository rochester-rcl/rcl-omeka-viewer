<?php

class OpenSeadragonTEI_ViewerController extends Omeka_Controller_AbstractActionController
{
  protected $_browseRecordsPerPage = self::RECORDS_PER_PAGE_SETTING;

  public $contexts = array(
        'browse' => array('json', 'dcmes-xml', 'rss2', 'omeka-xml', 'omeka-json', 'atom'),
        'show'   => array('json', 'dcmes-xml', 'omeka-xml', 'omeka-json', 'atom')
  );

  public function showAction()
  {
    $itemId = $this->getParam('id');
    $page = $this->getParam('page');
    $item = $this->_helper->db->getTable('Item')->find($itemId);
    if($item){
      $files = $item->getFiles();
      $this->view->files = $files;
      $this->view->itemTypeId = $item->item_type_id;
      $this->view->item = $item;
      $this->view->page = $page;
    }
  }

  public function browseAction()
  {
    $itemTypeName = $this->getParam('itemTypeName');
    $itemTypeNameInflection = str_replace('-', ' ', $this->getParam('itemTypeName'));
    $pluralName = ucwords(str_replace('_', ' ', $this->view->pluralize($itemTypeName)));
    $db = $this->_helper->db;
    $table = $db->getTable('ItemType');
    $select = $table->getSelect();
    $select->where('UPPER (name) = ?', strtoupper($itemTypeNameInflection));
    $results = $table->fetchObjects($select);

    if(sizeof($results) > 0) {
      $itemTypeId = $results[0]->id;
      $itemTable = $db->getTable('Item');

      // Sort params
      if (!$this->_getParam('sort_field')) {
            $defaultSort = apply_filters("openseadragon_tei_viewers_browse_default_sort",
                $this->_getBrowseDefaultSort(),
                array('params' => $this->getAllParams())
            );
            if (is_array($defaultSort) && isset($defaultSort[0])) {
                $this->setParam('sort_field', $defaultSort[0]);
                if (isset($defaultSort[1])) {
                    $this->setParam('sort_dir', $defaultSort[1]);
                }
            }
        }

      $params = $this->getAllParams();
      $params['item_type_id'] = $itemTypeId;
      $params['public'] = 1;
      $currentPage = $this->getParam('page', 1);
      //$recordsPerPage = $this->_getBrowseRecordsPerPage();
      $totalRecords = $itemTable->count($params);
      $items = $itemTable->findBy($params, $totalRecords, $currentPage);


      // Pagination
      Zend_Registry::set('pagination', array(
        'page' => $currentPage,
        'per_page' => $totalRecords,
        'total_results' => $totalRecords,

      ));

      $this->view->assign(array('items' => $items, 'total_results' => $totalRecords, 'browseTitle' => $itemTypeNameInflection));
    }
  }

  public function resultsAction()
  {
    $this->_doDBQuery('Item');
  }

  public function searchAction()
  {
    $this->_doDBQuery('SearchText');
  }

  public function advancedSearchAction()
  {

  }

  private function _doDBQuery($modelName)
  {
    // Respect only GET parameters when browsing.
       $this->getRequest()->setParamSources(array('_GET'));
       $searchTable = $db = $this->_helper->db->getTable($modelName);

       // Inflect the record type from the model name.
       $pluralName = $this->view->pluralize($searchTable->getTableAlias());
       // Apply controller-provided default sort parameters
       if (!$this->_getParam('sort_field')) {
           $defaultSort = apply_filters("{$pluralName}_browse_default_sort",
               $this->_getBrowseDefaultSort(),
               array('params' => $this->getAllParams())
           );
           if (is_array($defaultSort) && isset($defaultSort[0])) {
               $this->setParam('sort_field', $defaultSort[0]);
               if (isset($defaultSort[1])) {
                   $this->setParam('sort_dir', $defaultSort[1]);
               }
           }
       }

       $params = $this->getAllParams();
       // $recordsPerPage = $this->_getBrowseRecordsPerPage($pluralName);
       $currentPage = $this->getParam('page', 1);
       $totalRecords = $searchTable->count($params);
       // Get the records filtered to Omeka_Db_Table::applySearchFilters().
       $records = $searchTable->findBy($params, $totalRecords, $currentPage);


       // Add pagination data to the registry. Used by pagination_links().
       if ($totalRecords) {
           Zend_Registry::set('pagination', array(
               'page' => $currentPage,
               'per_page' => $totalRecords,
               'total_results' => $totalRecords,
           ));
       }
       $this->view->assign(array($pluralName => $records, 'total_results' => $totalRecords,));
  }

}
