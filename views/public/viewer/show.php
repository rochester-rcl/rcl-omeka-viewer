<?php echo $this->partial('common/header-viewer.php');
  echo $this->viewer($this->files, $this->itemTypeId, $this->item, $this->page, $this->anchor);
  echo $this->partial('common/footer-viewer.php');
?>
