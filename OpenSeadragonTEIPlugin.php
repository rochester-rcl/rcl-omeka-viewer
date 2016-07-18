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

  const DEFAULT_VIEWER_WIDTH = 500;
  const DEFAULT_VIEWER_HEIGHT = 600;
  const DEFAULT_VIEWER_EMBED = 1;
  const DEFAULT_XSLT_TRANSFORMATION = 'views/shared/xsl/generic.xsl';


  protected $_hooks = array(
    'install',
    'uninstall',
    'initialize',
    'define_acl',
    'public_items_show',
    'public_head',
  );

  protected $_filters = array(
    'admin_navigation_main',
  );

  protected $_options = array(
    'openseadragontei_width' => self::DEFAULT_VIEWER_WIDTH,
    'openseadragontei_height' => self::DEFAULT_VIEWER_HEIGHT,
    'openseadragontei_embed_public' => self::DEFAULT_VIEWER_EMBED,
    'openseadragontei_embed_private' => self::DEFAULT_VIEWER_EMBED,
    'openseadragontei_xsl' => self::DEFAULT_XSLT_TRANSFORMATION,
  );

  public function hookInstall()
    {
      $db = $this->_db;
      $sql = "
      CREATE TABLE IF NOT EXISTS `{$db->prefix}open_seadragon_tei_viewers` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `viewer_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
      `viewer_width` int(10) NOT NULL,
      `viewer_height` int(10) NOT NULL,
      `xsl_viewer_option` varchar(10) COLLATE utf8_unicode_ci NOT NULL, 
      `xsl_url` varchar(500) COLLATE utf8_unicode_ci,
      `item_type_id` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`id`)
      ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
      $db->query($sql);

      $this->_installOptions();
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

    /**
    * Display the image viewer in public items/show.
    */
  public function hookPublicItemsShow($args)
    {
      //put item type logic in view helper
      echo $args['view']->viewer($args['item']->Files, $args['item']->item_type_id, $args['item']);
    }

  public function openseadragon_pyramid($image, $size)
    {
      return openseadragon_create_pyramid($image, $size);
    }

  public function hookPublicHead()
    {
      queue_js_file('Saxonce.nocache', 'Saxon-CE/Saxonce');
      queue_css_file('openseadragon', 'screen', false, 'openseadragon');
      queue_js_file('openseadragon.min', 'openseadragon');
      //queue_js_file('settings', 'openseadragon');
      queue_js_file('openseadragon_tei', 'openseadragon');
      queue_js_url("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js");
      queue_css_url("https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css");
      queue_js_url("https://use.fontawesome.com/aadd731529.js");

    }


}

?>
