<?php
// Template Tags
function seed_csp4_title(){
	global $seed_csp4_settings;
	$o = $seed_csp4_settings;
	extract($o);

	$title = '';
	if(!empty($seo_title)){
		$title = esc_html($seo_title);
	} else {
		$blogname = get_bloginfo( 'name', 'display' );
	  	$title = $blogname;
	}
	return $title;
}


function seed_csp4_metadescription(){
	global $seed_csp4_settings;
	$o = $seed_csp4_settings;
	extract($o);
	if(empty($seo_description)){
		$seo_description = get_bloginfo( 'description', 'display' );
	}

	$meta_description = '<meta name="description" content="'.esc_attr($seo_description).'">';

	return $meta_description;
}

function seed_csp4_privacy(){
	$output = '';
	if(get_option('blog_public') == 0){
		$output = "<meta name='robots' content='noindex,nofollow' />";
	}
	return $output;
}

function seed_csp4_favicon(){
	global $seed_csp4_settings;
	$o = $seed_csp4_settings;
	extract($o);
	$output = '';
	if(!empty($favicon)){
		$output .= "<!-- Favicon -->\n";
		$output .= '<link href="'.esc_attr($favicon).'" rel="shortcut icon" type="image/x-icon" />';
	}
	return $output;
}

function seed_csp4_customcss(){
	global $seed_csp4_settings;
	$o = $seed_csp4_settings;
	extract($o);
	$output = '';
	if(!empty($custom_css)){
		$output = '<style type="text/css">'.$custom_css.'</style>';
	}
	return $output;
}

function seed_csp4_head(){
	require_once(SEED_CSP4_PLUGIN_PATH.'libraries/seed_csp4_lessc.inc.php');
	global $seed_csp4_settings;
	$o = $seed_csp4_settings;
	extract($o);

	$output = "";

	$output .= "<!-- Bootstrap and default Style -->\n";
	$output .= '<link rel="stylesheet" href="'.SEED_CSP4_PLUGIN_URL.'templates/default/bootstrap/css/bootstrap.min.css">'."\n";
	if(!empty($enable_responsiveness)){
	$output .= '<link rel="stylesheet" href="'.SEED_CSP4_PLUGIN_URL.'templates/default/bootstrap/css/bootstrap-responsive.min.css">'."\n";
	}
	$output .= '<link rel="stylesheet" href="'.SEED_CSP4_PLUGIN_URL.'templates/default/style.css">'."\n";
	if(is_rtl()){
		$output .= '<link rel="stylesheet" href="'.SEED_CSP4_PLUGIN_URL.'templates/default/rtl.css">'."\n";
	}
	$output .= '<style type="text/css">'."\n";
	$output .= '/* calculated styles */'."\n";
	ob_start();


	?>

	/* Background Style */
    html{
    	height:100%;
		<?php if(!empty($bg_image)): ;?>
			<?php if(isset($bg_cover) && in_array('1', $bg_cover)) : ?>
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
			<?php if(!empty($bg_effect) ) : ?>
				background: transparent url('<?php echo plugins_url('images/bg-'.$bg_effect.'.png',__FILE__) ; ?>') repeat;
			<?php else: ?>
				background: transparent;
			<?php endif; ?>
	}

    /* Text Styles */
    <?php if(!empty($text_font)):?>
	    body{
	        font-family: <?php //$seed_csp4->get_font_family($text_font); ?> 
	    }
    <?php endif;?>

    <?php if($headline_font == 'inherit'){$headline_font = $text_font;}?>

    <?php if(!empty($headline_font)):?>
	    h1, h2, h3, h4, h5, h6{
	        font-family: <?php //$seed_csp4->get_font_family($headline_font); ?>;
	        <?php if($headline_font[0] != "_") { if($headline_font != 'inherit'){ ?>
	        font-weight:normal;
	    	<?php }} ?>
	    }
    <?php endif;?>

    <?php if($button_font == 'inherit'){$button_font = $headline_font;}?>
    <?php if(!empty($button_font)):?>
	    button{
	        font-family: <?php //$seed_csp4->get_font_family($button_font); ?>
	    }
    <?php endif;?>

    <?php if(!empty($text_color)){ ?>
		body{
			color:<?php echo $text_color;?>;
		}
    <?php } ?>

    <?php if(empty($headline_color)){ $headline_color = $link_color; }?>


    <?php if(!empty($headline_color)){ ?>
		h1, h2, h3, h4, h5, h6{
			color:<?php echo $headline_color;?>;
		}
    <?php }?>


    <?php if(!empty($link_color)){ ?>
		a, a:visited, a:hover, a:active{
			color:<?php echo $link_color;?>;
		}
		
		<?php

		$css = "
				.buttonBackground(@startColor, @endColor) {
		  // gradientBar will set the background to a pleasing blend of these, to support IE<=9
		  .gradientBar(@startColor, @endColor);
		  *background-color: @endColor; /* Darken IE7 buttons by default so they stand out more given they won't have borders */
		  .reset-filter();

		  // in these cases the gradient won't cover the background, so we override
		  &:hover, &:active, &.active, &.disabled, &[disabled] {
		    background-color: @endColor;
		    *background-color: darken(@endColor, 5%);
		  }

		  // IE 7 + 8 can't handle box-shadow to show active, so we darken a bit ourselves
		  &:active,
		  &.active {
		    background-color: darken(@endColor, 10%) e(\"\9\");
		  }
		}

		.reset-filter() {
		  filter: e(%(\"progid:DXImageTransform.Microsoft.gradient(enabled = false)\"));
		}

		.gradientBar(@primaryColor, @secondaryColor) {
		  #gradient > .vertical(@primaryColor, @secondaryColor);
		  border-color: @secondaryColor @secondaryColor darken(@secondaryColor, 15%);
		  border-color: rgba(0,0,0,.1) rgba(0,0,0,.1) fadein(rgba(0,0,0,.1), 15%);
		}

		#gradient {
			.vertical(@startColor: #555, @endColor: #333) {
		    background-color: mix(@startColor, @endColor, 60%);
		    background-image: -moz-linear-gradient(top, @startColor, @endColor); // FF 3.6+
		    background-image: -ms-linear-gradient(top, @startColor, @endColor); // IE10
		    background-image: -webkit-gradient(linear, 0 0, 0 100%, from(@startColor), to(@endColor)); // Safari 4+, Chrome 2+
		    background-image: -webkit-linear-gradient(top, @startColor, @endColor); // Safari 5.1+, Chrome 10+
		    background-image: -o-linear-gradient(top, @startColor, @endColor); // Opera 11.10
		    background-image: linear-gradient(top, @startColor, @endColor); // The standard
		    background-repeat: repeat-x;
		    filter: e(%(\"progid:DXImageTransform.Microsoft.gradient(startColorstr='%d', endColorstr='%d', GradientType=0)\",@startColor,@endColor)); // IE9 and down
		  }
		}
		.lightordark (@c) when (lightness(@c) >= 65%) {
			color: black;
			text-shadow: 0 -1px 0 rgba(256, 256, 256, 0.3);
		}
		.lightordark (@c) when (lightness(@c) < 65%) {
			color: white;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.3);
		}
		@btnColor: {$link_color};
		@btnDarkColor: darken(@btnColor, 15%);
		.btn, .gform_button {
		  .lightordark (@btnColor);
		  .buttonBackground(@btnColor, @btnDarkColor);
		}

		#csp4-progressbar span,.countdown_section{
			.lightordark (@btnColor);
		}

		.btn:hover{
		  .lightordark (@btnColor);
		}

		input[type='text']{
			border-color: @btnDarkColor @btnDarkColor darken(@btnDarkColor, 15%);
		}

		@hue: hue(@btnDarkColor);
		@saturation: saturation(@btnDarkColor);
		@lightness: lightness(@btnDarkColor);
		input[type='text']:focus {
			border-color: hsla(@hue, @saturation, @lightness, 0.8);
			webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 8px hsla(@hue, @saturation, @lightness, 0.6);
			-moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 8px hsla(@hue, @saturation, @lightness, 0.6);
			box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075),0 0 8px hsla(@hue, @saturation, @lightness, 0.6);
		    
		}

		";

		$less = new seed_csp4_lessc();
		$style = $less->parse($css);
		echo $style;

		?>
    <?php } 


   

 
	$dump = ob_get_contents();
	ob_end_clean();
	$output .= $dump;

	$output .= '</style>'."\n";

	if(!empty($typekit_id)){
		$output .= "<!-- Typekit -->\n";
		$output .= '<script type="text/javascript" src="//use.typekit.com/'.$typekit_id.'.js"></script>';
		$output .= '<script type="text/javascript">try{Typekit.load();}catch(e){}</script>';

	}


	$output .= "<!-- JS -->\n";
	$include_url = includes_url();
	$last = $include_url[strlen($include_url)-1]; 
	if($last != '/'){
		$include_url = $include_url . '/';
	}
	if(empty($enable_wp_head_footer)){
	$output .= '<script src="'.$include_url.'js/jquery/jquery.js"></script>'."\n";
	}
	$output .= '<script src="'.SEED_CSP4_PLUGIN_URL.'templates/default/bootstrap/js/bootstrap.js"></script>'."\n";

	if(!empty($enable_fitvidjs)){		
		$output .= "<!-- FitVid -->\n";
		$output .= '<script src="'.SEED_CSP4_PLUGIN_URL.'templates/default/js/jquery.fitvids.js"></script>'."\n";
	}
	$output .= '<script src="'.SEED_CSP4_PLUGIN_URL.'templates/default/js/script.js"></script>'."\n";



	if(!empty($header_scripts)){
		$output .= "<!-- Header Scripts -->\n";
		$output .= $header_scripts;
	}

	if(!empty($ga_analytics)){
		$output .= "<!-- Google Analytics -->\n";

		$ga_code = "<script type='text/javascript'>

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', '{$ga_analytics}']);
		  _gaq.push(['_trackPageview']);

		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>";
		$output .= $ga_code;
	}



  	$output .= "<!-- Modernizr -->\n";
	$output .= '<script src="'.SEED_CSP4_PLUGIN_URL.'templates/default/js/modernizr.min.js"></script>'."\n";


	return $output;
}

function seed_csp4_footer(){
	global $seed_csp4,$seed_csp4_post_result;
	$is_post = false;
	if(!empty($seed_csp4_post_result['post']) && $seed_csp4_post_result['post'] == 'true' && $seed_csp4_post_result['status'] == '200'){
		$is_post = true;
	}
	$o = $seed_csp4_settings;
	extract($o);

	$output = '';

	$output .= "<!-- Belatedpng -->\n";
	$output .= "<!--[if lt IE 7 ]>\n";
	$output .= '<script src="'.SEED_CSP4_PLUGIN_URL.'templates/default/js/dd_belatedpng.js"></script>'."\n";
	$output .= "<script>DD_belatedPNG.fix('img, .png_bg');</script>\n";
	$output .= "<![endif]-->\n";

	$rf_url = "";
	$rf_stats = "";
	if(isset($enable_reflink) && !empty($seed_csp4_post_result['referrer_id'])){
		$rf_url = "<br><br>".__('Your Referral URL is:','coming-soon').'<br>'.home_url().'?ref='.$seed_csp4_post_result['referrer_id'].'';
		$rf_stats = '<br><br>'.__('Your Referral Stats','coming-soon').'<br>Clicks: '.$seed_csp4_post_result['clicks'].'<br>Subscribers: '.$seed_csp4_post_result['subscribers'];
	}


	if(!empty($seed_csp4_post_result['post'])){
		$output .= "<script>after_form('".json_encode($seed_csp4_post_result)."');</script>\n";
	}


	$output .= "<script>\n";
	if(is_array($container_effect['effects']) && in_array('5',$container_effect['effects'])){
	$output .= 'jQuery(document).ready(function($){$("#csp4-content").fadeIn("slow").css("display","inline-block");';
	}else{
	$output .= 'jQuery(document).ready(function($){$("#csp4-content").show().css("display","inline-block");';	
	}
	if(!empty($enable_fitvidjs)){
		$output .= '$("#csp4-description").fitVids();';
	}

	$fronend = parse_url($_SERVER['HTTP_HOST']);
	$backend = parse_url(admin_url());
	//if(empty($enable_ajax) || $backend['host'] != $fronend['host']){
		$output .= '$("#csp4-form").off();';
	//}
	$output .= "});</script>\n";


	if(!empty($footer_scripts)){
		$output .= "<!-- Footer Scripts -->\n";
		$output .= $footer_scripts;
	}

	return $output;

}

function seed_csp4_logo(){
	global $seed_csp4_settings;
	$o = $seed_csp4_settings;
	extract($o);
	$output = "";
	if(!empty($logo)){
		$output .= "<img id='csp4-image' src='$logo' />";
	}
	return  $output;
}

function seed_csp4_headline(){
	global $seed_csp4_settings,$post;

	// get settings
	$o = $seed_csp4_settings;
	extract($o);
	$output = "";
	$dump = "";
	if(!empty($headline)){
		if(!empty($post->post_title)){
			$post->post_title = $headline;
			ob_start();
				the_title();
			$dump = ob_get_contents();
			ob_end_clean();
		}else{
			$dump = $headline;
		}
		$output .= '<h1 id="csp4-headline">'.$dump.'</h1>';
	}
	return  $output;
}

function seed_csp4_description(){
	global $seed_csp4,$post;
	$o = $seed_csp4_settings;

	$csp4_description = $o['description'];

	$output = '';
	$dump = '';
	if(!empty($csp4_description)){

		ob_start();
			$content = $csp4_description;
			if(!empty($enable_wp_head_footer)){
				$content = apply_filters('the_content', $content);
				echo $content;
			}else{
				if(isset($GLOBALS['wp_embed']))
					$content = $GLOBALS['wp_embed']->autoembed($content);
				echo do_shortcode(shortcode_unautop(wpautop(convert_chars(wptexturize($content)))));
			}
		$dump = ob_get_contents();
		ob_end_clean();
		$output .= '<div id="csp4-description">'.$dump.'</div>';
	}
	return  $output;
}

	


function seed_csp4_credit(){
	global $seed_csp4_settings;
	$o = $seed_csp4_settings;
	extract($o);
	$output = "";
	
	if(!empty($footer_credit_img)){
	$output = '<div id="csp4-credit">';
	$output .= '<a target="_blank" href="'.esc_url($footer_credit_link).'"><img src="'.esc_url($footer_credit_img).'" /></a>';
	$output .= '</div>';
	}elseif(!empty($footer_credit_txt)){
	$output = '<div id="csp4-credit">';
	$output .= '<a target="_blank" href="'.esc_url($footer_credit_link).'">'.esc_html($footer_credit_txt).'</a>';
	$output .= '</div>';
	}

	// if(!empty($privacy_policy)){
	// 	if(preg_match('/iubenda/',$privacy_policy)){
	// 		$output .= $privacy_policy;
	// 	}else{
	// 		$output .= '<a href="">'.__('Privacy Policy','coming-soon').'</a>'.$privacy_policy;
	// 	}
	// }

	return  $output;
}
