<?php echo $this->partial('common/header-rcl.php');
  echo $this->viewer($this->files, $this->itemTypeId, $this->item, $this->page, $this->anchor);
  echo foot();
?>
