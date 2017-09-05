<?php echo $this->partial('common/header-viewer.php');
echo $this->viewer($this->files, $this->itemTypeId, $this->item, $this->page);
echo foot();
?>
