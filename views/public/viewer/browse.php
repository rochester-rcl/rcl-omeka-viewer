<?php
$pageTitle = __($this->browseTitle);
echo head(array('title'=>$pageTitle,'bodyclass' => 'viewer browse'));
?>

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
<div class="item-type-description">
  <?php echo(get_item_type_meta($items[0])['description']); ?>
</div>
<div class="item-records" id="item-records-block">
<?php foreach (loop('items') as $item): ?>
<?php $itemTitle = metadata('item', array('Dublin Core', 'Title'));
      $itemURL = url('viewer/' . $item->id);
 ?>
 <?php if (metadata('item', 'has files')): ?>
  <div class="item-flexbox-record">
      <a class="item-flexbox-record-link" href="<?php echo $itemURL; ?>">
        <div class="item-img">
            <?php echo lazy_load_image('square_thumbnail', $item); ?>
        </div>
      </a>
      <span class="item-flexbox-record-title"><?php echo $itemTitle ?></span>
  </div><!-- end class="item hentry" -->
  <?php endif; ?>
  <?php fire_plugin_hook('public_items_browse_each', array('view' => $this, 'item' =>$item)); ?>
<?php endforeach; ?>
</div>
<?php echo pagination_links(); ?>

<!--<div id="outputs">
    <span class="outputs-label"><?php echo __('Output Formats'); ?></span>
    <?php echo output_format_list(false); ?>
</div>-->

<?php fire_plugin_hook('public_items_browse', array('items'=>$items, 'view' => $this)); ?>

<script type="text/javascript">
  var lazyLoad = new LazyLoad();
</script>

<?php echo foot(); ?>
<!--- Google Analytics --->
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-45873735-2', 'auto');
  ga('send', 'pageview');

</script>
