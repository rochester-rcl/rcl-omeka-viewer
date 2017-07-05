<h2>Global Configuration</h2>

<div class="field">
    <div id="openseadragontei_override_items_show" class="two columns alpha">
        <label for="openseadragontei_override_items_show"><?php echo __('override item show pages?'); ?></label>
    </div>
    <div class="inputs five columns omega">
        <?php echo $this->formCheckbox('openseadragontei_override_items_show', null,
        array('checked' => (bool) get_option('openseadragontei_override_items_show'))); ?>
    </div>
</div>

<div class="field">
    <div>
      If you choose not to override the public theme's items show view the plugin adds a dropdown to public navigation in the form
      of <i>[top level nav name]-->[item type]</i> where <i>[item type]</i> is the item type(s) that have the viewer enabled. These link to
      the custom browse view that the plugin creates. If left blank the default
      menu name is <i>viewer</i>.
    </div>
    <br>
    <div id="openseadragontei_custom_nav_name" class="two columns alpha">
        <label for="openseadragontei_custom_nav_name"><?php echo __("top level nav name"); ?></label>
    </div>
    <div class="inputs five columns omega">
        <?php echo $this->formText('openseadragontei_custom_nav_name', get_option('openseadragontei_custom_nav_name')); ?>
    </div>
</div>
