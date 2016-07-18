<?php
/**
 * OpenSeadragon TEI Viewer Plugin (based on DPLA Omeka OpenSeadragon Plugin)
 *
 * @copyright Copyright 2016 River Campus Libraries
 * @copyright Copyright 2007-2016 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

function open_seadragon_tei_get_item_types(){
  $db = get_db();
  $sql = $db->query("SELECT DISTINCT id, name FROM `{$db->prefix}item_types`");
  $results = $sql->fetchAll();
  $itemTypes = array();
  foreach($results as $result){
    $itemTypes[$result['id']] = $result['name'];
  }
  return $itemTypes;
}

function open_seadragon_tei_get_viewer($item_type_id)
{
  $db = get_db();
  $select = $db->select()
  ->from('omeka_open_seadragon_tei_viewers')
  ->where('item_type_id = '.$item_type_id);

  $stmt = $db->query($select);
  $result = $stmt->fetchAll();

  return $result;

}

function open_seadragon_tei_transform_xml($xmlFileUrl, $xslFileUrl)
{
  $xmlDocument = new DOMDocument();
  $xmlDocument->load($xmlFileUrl);

  $xslDocument = new DOMDocument();
  $xslDocument->load($xslFileUrl);

  $processor = new XSLTProcessor();
  $processor->importStylesheet($xslDocument);

  $html = $processor->transformToDoc($xmlDocument);
  return $html;
}

function open_seadragon_tei_generate_upload_web_path($filename)
{
  $base = basename($filename);
  $url = TRANSFORMATION_DIRECTORY_WEB . $base;
  return $url;
}
