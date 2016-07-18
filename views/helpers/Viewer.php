<?php

class OpenSeadragonTEI_View_Helper_Viewer extends Zend_View_Helper_Abstract
{

  protected $_supportedExtensions = array('bmp', 'gif', 'ico', 'jpeg', 'jpg',
                                          'png', 'tiff', 'tif', );
  /**
   * Return a OpenSeadragon image viewer for the provided files.
   *
   * @param File|array $files A File record or an array of File records.
   * @param int $width The width of the image viewer in pixels.
   * @param int $height The height of the image viewer in pixels.
   * @return string|null
   */
  public function viewer($files, $item_type_id, $item)
  {
      if (!is_array($files)) {
          $files = array($files);
      }
      // Filter out invalid images.
      $validFiles = array();
      $validFiles['images'] = array();
      $validFiles['xml'] = array();
      foreach ($files as $file) {
          // A valid image must be a File record.
          if (!($file instanceof File)) {
              continue;
          }

          $extension = pathinfo($file->original_filename, PATHINFO_EXTENSION);
          // Catch XML files first
          if (strtolower($extension) == 'xml'){
            $validFiles['xml'] = $file;
          }
          // A valid image must have a supported extension.
          if (!in_array(strtolower($extension), $this->_supportedExtensions)) {
              continue;
          }
          $validFiles['images'][] = $file;
      }
      // Return if there are no valid images.
      if (!$validFiles) {
          return;
      }
      $viewer = $this->getViewer($item_type_id);
      if($viewer){
        $viewerName = strtolower($viewer['viewer_name']);
        $viewerNameClean = str_replace(' ', '-', $viewerName);
        $openSeadragonViewer = array(
          'width' => $viewer['viewer_width'],
          'height' => $viewer['viewer_height'],
          'name' => $viewerNameClean,
          'buttonPath' => src('images/', 'openseadragon'),
          'tileSources' => $this->getTileSources($validFiles['images']),
          'imageCount' => sizeof($validFiles['images']),
          'metadata' => $this->getMetadata($item),
          'tempImage' => html_escape($validFiles['images'][0]->getWebPath('original')),
          'osdViewer' => 'image',
        );
        if($viewer['xsl_viewer_option'] == 'true'){
          $openSeadragonViewer['xslURL'] = html_escape(open_seadragon_tei_generate_upload_web_path($viewer['xsl_url']));
          $openSeadragonViewer['osdViewer'] = 'tei';
          if($validFiles['xml']){
            $xml = $validFiles['xml'];
            $openSeadragonViewer['xmlURL'] = html_escape($xml->getWebPath('original'));
          }
        } else {
          $openSeadragonViewer['osdViewer'] = 'image';
        }
        
        return $this->view->partial('common/viewer.php', array(
            'viewer' => $openSeadragonViewer,
          ));
      } else {
        return;
      }
    }
    public function getViewer($item_type_id)
    {
      $viewer = open_seadragon_tei_get_viewer($item_type_id);
      if(!$viewer){
        return;
      } else {
        return $viewer[0];
      }
    }

    public function getTileSources($imageArray){
      $imageCount = sizeof($imageArray);
      if($imageCount > 1){
        $tileSources = array();
        foreach($imageArray as $image){
          $pyramid = openseadragon_create_pyramid($image, $sequence = TRUE);
          $tileSources[] = $pyramid;
        }
          return $tileSources;
      } else {
          $pyramid = openseadragon_create_pyramid($imageArray[0], $sequence = FALSE);
          return $pyramid;
      }
    }

    public function getMetadata($item){
      $escapedMetadata = '';
      $meta = all_element_texts($item, array('return_type' => 'array'));
      foreach($meta as $key=>$value){
        foreach($value as $title=>$meta){
          $escapedMetadata .= '<span class="item-metadata-element"><h4 class="item-meta-head">' . $title . '</h4><div class="item-meta-value">' . $meta[0] . '</div></span>';
        }
      }
      return $escapedMetadata;
    }


}

 ?>
