<?php

class OpenSeadragonTEI_VideoController extends Omeka_Controller_AbstractActionController
{
  public function showAction()
    {
      $itemId = $this->getParam('id');
      $item = $this->_helper->db->getTable('Item')->find($itemId);
      if($item)
      {
        $files = $item->getFiles();
        if($files){
          $fileUrls = array();
          foreach($files as $file){
            if(strpos($file->mime_type, 'video') !== false){
              $this->view->videoUrl = file_display_url($file, $format="original");
              $this->view->videoMimeType = $file->mime_type;
            }
            if(strpos($file->mime_type, 'image') !== false){
              $this->view->poster = file_display_url($file, $format="fullsize");
            }
          }
        }
      }
    }
}
