<?php
 
/*
Plugin Name: Youtube WebTv
Plugin URI: https://www.felicemarra.com/wordpress/
Description: Create a syncronized WebTv using youtube's videos. No streaming server required.
Author: Felice Marra
Version: 0.0.1
Author URI: https://www.felicemarra.com/wordpress/
License: GPLv2 or later
*/

if (!defined('ABSPATH')) die("Direct access not allowed");

// Get and set channel information
if (isset($_GET["channel"]))
	{
	$yt_webtv_channel = $_GET["channel"];
	}
  else
	{
	$yt_webtv_channel = 1;
	}

if (isset($_POST["data"]))
	{
	$yt_webtv_nome = $_POST["yt_webtv_nome"];
	$yt_webtv_cognome = $_POST["yt_webtv_cognome"];

	// $channels = get_option("yt_webtv_channels");

	$channel = json_decode(utf8_encode($_POST["data"]) , true);
	$channels = $_POST["data"];
	print_r($channels );
	update_option("yt_webtv_channel_" . $_POST["id"], $channels);
	exit;
	}
  else
	{
	$channel = get_option("yt_webtv_channel_" . $yt_webtv_channel);
	}

// Load scripts to the page
function yt_webtv_load_my_scripts()
	{
	wp_deregister_script('jquery');
	wp_register_script('jquery', '//code.jquery.com/jquery-1.11.0.min.js');
	wp_enqueue_script('jquery');
	if (is_admin())
		{
		wp_enqueue_style('jquery-ui');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-tabs');
		}
	  else
		{
		wp_register_script('ytWebTv_api', 'https://apis.google.com/js/client.js?onload=googleApiClientReady');
		wp_enqueue_script('ytWebTv_api');
		wp_register_script('ytWebTv_player', plugins_url('youtube-webtv/ytWebTv_player.js'));
		wp_enqueue_script('ytWebTv_player');
		}

	wp_register_style('yt_webtv_css', plugins_url('youtube-webtv/ytWebTv_style.css'));
	wp_enqueue_style('yt_webtv_css');
	}
add_action('init', 'yt_webtv_load_my_scripts');
add_action('wp_enqueue_scripts', 'yt_webtv_load_my_scripts');

// Add settings link into the plugins page
function yt_webtv_settings_link($actions, $file)
	{
	$actions['settings'] = '<a href="' . admin_url() . 'options-general.php?page=yt_webtv_options">Settings</a>';
	return $actions;
	}
add_filter('plugin_action_links_'.plugin_basename(__FILE__), 'yt_webtv_settings_link', 2, 2);


// Add link to the setting page on the left side bar
function yt_webtv_menu()
	{
	add_options_page('Youtube WebTv', 'Youtube WebTv', 'manage_options', 'yt_webtv_options', 'yt_webtv_options_page');
	add_menu_page('Youtube WebTv', 'Youtube WebTv', 'manage_options', 'yt_webtv_options', 'yt_webtv_options_page');
	}
add_action('admin_menu', 'yt_webtv_menu');

function yt_webtv_options_page()
	{
	require_once (dirname(__FILE__) . '/ytWebTv_admin.php');
	}

// Load stored channel's informations
function load_channel($id)
	{
	return "ytWebTv_playlist = JSON.parse('" . str_replace("'", "\'", get_option("yt_webtv_channel_" . $id)) . "');";
	}

// Add shortcode capability
function yt_webtv_shortcode($atts)
	{
	extract(shortcode_atts(array(
		'id' => ''
	) , $atts));
	if ($id == "")
		{
		$id = 1;
		}

	$HTML = '<div id="ytWebTv_player"></div><script>var ytWebTv_channel=' . $id . ';' . load_channel($id) . '</script>';
	return $HTML;
	}
add_shortcode('ytWebTv', 'yt_webtv_shortcode');

// Register options
function register_and_build_fields()
	{
	$channels = array();
	if (!get_option('yt_webtv_channel_' . $GLOBALS["yt_webtv_channel"]))
		{
		add_option('yt_webtv_channel_tot', $GLOBALS["yt_webtv_channel"]);
		}

	add_option('yt_webtv_channel_' . $GLOBALS["yt_webtv_channel"], $channels);
	}
add_action('admin_init', 'register_and_build_fields');
?>


