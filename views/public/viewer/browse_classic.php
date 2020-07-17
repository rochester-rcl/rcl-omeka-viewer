<?php
$pageTitle = __('Browse Items');
echo head(array('title'=>$pageTitle,'bodyclass' => 'items browse'));
?>

<h1><?php echo $pageTitle;?> <?php echo __('(%s total)', $total_results); ?></h1>


<nav class="items-nav navigation secondary-nav">
    <?php echo public_nav_items(); ?>
</nav>

<?php echo item_search_filters(); ?>

<?php echo pagination_links(); ?>

<?php if ($total_results > 0): ?>

<?php
$sortLinks[__('Title')] = 'Dublin Core,Title';
$sortLinks[__('Creator')] = 'Dublin Core,Creator';
$sortLinks[__('Date Added')] = 'added';
?>
<div id="sort-links">
    <span class="sort-label"><?php echo __('Sort by: '); ?></span><?php echo browse_sort_links($sortLinks); ?>
</div>

<?php endif; ?>

<?php foreach (loop('items') as $item):
  $itemURL = url('viewer/' . $item->id);
?>

<div class="item hentry">
    <h2><a href="<?php echo $itemURL; ?>"><?php echo metadata('item', array('Dublin Core', 'Title')); ?></a></h2>
    <div class="item-meta">
    <?php if (metadata('item', 'has thumbnail')): ?>
    <div class="item-img">
        <a href="<?php echo $itemURL; ?>"><?php echo item_image('square_thumbnail'); ?></a>
    </div>
    <?php endif; ?>

    <div class="item-dublin-core">
      <?php if ($description = metadata('item', array('Dublin Core', 'Description'), array('snippet'=>250))): ?>
      <div class="item-description">
          <h5>Description:</h5>
          <?php echo $description; ?>
      </div>
      <?php endif; ?>

      <?php if ($creator = metadata('item', array('Dublin Core', 'Creator'), array('snippet'=>250))): ?>
      <div class="item-creator">
          <h5>Creator:</h5> <?php echo $creator; ?>
      </div>
      <?php endif; ?>

      <?php if ($date = metadata('item', array('Dublin Core', 'Date'), array('snippet'=>250))): ?>
      <div class="item-date">
          <h5>Date:</h5> <?php echo $date; ?>
      </div>
      <?php endif; ?>

      <?php if (metadata('item', 'has tags')): ?>
      <div class="tags"><p><strong><?php echo __('Tags'); ?>:</strong>
          <?php echo tag_string('items'); ?></p>
      </div>
      <?php endif; ?>
    </div>
    <?php fire_plugin_hook('public_items_browse_each', array('view' => $this, 'item' =>$item)); ?>

    </div><!-- end class="item-meta" -->
</div><!-- end class="item hentry" -->
<?php endforeach; ?>

<?php echo pagination_links(); ?>

<div id="outputs">
    <span class="outputs-label"><?php echo __('Output Formats'); ?></span>
    <?php echo output_format_list(false); ?>
</div>

<?php fire_plugin_hook('public_items_browse', array('items'=>$items, 'view' => $this)); ?>

<?php echo foot(); ?>
