<?php
    echo head(array('title' => __('OpenSeadragon TEI Viewer')));
    echo flash();
?>
<div>
  <table class="full">
    <thead>
        <tr>
            <?php $browseHeadings[__('Title')] = 'Title'; ?>
            <?php echo browse_sort_links($browseHeadings, array(
                'link_tag' => 'th scope="col"', 'list_tag' => ''));
            ?>
        </tr>
    </thead>
    <tbody>
<?php foreach(loop('OpenSeadragonTEIViewer') as $viewer): ?>
    <tr>
            <td>
                <span class="title">
                    <a href="<?php echo html_escape(record_url('OpenSeadragonTEIViewer')); ?>">
                        <?php echo metadata('OpenSeadragonTEIViewer', 'viewer_name'); ?>
                    </a>
                </span>
                <ul class="action-links group">
                    <li><a class="edit" href="<?php echo html_escape(record_url('OpenSeadragonTEIViewer', 'edit')); ?>">
                        <?php echo __('Edit'); ?>
                    </a></li>
                    <li><a class="delete-confirm" href="<?php echo html_escape(record_url('OpenSeadragonTEIViewer', 'delete-confirm')); ?>">
                        <?php echo __('Delete'); ?>
                    </a></li>
                </ul>
            </td>
        </tr>
  <?php endforeach; ?>
</tbody>
</table>
  <a class="add-viewer button small green" href="<?php echo html_escape(url('open-seadragon-tei/index/add')); ?>"><?php echo __('Add a Viewer'); ?></a>

</div>
