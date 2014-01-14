<?php
// Template Tags
function seed_csp4_title() {
	$o = seed_csp4_get_settings();
	extract( $o );

	$output = '';

	if ( !empty( $seo_title ) ) {
		$output = esc_html( $seo_title );
	}
	return $output;
}

function seed_csp4_metadescription() {
	$o = seed_csp4_get_settings();
	extract( $o );

	$output = '';

	if ( !empty( $seo_description ) ) {
		$output = '<meta name="description" content="'.esc_attr( $seo_description ).'">';
	}

	return $output;
}

function seed_csp4_privacy() {
	$output = '';

	if ( get_option( 'blog_public' ) == 0 ) {
		$output = "<meta name='robots' content='noindex,nofollow' />";
	}

	return $output;
}

function seed_csp4_favicon() {
	$o = seed_csp4_get_settings();
	extract( $o );

	$output = '';

	if ( !empty( $favicon ) ) {
		$output .= "<!-- Favicon -->\n";
		$output .= '<link href="'.esc_attr( $favicon ).'" rel="shortcut icon" type="image/x-icon" />';
	}

	return $output;
}

function seed_csp4_customcss() {
	$o = seed_csp4_get_settings();
	extract( $o );

	$output = '';

	if ( !empty( $custom_css ) ) {
		$output = '<style type="text/css">'.$custom_css.'</style>';
	}

	return $output;
}

function seed_csp4_head() {
	$o = seed_csp4_get_settings();
	extract( $o );

	// CSS
	$output = '';

	$output .= "<!-- Bootstrap and default Style -->\n";
	$output .= '<link rel="stylesheet" href="'.SEED_CSP4_PLUGIN_URL.'themes/default/bootstrap/css/bootstrap.min.css">'."\n";
	if ( !empty( $enable_responsiveness ) ) {
		$output .= '<link rel="stylesheet" href="'.SEED_CSP4_PLUGIN_URL.'themes/default/bootstrap/css/bootstrap-responsive.min.css">'."\n";
	}
	$output .= '<link rel="stylesheet" href="'.SEED_CSP4_PLUGIN_URL.'themes/default/style.css">'."\n";
	if ( is_rtl() ) {
		$output .= '<link rel="stylesheet" href="'.SEED_CSP4_PLUGIN_URL.'themes/default/rtl.css">'."\n";
	}
	$output .= '<style type="text/css">'."\n";

	// Calculated Styles

	$output .= '/* calculated styles */'."\n";
	ob_start();
	?>

	/* Background Style */
    html{
    	height:100%;
		<?php if ( !empty( $bg_image ) ): ;?>
			<?php if ( isset( $bg_cover ) && in_array( '1', $bg_cover ) ) : ?>
				background: <?php echo $bg_color;?> url('<?php echo $bg_image; ?>') no-repeat top center fixed;
				-webkit-background-size: cover;
				-moz-background-size: cover;
				-o-background-size: cover;
				background-size: cover;
			<?php else: ?>
				background: <?php echo $bg_color;?> url('<?php echo $bg_image; ?>') <?php echo $bg_repeat;?> <?php echo $bg_position;?> <?php echo $bg_attahcment;?>;
			<?php endif ?>
        <?php else: ?>
        	background: <?php echo $bg_color;?>;
		<?php endif; ?>
    }
    body{
    	height:100%;
			<?php if ( !empty( $bg_effect ) ) : ?>
				background: transparent url('<?php echo plugins_url( 'images/bg-'.$bg_effect.'.png', __FILE__ ) ; ?>') repeat;
			<?php else: ?>
				background: transparent;
			<?php endif; ?>
	}

    /* Text Styles */
    <?php if ( !empty( $text_font ) ):?>
	    body{
	        font-family: <?php echo SEED_CSP4::get_font_family($text_font); ?>
	    }
    <?php endif;?>

    <?php if ( !empty( $text_color ) ) { ?>
		body{
			color:<?php echo $text_color;?>;
		}
    <?php } ?>

    <?php if ( empty( $headline_color ) ) { $headline_color = $link_color; }?>


    <?php if ( !empty( $headline_color ) ) { ?>
		h1, h2, h3, h4, h5, h6{
			color:<?php echo $headline_color;?>;
		}
    <?php }?>


    <?php if ( !empty( $link_color ) ) { ?>
		a, a:visited, a:hover, a:active{
			color:<?php echo $link_color;?>;
		}


    <?php }

	$output .= ob_get_clean();

	$output .= '</style>'."\n";



	// Javascript
	$output .= "<!-- JS -->\n";
	$include_url = includes_url();
	$last = $include_url[strlen( $include_url )-1];
	if ( $last != '/' ) {
		$include_url = $include_url . '/';
	}
	if ( empty( $enable_wp_head_footer ) ) {
		$output .= '<script src="'.$include_url.'js/jquery/jquery.js"></script>'."\n";
	}
	$output .= '<script src="'.SEED_CSP4_PLUGIN_URL.'themes/default/bootstrap/js/bootstrap.js"></script>'."\n";

	if ( !empty( $enable_fitvidjs ) ) {
		$output .= "<!-- FitVid -->\n";
		$output .= '<script src="'.SEED_CSP4_PLUGIN_URL.'themes/default/js/jquery.fitvids.js"></script>'."\n";
	}
	$output .= '<script src="'.SEED_CSP4_PLUGIN_URL.'themes/default/js/script.js"></script>'."\n";


	// Header Scripts
	if ( !empty( $header_scripts ) ) {
		$output .= "<!-- Header Scripts -->\n";
		$output .= $header_scripts;
	}

	// Google Analytics
	if ( !empty( $ga_analytics ) ) {
		$output .= "<!-- Google Analytics -->\n";
		$output .= $ga_analytics;
	}

	// Modernizr
	$output .= "<!-- Modernizr -->\n";
	$output .= '<script src="'.SEED_CSP4_PLUGIN_URL.'themes/default/js/modernizr.min.js"></script>'."\n";

	return $output;
}

function seed_csp4_footer() {
	$o = seed_csp4_get_settings();
	extract( $o );

	$output = '';

	if ( !empty( $footer_scripts ) ) {
		$output .= "<!-- Footer Scripts -->\n";
		$output .= $footer_scripts;
	}

	return $output;
}

function seed_csp4_logo() {
	$o = seed_csp4_get_settings();
	extract( $o );

	$output = '';

	if ( !empty( $logo ) ) {
		$output .= "<img id='csp4-image' src='$logo'>";
	}

	return  $output;
}

function seed_csp4_headline() {
	$o = seed_csp4_get_settings();
	extract( $o );

	$output = '';

	if ( !empty( $headline ) ) {
		$output .= '<h1 id="csp4-headline">'.$headline.'</h1>';
	}

	return  $output;
}

function seed_csp4_description() {
	$o = seed_csp4_get_settings();
	extract( $o );

	$output = '';

	if ( !empty( $description ) ) {
		$output .= '<div id="csp4-description">'.$description.'</div>';
	}

	return  $output;
}

function seed_csp4_credit() {
	$o = seed_csp4_get_settings();
	extract( $o );

	$output = '';

	if ( !empty( $footer_credit_txt ) ) {
		$output = '<div id="csp4-credit">';
		$output .= '<a target="_blank" href="'.esc_url( $footer_credit_link ).'">'.esc_html( $footer_credit_txt ).'</a>';
		$output .= '</div>';
	}

	return  $output;
}