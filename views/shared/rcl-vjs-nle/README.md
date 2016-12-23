# videojs-nle-controls

Adds frame-by-frame scrubbing and SMPTE timecode to a videojs player instance

## Table of Contents

<!-- START doctoc -->
<!-- END doctoc -->
## Installation
Clone this repository and run
```sh
npm install
```
To build an uncompressed, unminified version run
```sh
npm run build:js
```
To build a compressed, minified version run
```sh
npm run build:js:min
```
## Usage

To include videojs-nle-controls on your website or web application, use any of the following methods.

### `<script>` Tag

This is the simplest case. Get the script in whatever way you prefer and include the plugin _after_ you include [video.js][videojs], so that the `videojs` global is available.

```html
<script src="//path/to/video.min.js"></script>
<script src="//path/to/videojs-nle-controls.min.js"></script>
<script>
  var player = videojs('my-video');

  player.nleControls();
</script>
```

### ES6 Imports

When using with ES6 imports, build the plugin and import it

```js
import nleControls  from './dist/videojs-nle-controls';

var player = videojs('my-video');

player.nleControls();
```

## License

MIT. Copyright (c) jjromphf &lt;jromphf@library.rochester.edu&gt;


[videojs]: http://videojs.com/
