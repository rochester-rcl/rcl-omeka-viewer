<!DOCTYPE html>
<html lang="<?php echo get_html_lang(); ?>">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if ( $description = option('description')): ?>
        <meta name="description" content="<?php echo $description; ?>" />
    <?php endif; ?>

    <!-- Will build the page <title> -->
    <?php
        if (isset($title)) { $titleParts[] = strip_formatting($title); }
        $titleParts[] = option('site_title');
    ?>
    <title><?php echo implode(' &middot; ', $titleParts); ?></title>
    <?php echo auto_discovery_link_tags(); ?>

    <!-- Will fire plugins that need to include their own files in <head> -->
    <?php fire_plugin_hook('public_head', array('view'=>$this)); ?>


    <!-- Need to add custom and third-party CSS files? Include them here -->
    <?php
		    $bootswatch_theme=get_theme_option('Style Sheet');
        queue_css_file($bootswatch_theme.'/bootstrap.min');
        queue_css_file('openseadragon', 'screen', false, 'openseadragon');
        queue_css_file('video-js');
        queue_css_file('player-custom');
        queue_css_file('style');
        echo head_css();
    ?>

    <!-- Need more JavaScript files? Include them here -->
    <?php
        queue_js_file('lib/bootstrap.min');
        queue_js_file('globals');
        queue_js_file('Saxonce.nocache', 'Saxonce');
        queue_js_file('video.min', 'videojs');
        queue_js_file('videojs-nle-controls.min', 'rcl-vjs-nle/dist');
        queue_js_file('videojs-framerate', 'rcl-vjs-framerate');
        queue_js_file('openseadragon.min', 'openseadragon');
        queue_js_file('openseadragon_tei', 'openseadragon');
        queue_js_url("https://use.fontawesome.com/aadd731529.js");
        queue_js_file('lazyload.min', 'lazyload');
        echo head_js();
    ?>
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>



<?php

echo body_tag(array('id' => @$bodyid, 'class' => @$bodyclass)); ?>
    <?php fire_plugin_hook('public_body', array('view'=>$this)); ?>
    <nav class="navbar navbar-default navbar-fixed-top"><!-- navbar-fixed-top -->
      <div class="container">
        <div class="navbar-header">
          <?php echo bs_link_logo_to_navbar(); ?>
          <button class="navbar-toggle" type="button" data-toggle="collapse" data-target="#navbar-main">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="navbar-collapse collapse" id="navbar-main">
          <?php echo public_nav_main_bootstrap(); ?>
          <?php

// Commenting out the default search behavior for now, per the group's desire to have this search all of the RBSCP site - jss, 20171108
		  //echo search_form(array('show_advanced' => false, 'form_attributes'=>array('id'=>'navbar-search', 'class'=>'navbar-form navbar-right'))); ?>


 <!-- Here is the RBSCP header menu items, as is from the site -->
          <!-- Menu items -->
        <nav class="main-navigation-wrapper">
            <div class="region region-main-navigation">
    			<div id="block-search-form" class="block block-search">
  					<div class="content main-navigation">
    					<form class="gss search-form form-search content-search" action="https://rbscpdev.lib.rochester.edu/search/gss" method="post" id="search-block-form" accept-charset="UTF-8">
                    	<div>
           					<div>
   								<div class="searchform-alter input-group">
    								<input placeholder="Search RBSCP" type="text" id="edit-keys" name="keys" value="" size="40" maxlength="255" class="form-text form-control">
    								<span class="input-group-btn">
    									<button type="submit" class="btn btn-default"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
    								</span>
   								 </div>
   							</div>
						</div>
                        </form>
                 	</div>
				</div>
                <div id="block-menu-menu-rbscp-menu" class="block block-menu">
      				<div class="content main-navigation">
    					<ul class="menu">
                        	<li class="first leaf"><a href="https://www.library.rochester.edu/my-accounts" title="">My Accounts</a></li>
							<li class="leaf"><a href="http://www.library.rochester.edu/chat" title="">Chat</a></li>
							<li class="leaf"><a href="http://rbscp.lib.rochester.edu/files/forms/contact_redirect.php" title="">Contact RBSCP</a></li>
							<li class="leaf"><a href="http://rcld7.lib.rochester.edu/giving/" title="">Giving</a></li>
							<li class="last leaf"><a href="/blog">Blog</a></li>
						</ul>
                	</div>
				</div>
        	</div>
     	</nav>

 <!-- End content from RBSCP header -->

        </div>
      </div>
    </nav>
    <?php if ((get_theme_option('display_header') !== '0')): ?>
    <header id="banner" class="<?php echo get_theme_option('header_flow'); ?> page-header" style="background-size:cover;background-image:url('<?php
		if ((get_theme_option('Header Background Image') === null)){
			echo img('defaulthbg.jpg');
		}
		else echo bs_header_bg();
		?>');">

<!-- Added container div here, per Sean, for alignment - jss, 20171019 -->
        <div class="container">
		<div class="row header-row">
			<?php if ((get_theme_option('header_logo_image') !== null)): ?>
			<div class="col-md-4" id="header-logo-holder">
				 <?php
				 // added href to link the header logo back to the exhibit home, per SM based on meeting notes for 10/18 ... however, a closing </a> tag isn't needed because the bs_header_logo() function apparently tacks this on
				 echo "<a href='/exhibits/show/womens-rights-movement'>" . bs_header_logo();  ?>
			</div>
			<?php endif; ?>
			<div class="col-md-8" id="header-claim-holder">
				<div class="well">
				<?php if ((get_theme_option('header_image_heading') !== '')): ?>
					<h1><?php echo get_theme_option('header_image_heading'); ?></h1>
				<?php endif; ?>
				<?php if ((get_theme_option('header_image_text') !== '')): ?>
					<p><?php echo get_theme_option('header_image_text'); ?></p>
				<?php endif; ?>
				</div>
			</div>
		</div>
        </div>
    </header>
    <?php endif; ?>
    <div id="viewer-fullpage-container">
