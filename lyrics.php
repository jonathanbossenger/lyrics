<?php
/*
 * Plugin Name: WCCT Vanilla
 * Version: 1.0
 * Plugin URI: http://jonathanbossenger.com/
 * Description: Stop, Collaborate and listen
 * Author: Jonathan Bossenger
 * Author URI: http://jonathanbossenger.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: wcct-vanilla
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Jonathan Bossenger
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function wcct_get_lyrics(){
	$wcct_vanilla_lyrics = get_option('wcct_vanilla_lyrics', '');
	$wcct_vanilla_lyrics = stripslashes($wcct_vanilla_lyrics);
	if (empty($wcct_vanilla_lyrics)){
		$wcct_vanilla_lyrics = "Hello, Dolly
Well, hello, Dolly
It's so nice to have you back where you belong
You're lookin' swell, Dolly
I can tell, Dolly
You're still glowin', you're still crowin'
You're still goin' strong
We feel the room swayin'
While the band's playin'
One of your old favourite songs from way back when
So, take her wrap, fellas
Find her an empty lap, fellas
Dolly'll never go away again
Hello, Dolly
Well, hello, Dolly
It's so nice to have you back where you belong
You're lookin' swell, Dolly
I can tell, Dolly
You're still glowin', you're still crowin'
You're still goin' strong
We feel the room swayin'
While the band's playin'
One of your old favourite songs from way back when
Golly, gee, fellas
Find her a vacant knee, fellas
Dolly'll never go away
Dolly'll never go away
Dolly'll never go away again";
	}
	return $wcct_vanilla_lyrics;

}

function wcct_get_lyric() {
	/** These are the lyrics to Hello Dolly */
	$lyrics = wcct_get_lyrics();

	// Here we split it into lines
	$lyrics = explode( "\n", $lyrics );

	// And then randomly choose a line
	return wptexturize( $lyrics[ mt_rand( 0, count( $lyrics ) - 1 ) ] );
}

function wcct_content_filter( $content ) {
	// lets make sure this is a single post being rendered
	global $post;
	if ( !is_single( $post ) ){
		return $content;
	}
	// get the lyric
	$lyric = wcct_get_lyric();
	$lyric = '<div class="wcct-lyric">'.$lyric.'</div>';

	// append the lyric to the content
	return $lyric . $content;
}
add_filter( 'the_content', 'wcct_content_filter' );

function wcct_load_plugin_scripts() {
	// get the plugin url
	$plugin_url = plugin_dir_url( __FILE__ );
	//enqueue the plugin style sheet
	wp_enqueue_style( 'wcct_style', $plugin_url . 'css/style.css' );
}
add_action( 'wp_enqueue_scripts', 'wcct_load_plugin_scripts' );

function my_plugin_menu() {
	add_options_page(
		'Lyric Settings',
		'Lyrics',
		'manage_options',
		'vanilla-settings',
		'wcct_vanilla_settings'
	);
}
add_action( 'admin_menu', 'my_plugin_menu' );

function wcct_vanilla_settings(){
	$wcct_vanilla_lyrics = get_option('wcct_vanilla_lyrics', '');
	$wcct_vanilla_lyrics = stripslashes($wcct_vanilla_lyrics);
	?>
	<div class="wrap" id="wcct_vanilla_settings">
        <h2>Vanilla Lyrics</h2>

		<form method="post" action="options-general.php?page=vanilla-settings" enctype="multipart/form-data">

			<input type="hidden" name="wcct_vanilla_action" value="wcct_vanilla_settings_update">
			<?php wp_nonce_field('wcct_vanilla_dislay_settings', 'wcct_vanilla_settings'); ?>

			<h2>Lyrics</h2>
			<p>Edit your lyrics.</p>
			<table class="form-table">
				<tbody>

				<tr>
					<th scope="row">Lyrics</th>
					<td>
						<textarea rows="10" cols="100" name="wcct_vanilla_lyrics"><?php echo $wcct_vanilla_lyrics ?></textarea>
						<div>
							<label for="text_field">
								<span class="description">Enter your chosen song lyrics.</span>
							</label>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
			<input type="submit" value="Submit" />
		</form>
	</div>
	<?php
}

function wcct_brand_process_post() {
	if ( isset( $_POST['wcct_vanilla_action'] ) && $_POST['wcct_vanilla_action'] == 'wcct_vanilla_settings_update' && wp_verify_nonce( $_POST['wcct_vanilla_settings'], 'wcct_vanilla_dislay_settings' ) ) {
		if (isset($_POST['wcct_vanilla_lyrics']) && !empty($_POST['wcct_vanilla_lyrics'])){
			update_option( 'wcct_vanilla_lyrics', $_POST['wcct_vanilla_lyrics'] );
		}else {
			delete_option( 'wcct_vanilla_lyrics' );
		}
	}
}
add_action( 'init', 'wcct_brand_process_post' );
