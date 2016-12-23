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
define('TRANSFORMATION_DIRECTORY_SYSTEM', dirname(__FILE__) . '/views/shared/xsl/');
//In case anybody changes the plugin filename we can still serve up the uploaded files
define('TRANSFORMATION_DIRECTORY_WEB',  '/' . basename(dirname($_SERVER['PHP_SELF'])) .
        '/plugins/' . basename(dirname(__FILE__)) . '/views/shared/xsl/');

class OpenSeadragonTEIPlugin extends Omeka_Plugin_AbstractPlugin
{
  const DEFAULT_VIEWER_EMBED = 1;
  const DEFAULT_XSLT_TRANSFORMATION = 'views/shared/xsl/generic.xsl';

  protected $_hooks = array(
    'install',
    'uninstall',
    'initialize',
    'define_acl',
    'public_items_show',
    'config_form',
    'config',
    'public_head',
    'define_routes',
  );

  protected $_filters = array(
    'admin_navigation_main',
    'public_navigation_main',
    'page_caching_whitelist',
    'search_form_default_action',
    'items_search_default_url',
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

      $this->_uninstallOptions();
  }
  /**
    * Initialize the plugin.
    */
  public function hookInitialize()
  {
      add_translation_source(dirname(__FILE__) . '/languages');
      get_view()->addHelperPath(dirname(__FILE__) . '/views/helpers', 'OpenSeadragonTEI_View_Helper');
  }

  public function hookDefineAcl($args)
  {
      $acl = $args['acl']; // get the Zend_Acl

      $indexResource = new Zend_Acl_Resource('OpenSeadragonTEI_Index');

      $acl->add($indexResource);

      $acl->allow(array('super', 'admin'), array('OpenSeadragonTEI_Index'));
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

  public function filterPublicNavigationMain($nav)
  {
    if(get_option('openseadragontei_override_items_show')) {
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
                        'pages' => $pages,
          );
        }
        return $nav;
      } else {
      return;
    }
  }

  public function filterSearchFormDefaultAction()
  {
    if (is_admin_theme()) {
      return '/admin/search';
    }
    return '/viewer/search';
  }

  public function filterItemsSearchDefaultUrl()
  {
    if (is_admin_theme()) {
      return '/admin/items/search';
    }
    return '/viewer/advanced-search';
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
      //put item type logic in view helper
      echo $args['view']->viewer($args['item']->Files, $args['item']->item_type_id, $args['item']);
  }

  public function hookDefineRoutes($args)
  {
    $router = $args['router'];
    $viewerRoute = new Zend_Controller_Router_Route('viewer/:id',
      array('module'=>'open-seadragon-tei', 'controller'=>'viewer', 'action'=>'show'));
    $videoRoute = new Zend_Controller_Router_Route('video/:id',
      array('module'=>'open-seadragon-tei', 'controller'=>'video', 'action'=>'show'));
    $viewerBrowseRoute = new Zend_Controller_Router_Route('viewer/browse/:itemTypeName',
      array('module'=>'open-seadragon-tei', 'controller'=>'viewer', 'action'=>'browse'));
    $viewerSearchRoute = new Zend_Controller_Router_Route('viewer/search',
      array('module'=>'open-seadragon-tei', 'controller'=>'viewer', 'action'=>'search'));
    $viewerAdvancedSearchRoute = new Zend_Controller_Router_Route('viewer/advanced-search',
      array('module'=>'open-seadragon-tei', 'controller'=>'viewer', 'action'=>'advanced-search'));
    $viewerAdvancedSearchResultsRoute = new Zend_Controller_Router_Route('viewer/advanced-search/results',
      array('module'=>'open-seadragon-tei', 'controller'=>'viewer', 'action'=>'results'));
    $router->addRoute('viewer', $viewerRoute);
    $router->addRoute('video', $videoRoute);
    $router->addRoute('viewer-browse', $viewerBrowseRoute);
    $router->addRoute('viewer-search', $viewerSearchRoute);
    $router->addRoute('viewer-search-advanced', $viewerAdvancedSearchRoute);
    $router->addRoute('viewer-search-advanced-results', $viewerAdvancedSearchResultsRoute);
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
      queue_js_file('Saxonce.nocache', 'Saxon-CE/Saxonce');
      queue_css_file('openseadragon', 'screen', false, 'openseadragon');
      queue_js_file('video.min', 'rcl-vjs-nle/node_modules/video.js/dist');
      queue_js_file('videojs-nle-controls.min', 'rcl-vjs-nle/dist');
      queue_js_file('videojs-framerate', 'rcl-vjs-framerate');
      queue_css_file('video-js');
      queue_css_file('player-custom');
      queue_js_file('openseadragon.min', 'openseadragon');
      queue_js_file('openseadragon_tei', 'openseadragon');
      queue_js_url("https://use.fontawesome.com/aadd731529.js");
      queue_js_file('lazyload.min', 'lazyload');
  }
}

?>
