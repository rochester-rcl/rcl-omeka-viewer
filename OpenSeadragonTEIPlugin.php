<?php
/**
* OpenSeadragonTEI integrates a tiled OpenSeadragon viewer with an XSLT instance
*
*/

/**
*
* OpenSeadragonTEI plugin
* @package Omeka\Plugins\OpenSeadragonTEI
*/

require_once dirname(__FILE__) . '/helpers/OpenSeadragonTEIFunctions.php';
require_once dirname(__FILE__) . '/helpers/OpenSeadragonFunctions.php';
$appRoot = getcwd();
define('VIEWER_ROOT', dirname(__FILE__));
/*define('TRANSFORMATION_DIRECTORY_SYSTEM', dirname(__FILE__) . '/views/shared/xsl/');
//In case anybody changes the plugin filename we can still serve up the uploaded files
define('TRANSFORMATION_DIRECTORY_WEB', 'plugins/' . basename(__DIR__) . '/views/shared/xsl');*/
define('TRANSFORMATION_DIRECTORY_SYSTEM', FILES_DIR . '/xsl/');
//In case anybody changes the plugin filename we can still serve up the uploaded files
define('TRANSFORMATION_DIRECTORY_WEB', WEB_DIR . '/files/xsl');

class OpenSeadragonTEIPlugin extends Omeka_Plugin_AbstractPlugin
{
  const DEFAULT_VIEWER_EMBED = 1;
  const DEFAULT_XSLT_TRANSFORMATION = 'views/shared/xsl/generic.xsl';
  const XML_TEXT_ELEMENT_SET_NAME = 'XML Search';
  const XML_TEXT_ELEMENT_NAME = 'Text';
  const XML_EXT = 'xml';
  const XML_TEXT_ELEMENT_RECORD_TYPE = 'Item';

  protected $_hooks = array(
    'install',
    'uninstall',
    'initialize',
    'upgrade',
    'define_acl',
    'config_form',
    'config',
    'public_head',
    'define_routes',
    'public_items_show',
    'after_save_item',
    'after_delete_file',
  );

  protected $_filters = array(
    'admin_navigation_main',
    'public_navigation_main',
    'page_caching_whitelist',
    'exhibit_layouts',
    'display_elements',
    'disableDisplay' => array('Display', 'Item', self::XML_TEXT_ELEMENT_SET_NAME, self::XML_TEXT_ELEMENT_NAME),
    'disableForm' => array('Form', 'Item', self::XML_TEXT_ELEMENT_SET_NAME, self::XML_TEXT_ELEMENT_NAME),
  );

  protected $_options = array(
    'openseadragontei_override_items_show' => self::DEFAULT_VIEWER_EMBED,
  );

  public function hookInstall()
  {
      $db = $this->_db;
      $sql = "
      CREATE TABLE IF NOT EXISTS `{$db->prefix}open_seadragon_tei_viewers` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `viewer_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
      `xsl_viewer_option` tinyint(1) COLLATE utf8_unicode_ci NOT NULL,
      `override_items_show_option` tinyint(1) COLLATE utf8_unicode_ci NOT NULL,
      `xsl_url` varchar(500) COLLATE utf8_unicode_ci,
      `item_type_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
      $db->query($sql);

      $this->_installOptions();
      $this->createElementSet();

  }

  private function createElementSet()
  {
    if ($this->_db->getTable('ElementSet')->findByName(self::XML_TEXT_ELEMENT_SET_NAME)) {
      // throw new Exception('An element set by the name ' . self::XML_TEXT_ELEMENT_SET_NAME . ' already exists. You must delete that element set to install this plugin.');
      // do nothing
      return;
    }
    $elementSetMeta = array('name' => self::XML_TEXT_ELEMENT_SET_NAME,
                            'description' => 'This element set indexes the text from attached XML files and makes them searchable.');
    $elements = array(array('name' => self::XML_TEXT_ELEMENT_NAME,
                            'description' => 'Text extracted from XML files attached to this item.'));
    insert_element_set($elementSetMeta, $elements);
  }


  public function isTextExtracted($item) {
    $elements = array_filter($this->_db->getTable('ElementText')->findByRecord($item), function($element) {
      return $element->element_id === $this->getXMLSearchElementId();
    });
    return count($elements) > 0 === true;
  }

  public function saveItemXMLText(Item $item)
  {
    foreach ($item->Files as $file) {
      $this->saveFileXMLText($file, $item);
    }
  }

  public function updateSearchText($item, $text)
  {
    $st = $this->_db->getTable('SearchText')->findByRecord('Item', $item->id);
    if (isset($st)) {
      $st->text .= ' ' . $text;
      $st->save();
      return true;
    }
    return false;
  }

  public function saveFileXMLText(File $file, $item)
  {
    if ($file->getExtension() != self::XML_EXT) {
      return;
    }

    $elementId = $this->getXMLSearchElementId();

    if ($this->isTextExtracted($item)) {
      // delete the element
      $item->deleteElementTextsByElementId(array($elementId));
    }
    $et = new ElementText;
    $et->record_id = $file->item_id;
    $et->element_id = $elementId;
    $et->record_type = self::XML_TEXT_ELEMENT_RECORD_TYPE;
    $et->html = false;
    $et->text = $this->extractXML($file);
    if ($et->text != NULL) {
      $stStatus = $this->updateSearchText($item, $et->text);
      if (!$stStatus) {
        $item->addSearchText($et->text);
      }
      $et->save();
    }
  }

  private function getXMLSearchElementId()
  {
    return $this->_db->getTable('Element')->findByElementSetNameAndElementName(self::XML_TEXT_ELEMENT_SET_NAME,
                                             self::XML_TEXT_ELEMENT_NAME)->id;
  }

  private function getXMLSearchElementFileId()
  {
    return $this->_db->getTable('Element')->findByElementSetNameAndElementName(self::XML_TEXT_ELEMENT_SET_NAME,
                                             self::XML_TEXT_ELEMENT_FILE_NAME)->id;
  }

  public function extractXML(File $file)
  {
    $path = FILES_DIR . "/original/" . $file->filename;
    $xml = simplexml_load_file($path);
    $node = dom_import_simplexml($xml);
    return $node->textContent;
  }

  public function hookConfigForm()
  {
    echo get_view()->partial('plugins/openseadragontei-config-form.php');
  }

  public function hookConfig()
  {
    set_option('openseadragontei_override_items_show', (int) (boolean) $_POST['openseadragontei_override_items_show']);
    set_option('openseadragontei_custom_nav_name', $_POST['openseadragontei_custom_nav_name']);
  }

  public function hookUninstall()
  {
      // Drop the table.
      $db = $this->_db;
      $sql = "DROP TABLE IF EXISTS `{$db->prefix}open_seadragon_tei_viewers`";
      $db->query($sql);
      $this->_db->getTable('ElementSet')->findByName(self::XML_TEXT_ELEMENT_SET_NAME)->delete();
      $this->_uninstallOptions();
  }
  /**
    * Initialize the plugin.
    */
  public function hookInitialize()
  {
      add_translation_source(dirname(__FILE__) . '/languages');
      get_view()->addHelperPath(dirname(__FILE__) . '/views/helpers', 'OpenSeadragonTEI_View_Helper');
      add_shortcode('video_player', array($this, 'get_video_player'));

  }

  public function hookUpgrade()
  {
    $this->createElementSet();
    $this->_createXSLDir();
  }

  public function hookDefineAcl($args)
  {
      $acl = $args['acl']; // get the Zend_Acl

      $indexResource = new Zend_Acl_Resource('OpenSeadragonTEI_Index');

      $acl->add($indexResource);

      $acl->allow(array('super', 'admin'), array('OpenSeadragonTEI_Index'));
  }

  public function hookAfterSaveItem($args)
  {
    $this->saveItemXMLText($args['record']);
  }
  // $file is actually an array of records
  public function hookAfterDeleteFile($files)
  {
    foreach($files as $file) {
      if ($file->getExtension() == 'xml') {
        $item = $file->getItem();
        $elementId = $this->getXMLSearchElementId();
        $item->deleteElementTextsByElementId(array($elementId));
      }
    }
  }

  public function filterAdminNavigationMain($nav)
  {
      $nav[] = array(
          'label' => __('TEI Viewer'),
          'uri' => url('open-seadragon-tei'),
          'resource' => 'OpenSeadragonTEI_Index',
          'privilege' => 'index',
      );
      return $nav;
  }

  public function filterExhibitLayouts($layouts)
  {
    $layouts['osd-file'] = array(
      'name' => 'OpenSeadragon File Layout',
      'description' => 'Adds a link to the OSD viewer from a layout',
    );

    $layouts['osd-file-text'] = array(
      'name' => 'OpenSeadragon File & Text Layout',
      'description' => 'Adds a link to the OSD viewer from a layout, along with room for text',
    );

    $layouts['osd-gallery'] = array(
      'name' => 'OpenSeadragon Gallery Layout',
      'description' => 'Adds a link to the OSD viewer from a Gallery layout',
    );

    return $layouts;
  }

  public function filterPublicNavigationMain($nav)
  {
    if(!get_option('openseadragontei_override_items_show')) {
      $itemTypes = $this->getAllViewerItemTypes();
      $customNav = get_option('openseadragontei_custom_nav_name');
      if($itemTypes) {
        $pages = array();
        foreach($itemTypes as $itemType) {
          $page = array('label' => __($itemType['name']),
                        'uri' => $itemType['uri'],
          );
          array_push($pages, $page);
        }
        $nav[] = array(
                        'label' => __( $customNav ? $customNav : 'Viewer'),
                        'uri' => '/',
                        'pages' => $pages,);
        }
        return $nav;
      }
      return $nav;
  }

  public function filterDisplayElements($elementsBySet)
  {
    if (!is_admin_theme()) {
      unset($elementsBySet[self::XML_TEXT_ELEMENT_SET_NAME]);
    }
    return $elementsBySet;
  }

  private function _createXSLDir()
  {
    if (!file_exists(TRANSFORMATION_DIRECTORY_SYSTEM)) {
      mkdir(TRANSFORMATION_DIRECTORY_SYSTEM);
    }
  }

  private function _removeXSLDir()
  {
    if (file_exists(TRANSFORMATION_DIRECTORY_SYSTEM)) {
      rmdir(TRANSFORMATION_DIRECTORY_SYSTEM);
    }
  }

  private function getAllViewerItemTypes()
  {
    $db = $this->_db;
    $sql = "SELECT item_type_id FROM `{$db->prefix}open_seadragon_tei_viewers`";
    $stmt = $db->query($sql);
    $viewerItemTypeIds = $stmt->fetchAll();
    if($viewerItemTypeIds) {
      $table = $db->prefix . 'item_types';
      $itemTypeQuery = $db->select()
      ->from($table)
      ->where('id IN(?)', $viewerItemTypeIds);
      $stmt = $db->query($itemTypeQuery);
      $results = $stmt->fetchAll();

      if($results) {
        $itemTypeArray = array();
        foreach($results as $itemTypeObj) {
          $item = array( 'name' => $itemTypeObj['name'],
                         'id' => $itemTypeObj['id'],
                         'uri' => url('viewer/browse/' . str_replace(' ', '-', strtolower($itemTypeObj['name']))),
                       );
          array_push($itemTypeArray, $item);
        }
        return($itemTypeArray);
      } else {
        return;
      }
    }
  }

    /**
    * Display the image viewer in public items/show.
    */
  public function hookPublicItemsShow($args)
  {
      $helper = $args['view']->getHelper('viewer');

      $extensions = array_merge($helper->_supportedImageExtensions,
        $helper->_supportedVideoExtensions,
        $helper->_supportedDocExtensions,
        $helper->_supportedAudioExtensions);

      if(get_option('openseadragontei_override_items_show') && !is_admin_theme()) {
        $viewerInfo = open_seadragon_tei_get_viewer($args['item']->item_type_id);
        if (check_files($args['item'])) {
          if (sizeof($viewerInfo) > 0 && validate_extensions($args['item']->Files, $extensions)) {
            header("Location: " . absolute_url('viewer/' . $args['item']->id));
            exit;
          }
        }
      }
      // to append to items show
      //echo $args['view']->viewer($args['item']->Files, $args['item']->item_type_id, $args['item']);
  }

  public function hookDefineRoutes($args)
  {
    $router = $args['router'];
    $viewerRoute = new Zend_Controller_Router_Route('viewer/:id/:page',
      array('module'=>'open-seadragon-tei', 'controller'=>'viewer', 'action'=>'show', 'page' => 1));

    $videoRoute = new Zend_Controller_Router_Route('video/:id',
      array('module'=>'open-seadragon-tei', 'controller'=>'video', 'action'=>'show'));
    $viewerBrowseRoute = new Zend_Controller_Router_Route('viewer/browse/:itemTypeName',
      array('module'=>'open-seadragon-tei', 'controller'=>'viewer', 'action'=>'browse'));
    $router->addRoute('viewer', $viewerRoute);
    $router->addRoute('video', $videoRoute);
    $router->addRoute('viewer-browse', $viewerBrowseRoute);
  }

  public function filterPageCachingWhitelist($whitelist)
  {
    $itemTypes = $this->getAllViewerItemTypes();
    foreach($itemTypes as $itemType) {
      $whitelist[itemType[uri]] = array('cache'=>true);
    }
    return $whitelist;
  }

  public function openseadragon_pyramid($image, $size)
  {
      return openseadragon_create_pyramid($image, $size);
  }

  public function hookPublicHead()
  {

  }

  public function get_video_player($args, $view)
  {
    $videoUrl = $args['url'];
    $mime = $args['mime'];
    echo $view->partial('common/video-viewer.php', array('videoUrl' => $videoUrl, 'mime' => $mime));
  }

  public static function disableForm($html, $inputNameStem)
  {
    return __v()->formTextArea($inputNameStem . '[text]',
                               $value,
                               array('disabled' => 'disabled',
                                     'class' => 'textinput',
                                     'rows' => 15,
                                     'cols' => 50));
  }

  public static function disableDisplay($text, $record)
  {
    if (!is_admin_theme()) {
      $text = '';
    }
    return $text;
  }

}

?>
