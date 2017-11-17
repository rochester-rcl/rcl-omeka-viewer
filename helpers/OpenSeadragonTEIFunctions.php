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
  if ($item_type_id) {
    $db = get_db();
    $select = $db->select()
    ->from('omeka_open_seadragon_tei_viewers')
    ->where('item_type_id = '.$item_type_id);

    $stmt = $db->query($select);
    $result = $stmt->fetchAll();

    return $result;
  } else {
    return array();
  }
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

function lazy_load_image($format, $item)
{
  $file = $item->getFile();
  $thumbnail = $file->getWebPath($format);
  $thumbnailConstraint = get_option('square_thumbnail_constraint');
  $markup = '<img class="pre-loading" data-original="' . $thumbnail . '" width="' . $thumbnailConstraint . '" height="' . $thumbnailConstraint . '"/>';
  return $markup;
}

function get_item_type_meta($item)
{
  $itemTypeId = $item->item_type_id;
  $db = get_db();
  $select = $db->select()
  ->from($db->prefix . 'item_types')
  ->where('id = ' . $item->item_type_id);
  $stmt = $db->query($select);
  $results = $stmt->fetchAll();
  return $results[0];
}

function slugify($name) {
  return strtolower(str_replace(' ', '-', $name));
}

function de_slugify($name) {
  return ucwords(str_replace('-', ' ', $name));
}

function sort_search_results($searchTextArray)
{
  $recordTypes = array_unique(array_map(function($searchText){
    $recordType = $searchText['record_type'];
    if ($recordType === 'SimplePagesPage') return 'page';
    return $searchText['record_type'];
  }, $searchTextArray));

  $formattedResults = array_fill_keys($recordTypes, array());
  $recordResultArray = array();
  $itemResultArray = array();
  $itemTypeInfo = array();
  foreach($searchTextArray as $searchText) {
    $recordType = $searchText['record_type'];
    $recordResult;
    $itemTypes = array();
    if($recordType === 'Item') {
      $item = get_record_by_id($searchText['record_type'], $searchText['record_id']);
      if ($item->item_type_id) {
        $itemTypeName = get_item_type_meta($item)['name'];
      } else {
        $itemTypeName = 'unknown';
      }
      $itemTypeSlug = slugify($itemTypeName);
      $recordResult = array(
                        'title' => metadata($item, array('Dublin Core', 'Title')),
                        'item_type_id' => $item->item_type_id,
                        'item_type_name' => $itemTypeName,
                        'item_type_slug' => $itemTypeSlug,
                        'image_markup' => metadata($item, 'has files') ? lazy_load_image('square_thumbnail', $item) : NULL,
                        'url' => url('viewer/' . $item->id),
                       );
      if(!array_key_exists($itemTypeSlug, $itemResultArray)) {
        $itemResultArray[$itemTypeSlug] = array($recordResult);
      } else {
        array_push($itemResultArray[$itemTypeSlug], $recordResult);
      }
      $formattedResults[$recordType] = $itemResultArray;
    }
    if($recordType === 'SimplePagesPage') {
      $record = get_record_by_id($searchText['record_type'], $searchText['record_id']);
      $recordResult = array(
                      'title' => $record->title,
                      'url' => $record->getRecordUrl(),
                      'image_markup' => '<i class="fa fa-file-text fa-5x" aria-hidden="true"></i>',
                      'record_type_slug' => 'simple-page',
      );
      array_push($recordResultArray, $recordResult);
      $formattedResults['page'] = $recordResultArray;
    }

  }
  return $formattedResults;
}

function sort_item_search_results($items)
{
  $itemTypeArray = array();
  foreach($items as $item) {
    $itemTypeId = $item->item_type_id;
    if ($itemTypeId) {
      $itemTypeName = get_item_type_meta($item)['name'];
    } else {
      $itemTypeName = 'unknown';
    }
    $itemTypeSlug = slugify($itemTypeName);
    $itemResult = array(
                      'title' => metadata($item, array('Dublin Core', 'Title')),
                      'item_type_id' => $item->item_type_id,
                      'item_type_name' => $itemTypeName,
                      'item_type_slug' => $itemTypeSlug,
                      'image_markup' => metadata($item, 'has files') ? lazy_load_image('square_thumbnail', $item) : NULL,
                      'url' => url('viewer/' . $item->id),
                     );
    if (!array_key_exists($itemTypeSlug, $itemTypeArray)) {
      $itemTypeArray[$itemTypeSlug] = array($itemResult);

    } else {
      array_push($itemTypeArray[$itemTypeSlug], $itemResult);
    }
  }
  return $itemTypeArray;
}

function get_featured_encounters($numEncounters)
{
  $results = get_records('Exhibit', array('featured' => 1,
                                          'tags' => 'encounter',
                                          'order' => 'd',
                                          'public' => 1), $numEncounters);
  $encounterMarkupArray = array();
  foreach($results as $record) {
    $containerOpen = '<div class="hp-buttons-imgs">';
    $encounterTitle = '<div class="hp_buttons_imgs_text">' . $record->title . '</div>';
    $imageMarkup = record_image($record, 'fullsize');
    $encounterLink = '<a href="' . '/encounters/' . $record->slug . '/page/1' . '">' . $imageMarkup . '</a>';
    $containerClose = '</div>';
    $containerOpen .= $encounterTitle .= $encounterLink .= $containerClose;
    array_push($encounterMarkupArray, $containerOpen);
  }
  return implode($encounterMarkupArray);
}

function check_files($item)
{
  return metadata($item, 'has files') === TRUE;
}

function total_gallery_items($items)
{
  $galleryItems = array_filter($items, 'check_files');
  return sizeof($galleryItems);
}

function osd_viewer_layout_link($attachment, $imageType)
{
  $uri = url('viewer/' . $attachment->item_id);
  $file = $attachment->getFile();
  $imageTag = file_image($imageType, array('class' => 'osd-viewer-thumbnail'), $file);
  if ($imageTag) {
    $html = '<a href="' . $uri . '" class="osd-viewer-image-link">' . $imageTag . '</a>';
  }

  if (isset($html)) {
      $html .= osd_exhibit_attachment_caption($attachment);
  }

  return apply_filters('exhibit_attachment_markup', $html, compact('attachment', 'fileOptions', 'linkProps', 'forceImage'));
}

function validate_extensions($files, $extensions)
{
  foreach($files as $file) {
    $ext = strtolower(pathinfo($file->original_filename, PATHINFO_EXTENSION));
    if (!in_array($ext, $extensions)) {
      return FALSE;
    }
  }
  return TRUE;
}

function osd_exhibit_attachment_gallery($attachments, $fileOptions = array(), $linkProps = array())
{
  if (!isset($fileOptions['imageSize'])) {
    $fileOptions['imageSize'] = 'square_thumbnail';
  }
  $html = '';
  foreach ($attachments as $attachment) {
    $html .= '<div class="exhibit-item exhibit-gallery-item">';
    $html .= osd_viewer_layout_link($attachment, $fileOptions['imageSize']);
    $html .= '</div>';
  }

  return apply_filters('exhibit_attachment_gallery_markup', $html,
    compact('attachments', 'fileOptions', 'linkProps'));
}

function osd_exhibit_attachment_caption($attachment)
{
  if (!is_string($attachment['caption']) || $attachment['caption'] == '') {
            return '';
  }
  $html = '<div class="exhibit-item-caption">'
    . $attachment['caption']
    . '</div>';
  return apply_filters('exhibit_attachment_caption', $html, array(
    'attachment' => $attachment
  ));
}
