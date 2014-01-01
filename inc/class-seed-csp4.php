<?php
/**
 * Plugin class logic goes here
 */
class SEED_CSP4{

    /**
     * Instance of this class.
     *
     * @since    1.0.0
     *
     * @var      object
     */
    protected static $instance = null;

	private $comingsoon_rendered = false;

	function __construct(){

			extract(seed_csp4_get_settings());

            // Actions & Filters
            if(((!empty($status) && $status === '1') || (!empty($status) && $status === '2')) || (isset($_GET['cs_preview']) && $_GET['cs_preview'] == 'true')){
            	if(function_exists('bp_is_active')){
                    add_action( 'template_redirect', array(&$this,'render_comingsoon_page'),9);
                }else{
                    add_action( 'template_redirect', array(&$this,'render_comingsoon_page'));
                }
                add_action( 'admin_bar_menu',array( &$this, 'admin_bar_menu' ), 1000 );
            }

            // Add scripts
            add_action( 'admin_enqueue_scripts', array(&$this,'add_scripts') );            
    }

    /**
     * Return an instance of this class.
     *
     * @since     1.0.0
     *
     * @return    object    A single instance of this class.
     */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Get pages and put in assoc array
     */
    function get_pages(){
        $pages = get_pages();
        $page_arr = array();
        if(is_array($pages)){
            foreach($pages as $k=>$v){
                $page_arr[$v->ID] = $v->post_title;
            }
        }
        return $page_arr;
    }

    /**
     * Display admin bar when active
     */
    function admin_bar_menu($str){
        global $wp_admin_bar;
        extract(seed_csp4_get_settings());
        $msg = '';
        if($status == '1'){
        	$msg = __('Coming Soon Mode Active','coming-soon');
        }elseif($status == '2'){
        	$msg = __('Maintenance Mode Active','coming-soon');
        }
    	//Add the main siteadmin menu item 
        $wp_admin_bar->add_menu( array(
            'id'     => 'seed-csp4-notice',
            'href' => admin_url().'options-general.php?page=seed_csp4',
            'parent' => 'top-secondary',
            'title'  => $msg,
            'meta'   => array( 'class' => 'csp4-mode-active' ),
        ) );
    }

    /**
     * Display the default template
     */
    function get_default_template(){
        $file = file_get_contents(SEED_CSP4_PLUGIN_PATH.'/themes/default/index.php');
        return $file;
    }

	/**
     * Load scripts
     */
    function add_scripts($hook) {
        wp_enqueue_style( 'seed-csp4-adminbar-notification', SEED_CSP4_PLUGIN_URL.'inc/adminbar-style.css', false, SEED_CSP4_VERSION, 'screen');
    }

    /**
     * Display the coming soon page
     */
    function render_comingsoon_page() {

    	extract(seed_csp4_get_settings());


        if(empty($_GET['cs_preview'])){
            $_GET['cs_preview'] = false;
        }

        // Check if Preview
        $is_preview = false;
        if ((isset($_GET['cs_preview']) && $_GET['cs_preview'] == 'true')) {
            $is_preview = true;
        }

        // Exit if a custom login page
        if(preg_match("/login/i",$_SERVER['REQUEST_URI']) > 0 && $is_preview == false){
            return false;
        }

        if(preg_match("/account/i",$_SERVER['REQUEST_URI']) > 0 && $is_preview == false){
            return false;
        }

        //Exit if wysija double opt-in
        if(preg_match("/wysija/i",$_SERVER['REQUEST_URI']) > 0 && $is_preview == false){
            return false;
        }



        // Finally check if we should show the coming soon page.
        $this->comingsoon_rendered = true;
        
        // set headers
        if($status == '2'){
            header('HTTP/1.1 503 Service Temporarily Unavailable');
            header('Status: 503 Service Temporarily Unavailable');
            header('Retry-After: 86400'); // retry in a day
            $csp4_maintenance_file = WP_CONTENT_DIR."/maintenance.php";
            if(!empty($enable_maintenance_php) and file_exists($csp4_maintenance_file)){
                include_once( $csp4_maintenance_file );
                exit();
            }
        }
        
        // render template tags
        
        $template = $this->get_default_template();
        require_once( SEED_CSP4_PLUGIN_PATH.'/themes/default/functions.php' );
        $template_tags = array(
            '{Title}' => seed_csp4_title(),
            '{MetaDescription}' => seed_csp4_metadescription(),
            '{Privacy}' => seed_csp4_privacy(),
            '{Favicon}' => seed_csp4_favicon(),
            '{CustomCSS}' => seed_csp4_customcss(),
            '{Head}' => seed_csp4_head(),
            '{Footer}' => seed_csp4_footer(),
            '{Logo}' => seed_csp4_logo(),
            '{Headline}' => seed_csp4_headline(),
            '{Description}' => seed_csp4_description(),
            '{Credit}' => seed_csp4_credit(),
            );
		echo strtr($template, $template_tags);
        exit();
        
    }

    function get_font_family(){
        return false;
    }


}


function seed_csp4_get_settings(){
    $s1 = get_option('seed_csp4_settings_1');
    $s2 = get_option('seed_csp4_settings_2');
    $s3 = get_option('seed_csp4_settings_4');
    if(empty($s1))
        $s1 = array();

    if(empty($s2))
        $s2 = array();

    if(empty($s3))
        $s3 = array();

    $options = $s1 + $s2 + $s3;
    return $options;
}


