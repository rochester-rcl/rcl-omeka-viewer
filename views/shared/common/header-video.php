<!DOCTYPE html>
<html class="<?php echo get_theme_option('Style Sheet'); ?>" lang="<?php echo get_html_lang(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=yes" />
    <?php if ($description = option('description')): ?>
    <meta name="description" content="<?php echo $description; ?>" />
    <?php endif; ?>

    <?php
    if (isset($title)) {
        $titleParts[] = strip_formatting($title);
    }
    $titleParts[] = option('site_title');
    ?>
    <title><?php echo implode(' &middot; ', $titleParts); ?></title>

    <?php echo auto_discovery_link_tags(); ?>

    <?php fire_plugin_hook('public_head',array('view'=>$this)); ?>
    <!-- Stylesheets -->
    <?php
    queue_css_file('openseadragon', 'screen', false, 'openseadragon');
    queue_css_file(array('iconfonts', 'skeleton','style'));
    queue_css_file('video-js');
    queue_css_file('player-custom');
    echo head_css();
    ?>
    <?php
      queue_js_file('Saxonce.nocache', 'Saxon-CE/Saxonce');
      queue_js_file('video.min', 'videojs');
      queue_js_file('videojs-nle-controls.min', 'rcl-vjs-nle/dist');
      queue_js_file('videojs-framerate', 'rcl-vjs-framerate');
      queue_js_file('openseadragon.min', 'openseadragon');
      queue_js_file('openseadragon_tei', 'openseadragon');
      queue_js_url("https://use.fontawesome.com/aadd731529.js");
      queue_js_file('lazyload.min', 'lazyload');
    ?>
    <?php queue_js_file('vendor/selectivizr', 'javascripts', array('conditional' => '(gte IE 6)&(lte IE 8)')); ?>
    <?php queue_js_file('vendor/respond'); ?>
    <?php queue_js_file('vendor/jquery-accessibleMegaMenu'); ?>
    <?php queue_js_file('globals'); ?>
    <?php echo head_js(); ?>
</head>
<body>
  <div>
