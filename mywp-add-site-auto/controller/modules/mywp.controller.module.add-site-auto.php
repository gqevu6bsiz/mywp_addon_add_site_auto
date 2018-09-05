<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpControllerAbstractModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpControllerModuleAddSiteAutoMainGeneral' ) ) :

final class MywpControllerModuleAddSiteAuto extends MywpControllerAbstractModule {

  static protected $id = 'add_site_auto';

  protected static function after_init() {

    add_filter( 'mywp_controller_pre_get_model_' . self::$id , array( __CLASS__ , 'mywp_controller_pre_get_model' ) );

  }

  public static function mywp_controller_pre_get_model( $pre_model ) {

    $pre_model = true;

    return $pre_model;

  }

}

MywpControllerModuleAddSiteAuto::init();

endif;
