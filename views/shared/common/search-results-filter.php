<div class="search-result-filter-container">
<?php if ($results_type === 'item'): ?>
  <?php foreach($formatted_results as $itemType => $itemArray): ?>
    <label>
      <input
        class="search-result-filter-checkbox"
        type="checkbox"
        checked
        id="checkbox-<?php echo $itemType; ?>"
        value="<?php echo $itemType; ?>"
      />
      <?php echo de_slugify($itemType); ?>
    </label>
  <?php endforeach; ?>
<?php endif; ?>
<?php if ($results_type === 'record'): ?>
  <?php foreach ($formatted_results as $recordType => $itemTypeArray): ?>
    <?php if ($recordType === 'Item'): ?>
      <?php foreach ($itemTypeArray as $itemType => $itemArray): ?>
        <label>
          <input
            class="search-result-filter-checkbox"
            type="checkbox"
            checked
            id="checkbox-<?php echo $itemType; ?>"
            value="<?php echo $itemType; ?>"
          />
          <?php echo de_slugify($itemType); ?>
        </label>
      <?php endforeach; ?>
    <?php else: ?>
      <label>
        <input
          class="search-result-filter-checkbox"
          type="checkbox"
          checked
          id="checkbox-<?php echo $recordType; ?>"
          value="<?php echo $recordType; ?>"
        />
        <?php echo de_slugify($recordType); ?>
      </label>
    <?php endif; ?>
  <?php endforeach; ?>
<?php endif; ?>
</div>
<script type="text/javascript">
  function initFilters() {
    var checkboxes = document.getElementsByClassName('search-result-filter-checkbox');
    for (var i=0; i < checkboxes.length; i++) {
      var checkbox = checkboxes[i];
      addClickListeners(checkbox);
    }
  }

  function addClickListeners(checkbox) {
    checkbox.addEventListener('click', function(){
      var targetRecordTypeClassName = checkbox.value;
      filterResultType(targetRecordTypeClassName);
    }, false);
  }

  function filterResultType(recordClassName) {
    var elements = document.getElementsByClassName(recordClassName);
    for (var i=0; i < elements.length; i++) {
      var element = elements[i];
      if (element) {
        if (element.classList.contains('show')) {
          element.classList.remove('show');
          element.classList.add('hide');
        } else {
          element.classList.remove('hide');
          element.classList.add('show');
        }
      }
    }
  }

  initFilters();

</script>
