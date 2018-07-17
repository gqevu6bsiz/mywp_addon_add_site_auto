<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if ( ! class_exists( 'MywpAddSiteAutoApi' ) ) :

final class MywpAddSiteAutoApi {

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

  public static function plugin_info() {

    $plugin_info = array(
      'admin_url' => network_admin_url( 'admin.php?page=mywp_add_on_add_site_auto' ),
      'document_url' => 'https://mywpcustomize.com/add_ons/my-wp-add-on-add-site-auto',
      'website_url' => 'https://mywpcustomize.com/',
      'github' => 'https://github.com/gqevu6bsiz/mywp_addon_add_site_auto',
      'github_tags' => 'https://api.github.com/repos/gqevu6bsiz/mywp_addon_add-site-auto/tags',
    );

    $plugin_info = apply_filters( 'mywp_add_site_auto_plugin_info' , $plugin_info );

    return $plugin_info;

  }

}

endif;
