(function(f){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}g.videojsFramerate = f()}})(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function (global){
'use strict';

exports.__esModule = true;

var _video = (typeof window !== "undefined" ? window['videojs'] : typeof global !== "undefined" ? global['videojs'] : null);

var _video2 = _interopRequireDefault(_video);

function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

// Default options for the plugin.
var defaults = {};

/**
 * Function to invoke when the player is ready.
 *
 * This is a great place for your plugin to initialize itself. When this
 * function is called, the player will have its DOM and child components
 * in place.
 *
 * @function onPlayerReady
 * @param    {Player} player
 * @param    {Object} [options={}]
 */
var onPlayerReady = function onPlayerReady(player, options) {
  player.addClass('vjs-framerate');
  var framerates = [24.0, 18.0, 12.0, 6.0];
  var origFramerate = options.origFramerate ? options.origFramerate : 24.0;
  initControls(player, origFramerate, framerates);
};

/**
 * Function to initialize framerate buttons and bind them.
 *
 *
 * @function initControls
 * @param    {Player} player
 *@param {framerates} array
 */
var initControls = function initControls(player, origFramerate, framerates) {
  var vjsButtonClass = _video2.default.getComponent('Button');
  framerates.map(function (framerate, index) {
    var name = framerate.toString().split('.')[0] + 'fps';
    var extendedButtonClass = _video2.default.extend(vjsButtonClass, {
      constructor: function constructor() {
        vjsButtonClass.call(this, player);
      },
      handleClick: function handleClick() {
        player.playbackRate(framerate / origFramerate);
        console.log(player.playbackRate);
      }
    });
    var extendedButtonInstance = player.controlBar.addChild(new extendedButtonClass());
    extendedButtonInstance.addClass("vjs-" + name);
    var extendedButtonInstanceEl = extendedButtonInstance.el();
    extendedButtonInstanceEl.innerHTML = '<span class="vjs-control-text">' + name + '</span>' + name;
  });
};

/**
 * A video.js plugin.
 *
 * In the plugin function, the value of `this` is a video.js `Player`
 * instance. You cannot rely on the player being in a "ready" state here,
 * depending on how the plugin is invoked. This may or may not be important
 * to you; if not, remove the wait for "ready"!
 *
 * @function framerate
 * @param    {Object} [options={}]
 *           An object of options left to the plugin author to define.
 */
var framerate = function framerate(options) {
  var _this = this;

  this.ready(function () {
    onPlayerReady(_this, _video2.default.mergeOptions(defaults, options));
  });
};

// Register the plugin with video.js.
_video2.default.plugin('framerate', framerate);

// Include the version number.
framerate.VERSION = '0.0.0';

exports.default = framerate;

}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})

},{}]},{},[1])(1)
});
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJzcmMvcGx1Z2luLmpzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBOzs7Ozs7QUNBQTs7Ozs7O0FBRUE7QUFDQSxJQUFNLFdBQVcsRUFBakI7O0FBRUE7Ozs7Ozs7Ozs7O0FBV0EsSUFBTSxnQkFBZ0IsU0FBaEIsYUFBZ0IsQ0FBQyxNQUFELEVBQVMsT0FBVCxFQUFxQjtBQUN6QyxTQUFPLFFBQVAsQ0FBZ0IsZUFBaEI7QUFDQSxNQUFNLGFBQWEsQ0FBQyxJQUFELEVBQU8sSUFBUCxFQUFhLElBQWIsRUFBbUIsR0FBbkIsQ0FBbkI7QUFDQSxNQUFNLGdCQUFnQixRQUFRLGFBQVIsR0FBd0IsUUFBUSxhQUFoQyxHQUFnRCxJQUF0RTtBQUNBLGVBQWEsTUFBYixFQUFxQixhQUFyQixFQUFvQyxVQUFwQztBQUNELENBTEQ7O0FBT0E7Ozs7Ozs7O0FBUUMsSUFBTSxlQUFlLFNBQWYsWUFBZSxDQUFDLE1BQUQsRUFBUyxhQUFULEVBQXdCLFVBQXhCLEVBQXVDO0FBQzFELE1BQU0saUJBQWlCLGdCQUFRLFlBQVIsQ0FBcUIsUUFBckIsQ0FBdkI7QUFDQSxhQUFXLEdBQVgsQ0FBZSxVQUFDLFNBQUQsRUFBWSxLQUFaLEVBQXNCO0FBQ25DLFFBQUksT0FBTyxVQUFVLFFBQVYsR0FBcUIsS0FBckIsQ0FBMkIsR0FBM0IsRUFBZ0MsQ0FBaEMsSUFBcUMsS0FBaEQ7QUFDQSxRQUFJLHNCQUFzQixnQkFBUSxNQUFSLENBQWUsY0FBZixFQUErQjtBQUN2RCxtQkFBYSx1QkFBVztBQUN0Qix1QkFBZSxJQUFmLENBQW9CLElBQXBCLEVBQTBCLE1BQTFCO0FBQ0QsT0FIc0Q7QUFJdkQsbUJBQWEsdUJBQVc7QUFDdEIsZUFBTyxZQUFQLENBQW9CLFlBQVksYUFBaEM7QUFDQSxnQkFBUSxHQUFSLENBQVksT0FBTyxZQUFuQjtBQUNEO0FBUHNELEtBQS9CLENBQTFCO0FBU0EsUUFBSSx5QkFBeUIsT0FBTyxVQUFQLENBQWtCLFFBQWxCLENBQTJCLElBQUksbUJBQUosRUFBM0IsQ0FBN0I7QUFDQSwyQkFBdUIsUUFBdkIsQ0FBZ0MsU0FBUyxJQUF6QztBQUNBLFFBQUksMkJBQTJCLHVCQUF1QixFQUF2QixFQUEvQjtBQUNBLDZCQUF5QixTQUF6QixHQUFxQyxvQ0FBb0MsSUFBcEMsR0FBMkMsU0FBM0MsR0FBdUQsSUFBNUY7QUFDRCxHQWZEO0FBZ0JELENBbEJEOztBQXFCRDs7Ozs7Ozs7Ozs7O0FBWUEsSUFBTSxZQUFZLFNBQVosU0FBWSxDQUFTLE9BQVQsRUFBa0I7QUFBQTs7QUFDbEMsT0FBSyxLQUFMLENBQVcsWUFBTTtBQUNmLHlCQUFvQixnQkFBUSxZQUFSLENBQXFCLFFBQXJCLEVBQStCLE9BQS9CLENBQXBCO0FBQ0QsR0FGRDtBQUdELENBSkQ7O0FBTUE7QUFDQSxnQkFBUSxNQUFSLENBQWUsV0FBZixFQUE0QixTQUE1Qjs7QUFFQTtBQUNBLFVBQVUsT0FBVixHQUFvQixhQUFwQjs7a0JBRWUsUyIsImZpbGUiOiJnZW5lcmF0ZWQuanMiLCJzb3VyY2VSb290IjoiIiwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uIGUodCxuLHIpe2Z1bmN0aW9uIHMobyx1KXtpZighbltvXSl7aWYoIXRbb10pe3ZhciBhPXR5cGVvZiByZXF1aXJlPT1cImZ1bmN0aW9uXCImJnJlcXVpcmU7aWYoIXUmJmEpcmV0dXJuIGEobywhMCk7aWYoaSlyZXR1cm4gaShvLCEwKTt2YXIgZj1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK28rXCInXCIpO3Rocm93IGYuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixmfXZhciBsPW5bb109e2V4cG9ydHM6e319O3Rbb11bMF0uY2FsbChsLmV4cG9ydHMsZnVuY3Rpb24oZSl7dmFyIG49dFtvXVsxXVtlXTtyZXR1cm4gcyhuP246ZSl9LGwsbC5leHBvcnRzLGUsdCxuLHIpfXJldHVybiBuW29dLmV4cG9ydHN9dmFyIGk9dHlwZW9mIHJlcXVpcmU9PVwiZnVuY3Rpb25cIiYmcmVxdWlyZTtmb3IodmFyIG89MDtvPHIubGVuZ3RoO28rKylzKHJbb10pO3JldHVybiBzfSkiLCJpbXBvcnQgdmlkZW9qcyBmcm9tICd2aWRlby5qcyc7XG5cbi8vIERlZmF1bHQgb3B0aW9ucyBmb3IgdGhlIHBsdWdpbi5cbmNvbnN0IGRlZmF1bHRzID0ge307XG5cbi8qKlxuICogRnVuY3Rpb24gdG8gaW52b2tlIHdoZW4gdGhlIHBsYXllciBpcyByZWFkeS5cbiAqXG4gKiBUaGlzIGlzIGEgZ3JlYXQgcGxhY2UgZm9yIHlvdXIgcGx1Z2luIHRvIGluaXRpYWxpemUgaXRzZWxmLiBXaGVuIHRoaXNcbiAqIGZ1bmN0aW9uIGlzIGNhbGxlZCwgdGhlIHBsYXllciB3aWxsIGhhdmUgaXRzIERPTSBhbmQgY2hpbGQgY29tcG9uZW50c1xuICogaW4gcGxhY2UuXG4gKlxuICogQGZ1bmN0aW9uIG9uUGxheWVyUmVhZHlcbiAqIEBwYXJhbSAgICB7UGxheWVyfSBwbGF5ZXJcbiAqIEBwYXJhbSAgICB7T2JqZWN0fSBbb3B0aW9ucz17fV1cbiAqL1xuY29uc3Qgb25QbGF5ZXJSZWFkeSA9IChwbGF5ZXIsIG9wdGlvbnMpID0+IHtcbiAgcGxheWVyLmFkZENsYXNzKCd2anMtZnJhbWVyYXRlJyk7XG4gIGNvbnN0IGZyYW1lcmF0ZXMgPSBbMjQuMCwgMTguMCwgMTIuMCwgNi4wXTtcbiAgY29uc3Qgb3JpZ0ZyYW1lcmF0ZSA9IG9wdGlvbnMub3JpZ0ZyYW1lcmF0ZSA/IG9wdGlvbnMub3JpZ0ZyYW1lcmF0ZSA6IDI0LjA7XG4gIGluaXRDb250cm9scyhwbGF5ZXIsIG9yaWdGcmFtZXJhdGUsIGZyYW1lcmF0ZXMpO1xufTtcblxuLyoqXG4gKiBGdW5jdGlvbiB0byBpbml0aWFsaXplIGZyYW1lcmF0ZSBidXR0b25zIGFuZCBiaW5kIHRoZW0uXG4gKlxuICpcbiAqIEBmdW5jdGlvbiBpbml0Q29udHJvbHNcbiAqIEBwYXJhbSAgICB7UGxheWVyfSBwbGF5ZXJcbiAqQHBhcmFtIHtmcmFtZXJhdGVzfSBhcnJheVxuICovXG4gY29uc3QgaW5pdENvbnRyb2xzID0gKHBsYXllciwgb3JpZ0ZyYW1lcmF0ZSwgZnJhbWVyYXRlcykgPT4ge1xuICAgY29uc3QgdmpzQnV0dG9uQ2xhc3MgPSB2aWRlb2pzLmdldENvbXBvbmVudCgnQnV0dG9uJyk7XG4gICBmcmFtZXJhdGVzLm1hcCgoZnJhbWVyYXRlLCBpbmRleCkgPT4ge1xuICAgICBsZXQgbmFtZSA9IGZyYW1lcmF0ZS50b1N0cmluZygpLnNwbGl0KCcuJylbMF0gKyAnZnBzJztcbiAgICAgbGV0IGV4dGVuZGVkQnV0dG9uQ2xhc3MgPSB2aWRlb2pzLmV4dGVuZCh2anNCdXR0b25DbGFzcywge1xuICAgICAgIGNvbnN0cnVjdG9yOiBmdW5jdGlvbigpIHtcbiAgICAgICAgIHZqc0J1dHRvbkNsYXNzLmNhbGwodGhpcywgcGxheWVyKTtcbiAgICAgICB9LFxuICAgICAgIGhhbmRsZUNsaWNrOiBmdW5jdGlvbigpIHtcbiAgICAgICAgIHBsYXllci5wbGF5YmFja1JhdGUoZnJhbWVyYXRlIC8gb3JpZ0ZyYW1lcmF0ZSk7XG4gICAgICAgICBjb25zb2xlLmxvZyhwbGF5ZXIucGxheWJhY2tSYXRlKTtcbiAgICAgICB9XG4gICAgIH0pO1xuICAgICBsZXQgZXh0ZW5kZWRCdXR0b25JbnN0YW5jZSA9IHBsYXllci5jb250cm9sQmFyLmFkZENoaWxkKG5ldyBleHRlbmRlZEJ1dHRvbkNsYXNzKCkpO1xuICAgICBleHRlbmRlZEJ1dHRvbkluc3RhbmNlLmFkZENsYXNzKFwidmpzLVwiICsgbmFtZSk7XG4gICAgIGxldCBleHRlbmRlZEJ1dHRvbkluc3RhbmNlRWwgPSBleHRlbmRlZEJ1dHRvbkluc3RhbmNlLmVsKCk7XG4gICAgIGV4dGVuZGVkQnV0dG9uSW5zdGFuY2VFbC5pbm5lckhUTUwgPSAnPHNwYW4gY2xhc3M9XCJ2anMtY29udHJvbC10ZXh0XCI+JyArIG5hbWUgKyAnPC9zcGFuPicgKyBuYW1lO1xuICAgfSk7XG4gfVxuXG5cbi8qKlxuICogQSB2aWRlby5qcyBwbHVnaW4uXG4gKlxuICogSW4gdGhlIHBsdWdpbiBmdW5jdGlvbiwgdGhlIHZhbHVlIG9mIGB0aGlzYCBpcyBhIHZpZGVvLmpzIGBQbGF5ZXJgXG4gKiBpbnN0YW5jZS4gWW91IGNhbm5vdCByZWx5IG9uIHRoZSBwbGF5ZXIgYmVpbmcgaW4gYSBcInJlYWR5XCIgc3RhdGUgaGVyZSxcbiAqIGRlcGVuZGluZyBvbiBob3cgdGhlIHBsdWdpbiBpcyBpbnZva2VkLiBUaGlzIG1heSBvciBtYXkgbm90IGJlIGltcG9ydGFudFxuICogdG8geW91OyBpZiBub3QsIHJlbW92ZSB0aGUgd2FpdCBmb3IgXCJyZWFkeVwiIVxuICpcbiAqIEBmdW5jdGlvbiBmcmFtZXJhdGVcbiAqIEBwYXJhbSAgICB7T2JqZWN0fSBbb3B0aW9ucz17fV1cbiAqICAgICAgICAgICBBbiBvYmplY3Qgb2Ygb3B0aW9ucyBsZWZ0IHRvIHRoZSBwbHVnaW4gYXV0aG9yIHRvIGRlZmluZS5cbiAqL1xuY29uc3QgZnJhbWVyYXRlID0gZnVuY3Rpb24ob3B0aW9ucykge1xuICB0aGlzLnJlYWR5KCgpID0+IHtcbiAgICBvblBsYXllclJlYWR5KHRoaXMsIHZpZGVvanMubWVyZ2VPcHRpb25zKGRlZmF1bHRzLCBvcHRpb25zKSk7XG4gIH0pO1xufTtcblxuLy8gUmVnaXN0ZXIgdGhlIHBsdWdpbiB3aXRoIHZpZGVvLmpzLlxudmlkZW9qcy5wbHVnaW4oJ2ZyYW1lcmF0ZScsIGZyYW1lcmF0ZSk7XG5cbi8vIEluY2x1ZGUgdGhlIHZlcnNpb24gbnVtYmVyLlxuZnJhbWVyYXRlLlZFUlNJT04gPSAnX19WRVJTSU9OX18nO1xuXG5leHBvcnQgZGVmYXVsdCBmcmFtZXJhdGU7XG4iXX0=
