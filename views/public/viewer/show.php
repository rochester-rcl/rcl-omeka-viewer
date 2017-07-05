<?php echo $this->partial('common/header-viewer.php');
echo $this->viewer($this->files, $this->itemTypeId, $this->item);
echo foot();
?>
<script type="text/javascript">
  var navContainer = document.getElementById('primary-nav-viewer');
  var nav = navContainer.getElementsByClassName('navigation');
  var navItems = nav[0].childNodes;
  for (var i=0; i < navItems.length; i++) {
    var element = navItems[i];
    if (element.nodeType !== Node.TEXT_NODE) {
      checkDropdown(element);
    }
  }

  function checkDropdown(element) {
    var dropdown = element.getElementsByTagName('ul');
    if (dropdown.length > 0) {
        var children = dropdown[0].getElementsByTagName('li');
        element.className = "hide";
        element.onclick = function(event) {
          event.preventDefault();
          if (element.className !== 'show') {
            setElementClassNames(children, "fade-in");
            setTimeout(function() {
              element.className =  "show";
              setElementClassNames(children, "fade-in show");
            }, 250);
          } else {
            setElementClassNames(children, "hide");
            setTimeout(function() {
              element.className =  "hide";
              setElementClassNames(children, "fade-out hide");
            }, 250);
          }
        }
    }
    function setElementClassNames(elements, className) {
      for (var i=0; i < elements.length; i++) {
        var element = elements[i];
        element.className = className;
        element.onclick = function(event) {
          var links = element.getElementsByTagName('a');
          window.location = links[0].href;
        }
      }
    }
  }
</script>
