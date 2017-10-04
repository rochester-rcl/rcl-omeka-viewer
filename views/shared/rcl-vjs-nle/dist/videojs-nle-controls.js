/**
 * videojs-nle-controls
 * @version 0.0.0
 * @copyright 2016 jjromphf <jromphf@library.rochester.edu>
 * @license MIT
 */
(function(f){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}g.videojsNleControls = f()}})(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
(function (global){
'use strict';

Object.defineProperty(exports, "__esModule", {
  value: true
});

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
 * @param    {Object} options
 */


// Video JS
var onPlayerReady = function onPlayerReady(player, options) {
  player.addClass('vjs-nle-controls');
  var framerate = options.framerate ? options.framerate : 24.0;
  var duration = options.duration ? function () {
    return options.duration;
  } : function () {
    return player.duration();
  };
  if (options.frameControls) {
    initControls(player, framerate, duration);
  }
  if (options.smpteTimecode) {
    initSMPTE(player, framerate, duration);
  }
};

/**
 * Function to handle non-linear editor style keyboard events
 *
 *
 *@function initControls
 *@param     {Player} player
 *@param     {number} framerate
 */
var initControls = function initControls(player, framerate, duration) {
  var frame = parseFloat((1 / framerate).toFixed(2));
  var keyDown = function keyDown(event) {
    var keyName = event.keyCode;
    switch (keyName) {
      case 37:
        frameReverse(player, frame);
        break;
      case 39:
        frameForward(player, frame, duration);
        break;
    }
  };
  player.on('keydown', keyDown);
};

/**
 * Function to scrub frame by frame in reverse
 *
 *@function frameReverse
 *@param {Player} player
 *@param {number} frame
 */
var frameReverse = function frameReverse(player, frame) {
  var currentTime = player.currentTime();
  if (currentTime > 0) {
    var decrement = currentTime - frame;
    player.currentTime(decrement);
  }
};

/**
* Function to scrub frame by frame in reverse
*
*@function frameForward
*@param {Player} player
*@param {number} frame
*/
var frameForward = function frameForward(player, frame, duration) {
  var currentTime = player.currentTime();
  if (currentTime < duration()) {
    var increment = Math.min(duration(), currentTime + frame);
    player.currentTime(increment);
  }
};

/**
* Function to convert milliseconds to SMPTE (HH:MM:SS:FF) timecode
*
*@function toSMPTE
*@param {number} time
*@param {number} framerate
*/
var toSMPTE = function toSMPTE(currentTime, framerate) {
  var currentFrame = parseInt(currentTime * framerate);
  var hours = Math.floor(currentTime / 3600);
  var minutes = Math.floor(currentTime / 60);
  var seconds = parseInt(currentTime - hours * 3600 - minutes * 60);
  var frames = parseInt(currentFrame % framerate);

  var timecodeArray = [hours, minutes, seconds, frames];
  var processedTimecodeArray = [];

  timecodeArray.forEach(function (time) {
    if (time < 10) {
      var timeString = "0" + time;
      processedTimecodeArray.push(timeString);
    } else {
      var _timeString = time.toString();
      processedTimecodeArray.push(_timeString);
    }
  });
  return processedTimecodeArray.join(':');
};

/**
* Function to display current time (seconds) to milliseconds
*
*@function toMS
*@param {number} currentTimeInSeconds
*/
var toMS = function toMS(currentTimeInSeconds) {
  return Math.ceil(currentTimeInSeconds * 1000);
};

/**
* Function to display current time as SMPTE (HH:MM:SS:FF) timecode
*
*@function initSMPTE
*@param {Player} player
*/

var initSMPTE = function initSMPTE(player, framerate, duration) {
  var setCurrentTimeDisplay = function setCurrentTimeDisplay() {
    var currentTimeDisplay = player.controlBar.progressControl.seekBar.playProgressBar.el();
    var currentTime = player.currentTime();
    currentTimeDisplay.dataset.currentTime = toSMPTE(currentTime, framerate);
  };
  var setRemainingTimeDisplay = function setRemainingTimeDisplay() {
    var currentTime = player.currentTime();
    var remainingTimeDisplay = player.controlBar.remainingTimeDisplay.el();
    remainingTimeDisplay.innerHTML = '<div class="vjs-remaining-time-display" aria-live="off"><span class="vjs-control-text">Remaining Time</span>' + toSMPTE(currentTime, framerate) + ' / ' + toSMPTE(duration(), framerate) + '</div>';
  };
  player.on('timeupdate', setCurrentTimeDisplay);
  player.on('timeupdate', setRemainingTimeDisplay);
};

/**
 * A video.js plugin.
 *
 * In the plugin function, the value of `this` is a video.js `Player`
 * instance. You cannot rely on the player being in a "ready" state here,
 * depending on how the plugin is invoked. This may or may not be important
 * to you; if not, remove the wait for "ready"!
 *
 * @function nleControls
 * @param    {Object} [options={}]
 *           An object of options left to the plugin author to define.
 */
var nleControls = function nleControls(options) {
  var _this = this;

  this.ready(function () {
    onPlayerReady(_this, _video2.default.mergeOptions(defaults, options));
  });
};

// Register the plugin with video.js.
_video2.default.plugin('nleControls', nleControls);

// Include the version number.
nleControls.VERSION = '__VERSION__';

exports.default = nleControls;
}).call(this,typeof global !== "undefined" ? global : typeof self !== "undefined" ? self : typeof window !== "undefined" ? window : {})
},{}]},{},[1])(1)
});