<?php
$pageTitle = __('Search Results') . ' ' . __('(%s total)', $total_results);
echo head(array('title' => $pageTitle, 'bodyclass' => 'viewer browse'));
$searchRecordTypes = get_search_record_types();
?>
<h1><?php echo $pageTitle; ?></h1>
<?php echo search_filters(); ?>
<?php if ($total_results > 0): ?>
<?php echo pagination_links(); ?>
<?php $formattedSearchResults = sort_search_results($search_texts); ?>
<?php foreach ($formattedSearchResults as $recordType => $itemTypeArray): ?>
  <?php if ($recordType === 'Item'): ?>
    <?php foreach ($itemTypeArray as $itemType => $itemArray): ?>
      <?php if ($itemType !== 'unknown'): ?>
        <div class="item-type-container <?php echo $itemType; ?> show">
          <h3 class="search-result-item-type-title"><?php echo(de_slugify($itemType)); ?></h3>
          <div class="item-records" id="item-records-block">
            <?php foreach ($itemArray as $item): ?>
              <?php if($item['image_markup'] !== NULL): ?>
                <div class="item-flexbox-record">
                  <a class="item-flexbox-record-link" href="<?php echo $item['url']; ?>">
                    <div class="item-img">
                        <?php echo $item['image_markup']; ?>
                    </div>
                  </a>
                  <span class="item-flexbox-record-title"><?php echo $item['title']; ?></span>
                </div><!-- end class="item hentry" -->
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
    <?php endif; ?>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="record-type-container <?php echo $recordType; ?> show">
    <h3 class="search-result-record-type-title"><?php echo(de_slugify($recordType)); ?></h3>
      <div class="item-records" id="item-records-block">
        <?php foreach ($itemTypeArray as $item): ?>
        <div class="item-flexbox-record">
        <a class="item-flexbox-record-link" href="<?php echo $item['url']; ?>">
          <div class="item-img">
            <?php echo $item['image_markup']; ?>
          </div>
        </a>
        <span class="item-flexbox-record-title"><?php echo $item['title']; ?></span>
      </div>
    </div>
  <?php endforeach; ?>
  <?php endif; ?>
<?php endforeach; ?>

<?php echo pagination_links(); ?>
<?php echo $this->partial('common/search-results-filter.php', array('formatted_results' => $formattedSearchResults, 'results_type' => 'record')); ?>
<?php else: ?>
<div id="no-results">
    <p><?php echo __('Your query returned no results.');?></p>
</div>
<?php endif; ?>
<script type="text/javascript">
  var lazyLoad = new LazyLoad();
</script>

<?php echo foot(); ?>
