<?php
$pageTitle = __($this->browseTitle);
echo head(array('title'=>$pageTitle,'bodyclass' => 'viewer browse'));
?>

<?php if (sizeof($total_results) > 0): ?>
  <?php $formattedResults = sort_item_search_results($items); ?>

  <h1 class="item-type-title">
    <?php echo $pageTitle;?>
    <?php echo __('(%s total)', $total_results); ?>
  </h1>

  <nav class="items-nav navigation secondary-nav">
      <?php echo public_nav_items(); ?>
      <?php if ($total_results > 0): ?>

      <?php
      $sortLinks[__('Title')] = 'Dublin Core,Title';
      $sortLinks[__('Creator')] = 'Dublin Core,Creator';
      $sortLinks[__('Date Added')] = 'added';
      ?>
      <div id="sort-links">
          <span class="sort-label"><?php echo __('Sort by: '); ?></span>
          <?php echo browse_sort_links($sortLinks); ?>
      </div>
      <?php echo item_search_filters(); ?>
      <?php echo pagination_links(); ?>
      <?php endif; ?>
  </nav>
  <?php foreach ($formattedResults as $itemType => $itemArray): ?>
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
  <script type="text/javascript">
    var lazyLoad = new LazyLoad();
  </script>
  <?php echo $this->partial('common/search-results-filter.php', array('formatted_results' => $formattedResults, 'results_type' => 'item')); ?>
<?php else: ?>
  <div id="no-results">
      <p><?php echo __('Your query returned no results.');?></p>
  </div>
<?php endif; ?>
<?php echo foot(); ?>
