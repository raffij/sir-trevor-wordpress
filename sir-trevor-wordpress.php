<?php
/*
Plugin Name: Sir Trevor Wordpress
Version: 0.9
License: MIT
Plugin URI: http://github.com/raffij/sir-trevor-wordpress
Author: Raffi Jacobs
Author URI: http://raffijacobs.com
Description: This code will replace Wordpress editor with Sir Trevor Js
*/

add_action("wp_ajax_sir_trevor_wordpress_twitter_fetch", "sir_trevor_wordpress_twitter_fetch");

add_filter( 'wp_default_editor', 'sir_trevor_wordpress_default_editor' );
add_filter( 'admin_footer', 'sir_trevor_wordpress_admin_footer', 99);

remove_filter( 'before_wp_tiny_mce', 'wp_print_editor_js' );
remove_filter( 'after_wp_tiny_mce', 'wp_preload_dialogs' );

add_filter( 'the_content', 'sir_trevor_wordpress_render', 0);

add_filter( 'init', 'sir_trevor_wordpress_init');

function sir_trevor_wordpress_init() {
	wp_enqueue_script("twitter", "https://platform.twitter.com/widgets.js");
}

function sir_trevor_wordpress_default_editor($content){
	return 'html';
}

function sir_trevor_wordpress_admin_footer(){
	
	echo '
	<link rel="stylesheet" href="'.plugin_dir_url( __FILE__ ).'js/sir-trevor-js/sir-trevor-icons.css">
	<link rel="stylesheet" href="'.plugin_dir_url( __FILE__ ).'js/sir-trevor-js/sir-trevor.css">
	';

	wp_enqueue_script("underscore", plugin_dir_url( __FILE__ ).'js/underscore/underscore-min.js');
	wp_enqueue_script("eventable", plugin_dir_url( __FILE__ ).'js/Eventable/eventable.js');
	wp_enqueue_script("sir-trevor-wordpress", plugin_dir_url( __FILE__ ).'js/sir-trevor-js/sir-trevor.js');
	wp_enqueue_script("sir-trevor-wordpress-init", plugin_dir_url( __FILE__ ).'init.js');

	echo '
	<style type="text/css">
	#wp-content-editor-tools, #ed_toolbar, #wp-word-count, .wp-editor-tools {
		display: none !important;
	}
	</style>';
	
}

function sir_trevor_wordpress_render($content) {
	require_once( plugin_dir_path( __FILE__ )."lib/Markdown.php" );

	$data = json_decode($content, true);
	$blocks = $data['data'];
	$output = "";
	foreach($blocks as $block) {
		$filename = get_stylesheet_directory()."/sir-trevor-wordpress/".$block['type'].".php";
		if (!file_exists($filename)) {
			$filename = plugin_dir_path( __FILE__ )."blocks/".$block['type'].".php";
		}
		if (file_exists($filename)) {
			ob_start();
			$block = $block['data'];
			require $filename;
			$output .= ob_get_clean();
		}
	}
	return $output;
}

function sir_trevor_wordpress_twitter_fetch() {

	$id = $_REQUEST["id"];

	require_once( plugin_dir_path( __FILE__ )."lib/TwitterAPIExchange.php");

	$settings = array(
	    'oauth_access_token' => get_option('sir_trevor_wordpress_twitter_oauth_access_token'),
	    'oauth_access_token_secret' => get_option('sir_trevor_wordpress_twitter_oauth_access_token_secret'),
	    'consumer_key' => get_option('sir_trevor_wordpress_twitter_consumer_key'),
	    'consumer_secret' => get_option('sir_trevor_wordpress_twitter_consumer_secret')
	);

	$url = 'https://api.twitter.com/1.1/statuses/show.json';
	$getfield = '?id='.$id;
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($settings);
	$response = $twitter->setGetfield($getfield)
	             ->buildOauth($url, $requestMethod)
	             ->performRequest();

	print $response;
	exit();
}

// create custom plugin settings menu
add_action('admin_menu', 'sir_trevor_wordpress_create_menu');

function sir_trevor_wordpress_create_menu() {

	//create new top-level menu
	add_submenu_page('plugins.php', 'Sir Trevor Wordpress Settings', 'Sir Trevor Wordpress', 'administrator', __FILE__, 'sir_trevor_wordpress_settings_page');

	//call register settings function
	add_action( 'admin_init', 'register_mysettings' );
}


function register_mysettings() {
	//register our settings
	register_setting( 'sir-trevor-wordpress-twitter-settings-group', 'sir_trevor_wordpress_twitter_oauth_access_token' );
	register_setting( 'sir-trevor-wordpress-twitter-settings-group', 'sir_trevor_wordpress_twitter_oauth_access_token_secret' );
	register_setting( 'sir-trevor-wordpress-twitter-settings-group', 'sir_trevor_wordpress_twitter_consumer_key' );
	register_setting( 'sir-trevor-wordpress-twitter-settings-group', 'sir_trevor_wordpress_twitter_consumer_secret' );
}

function sir_trevor_wordpress_settings_page() {
?>
<div class="wrap">
<h2>Sir Trevor Js</h2>

<form method="post" action="options.php">

    <?php settings_fields( 'sir-trevor-wordpress-twitter-settings-group' ); ?>
    <?php do_settings_sections( 'sir-trevor-wordpress-twitter-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Oauth access token</th>
        <td><input type="text" name="sir_trevor_wordpress_twitter_oauth_access_token" value="<?php echo get_option('sir_trevor_wordpress_twitter_oauth_access_token'); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">oauth_access_token_secret</th>
        <td><input type="text" name="sir_trevor_wordpress_twitter_oauth_access_token_secret" value="<?php echo get_option('sir_trevor_wordpress_twitter_oauth_access_token_secret'); ?>" /></td>
        </tr>
        
        <tr valign="top">
        <th scope="row">consumer_key</th>
        <td><input type="text" name="sir_trevor_wordpress_twitter_consumer_key" value="<?php echo get_option('sir_trevor_wordpress_twitter_consumer_key'); ?>" /></td>
        </tr>

        <tr valign="top">
        <th scope="row">consumer_secret</th>
        <td><input type="text" name="sir_trevor_wordpress_twitter_consumer_secret" value="<?php echo get_option('sir_trevor_wordpress_twitter_consumer_secret'); ?>" /></td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php }
