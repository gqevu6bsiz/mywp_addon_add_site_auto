<?php
/*
Plugin Name: My WP Add Site Auto
Plugin URI: https://mywpcustomize.com/add_ons/my-wp-add-on-add-site-auto
Description: My WP Add Site Auto is automatically add site from network admin.
Version: 1.0.1
Author: gqevu6bsiz
Author URI: http://gqevu6bsiz.chicappa.jp/
Text Domain: mywp-add-site-auto
Domain Path: /languages
My WP Test working: 1.6
*/

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpAddSiteAuto' ) ) :

final class MywpAddSiteAuto {

  private static $instance;

  private function __construct() {}

  public static function get_instance() {

    if ( !isset( self::$instance ) ) {

      self::$instance = new self();

    }

    return self::$instance;

  }

  private function __clone() {}

  private function __wakeup() {}

  public static function init() {

    self::define_constants();
    self::include_core();

    add_action( 'mywp_start' , array( __CLASS__ , 'mywp_start' ) );

  }

  private static function define_constants() {

    define( 'MYWP_ADD_SITE_AUTO_NAME' , 'My WP Add Site Auto' );
    define( 'MYWP_ADD_SITE_AUTO_VERSION' , '1.0.1' );
    define( 'MYWP_ADD_SITE_AUTO_PLUGIN_FILE' , __FILE__ );
    define( 'MYWP_ADD_SITE_AUTO_PLUGIN_BASENAME' , plugin_basename( MYWP_ADD_SITE_AUTO_PLUGIN_FILE ) );
    define( 'MYWP_ADD_SITE_AUTO_PLUGIN_DIRNAME' , dirname( MYWP_ADD_SITE_AUTO_PLUGIN_BASENAME ) );
    define( 'MYWP_ADD_SITE_AUTO_PLUGIN_PATH' , plugin_dir_path( MYWP_ADD_SITE_AUTO_PLUGIN_FILE ) );
    define( 'MYWP_ADD_SITE_AUTO_PLUGIN_URL' , plugin_dir_url( MYWP_ADD_SITE_AUTO_PLUGIN_FILE ) );

  }

  private static function include_core() {

    $dir = MYWP_ADD_SITE_AUTO_PLUGIN_PATH . 'core/';

    require_once( $dir . 'class.api.php' );

  }

  public static function mywp_start() {

    add_action( 'mywp_plugins_loaded', array( __CLASS__ , 'mywp_plugins_loaded' ) );

    add_action( 'init' , array( __CLASS__ , 'wp_init' ) );

  }

  public static function mywp_plugins_loaded() {

    add_filter( 'mywp_controller_plugins_loaded_include_modules' , array( __CLASS__ , 'mywp_controller_plugins_loaded_include_modules' ) );

    add_filter( 'mywp_setting_plugins_loaded_include_modules' , array( __CLASS__ , 'mywp_setting_plugins_loaded_include_modules' ) );

  }

  public static function wp_init() {

    load_plugin_textdomain( 'mywp-add-site-auto' , false , MYWP_ADD_SITE_AUTO_PLUGIN_DIRNAME . '/languages' );

  }

  public static function mywp_controller_plugins_loaded_include_modules( $includes ) {

    $dir = MYWP_ADD_SITE_AUTO_PLUGIN_PATH . 'controller/modules/';

    $includes['add_site_auto_main_general']   = $dir . 'mywp.controller.module.main.general.php';
    $includes['add_site_auto_updater']        = $dir . 'mywp.controller.module.updater.php';

    return $includes;

  }

  public static function mywp_setting_plugins_loaded_include_modules( $includes ) {

    $dir = MYWP_ADD_SITE_AUTO_PLUGIN_PATH . 'setting/modules/';

    $includes['add_site_auto_setting'] = $dir . 'mywp.setting.add-site-auto.php';

    return $includes;

  }

}

MywpAddSiteAuto::init();

endif;
