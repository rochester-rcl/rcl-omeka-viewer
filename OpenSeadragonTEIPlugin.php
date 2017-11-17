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
    'config_form',
    'config',
    'public_head',
    'define_routes',
    'public_items_show'
  );

  protected $_filters = array(
    'admin_navigation_main',
    'public_navigation_main',
    'page_caching_whitelist',
    'exhibit_layouts',
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
      add_shortcode('video_player', array($this, 'get_video_player'));

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
            header("Location: " . url('viewer/' . $args['item']->id));
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
}

?>
