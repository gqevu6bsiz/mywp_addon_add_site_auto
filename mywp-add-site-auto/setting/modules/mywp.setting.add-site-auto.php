<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

if( ! class_exists( 'MywpAbstractSettingModule' ) ) {
  return false;
}

if ( ! class_exists( 'MywpSettingScreenAddSiteAuto' ) ) :

final class MywpSettingScreenAddSiteAuto extends MywpAbstractSettingModule {

  static protected $id = 'add_site_auto';

  static protected $priority = 50;

  static private $menu = 'add_on_add_site_auto';

  public static function mywp_setting_menus( $setting_menus ) {

    if( is_multisite() ) {

      $setting_menus[ self::$menu ] = array(
        'menu_title' => __( 'Add Site Auto' , 'mywp-add-site-auto' ),
        'multiple_screens' => false,
        'network' => true,
      );

    }

    return $setting_menus;

  }

  public static function mywp_setting_screens( $setting_screens ) {

    if( is_multisite() ) {

      $setting_screens[ self::$id ] = array(
        'title' => __( 'Add Site Auto' , 'mywp-add-site-auto' ),
        'menu' => self::$menu,
        'controller' => 'add_site_auto',
        'use_advance' => false,
        'use_form' => false,
      );

    }

    return $setting_screens;

  }

  public static function mywp_ajax() {

    if( ! MywpAddSiteAutoApi::is_network_manager() ) {

      return false;

    }

    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( self::$id , 'check_latest' ) , array( __CLASS__ , 'check_latest' ) );
    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( self::$id , 'validate_add_site_auto' ) , array( __CLASS__ , 'validate_add_site_auto' ) );
    add_action( 'wp_ajax_' . MywpSetting::get_ajax_action_name( self::$id , 'add_site' ) , array( __CLASS__ , 'add_site' ) );

  }

  public static function check_latest() {

    $action_name = MywpSetting::get_ajax_action_name( self::$id , 'check_latest' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    if( ! MywpAddSiteAutoApi::is_network_manager() ) {

      return false;

    }

    delete_site_transient( 'mywp_add_site_auto_updater' );
    delete_site_transient( 'mywp_add_site_auto_updater_remote' );

    $is_latest = MywpControllerModuleAddSiteAutoUpdater::is_latest();

    if( is_wp_error( $is_latest ) ) {

      wp_send_json_error( array( 'error' => $is_latest->get_error_message() ) );

    }

    if( ! $is_latest ) {

      wp_send_json_success( array( 'is_latest' => 0 ) );

    } else {

      wp_send_json_success( array( 'is_latest' => 1 , 'message' => sprintf( '<p>%s</p>' , '<span class="dashicons dashicons-yes"></span> ' . __( 'Using a latest version.' , 'mywp-add-site-auto' ) ) ) );

    }

  }

  public static function validate_add_site_auto() {

    $action_name = MywpSetting::get_ajax_action_name( self::$id , 'validate_add_site_auto' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    if( ! MywpAddSiteAutoApi::is_network_manager() ) {

      return false;

    }

    $error = new WP_Error();

    if( empty( $_POST['blog_url'] ) ) {

      $error->add( 'empty_blog_url' , __( 'Please enter a valid blog url.' , 'mywp-add-site-auto' ) );

    }

    $blog_url = strtolower( strip_tags( $_POST['blog_url'] ) );

    if( empty( $_POST['blog_title'] ) ) {

      $error->add( 'empty_blog_title' , __( 'Please enter a valid blog title.' , 'mywp-add-site-auto' ) );

    }

    $blog_title = strip_tags( $_POST['blog_title'] );

    if( empty( $_POST['admin_email'] ) ) {

      $error->add( 'empty_admin_email' , __( 'Please enter a valid admin email.' , 'mywp-add-site-auto' ) );

    }

    $admin_email = sanitize_email( strip_tags( $_POST['admin_email'] ) );

    if( ! is_email( $admin_email ) ) {

      $error->add( 'empty_admin_email' , __( 'Please enter a valid admin email.' , 'mywp-add-site-auto' ) );

    }

    if( empty( $_POST['sites_num'] ) ) {

      $error->add( 'empty_sites_num' , __( 'Please enter a valid sites num.' , 'mywp-add-site-auto' ) );

    }

    $sites_num = intval( $_POST['sites_num'] );

    if( empty( $sites_num ) ) {

      $error->add( 'empty_sites_num' , __( 'Please enter a valid sites num.' , 'mywp-add-site-auto' ) );

    }

    if( $error->get_error_message() ) {

      wp_send_json_error( array( 'error' => $error->get_error_message() ) );

    }

    $data = array(
      'blog_url' => $blog_url,
      'blog_title' => $blog_title,
      'admin_email' => $admin_email,
      'sites_num' => $sites_num,
    );

    wp_send_json_success( $data );

  }

  public static function add_site() {

    global $wpdb;

    $action_name = MywpSetting::get_ajax_action_name( self::$id , 'add_site' );

    if( empty( $_POST[ $action_name ] ) ) {

      return false;

    }

    check_ajax_referer( $action_name , $action_name );

    if( ! MywpAddSiteAutoApi::is_network_manager() ) {

      return false;

    }

    $error = new WP_Error();

    if( empty( $_POST['blog_url'] ) ) {

      $error->add( 'empty_blog_url' , __( 'Please enter a valid blog url.' , 'mywp-add-site-auto' ) );

    }

    $blog_url = strtolower( strip_tags( $_POST['blog_url'] ) );

    if( empty( $_POST['blog_title'] ) ) {

      $error->add( 'empty_blog_title' , __( 'Please enter a valid blog title.' , 'mywp-add-site-auto' ) );

    }

    $blog_title = strip_tags( $_POST['blog_title'] );

    if( empty( $_POST['admin_email'] ) ) {

      $error->add( 'empty_admin_email' , __( 'Please enter a valid admin email.' , 'mywp-add-site-auto' ) );

    }

    $admin_email = sanitize_email( strip_tags( $_POST['admin_email'] ) );

    if( ! is_email( $admin_email ) ) {

      $error->add( 'empty_admin_email' , __( 'Please enter a valid admin email.' , 'mywp-add-site-auto' ) );

    }

    if( $error->get_error_message() ) {

      wp_send_json_error( array( 'error' => $error->get_error_message() ) );

    }

    $blogs_results = $wpdb->get_results( "SHOW TABLE STATUS LIKE '{$wpdb->blogs}'" );

    if( empty( $blogs_results[0]->Auto_increment ) ) {

      $error->add( 'empty_auto_increment' , __( 'An unexpected error occurred.' , 'mywp-add-site-auto' ) );

      wp_send_json_error( array( 'error' => $error->get_error_message() ) );

    }

    $next_blog_id = $blogs_results[0]->Auto_increment;

    $meta = array(
      'public' => 1,
      'WPLANG' => get_site_option( 'WPLANG' ),
    );

    if( strpos( $blog_url , '###next_blog_id###' ) !== false ) {

      $blog_url = str_replace( '###next_blog_id###' , $next_blog_id , $blog_url );

    }

    if( strpos( $blog_title , '###next_blog_id###' ) !== false ) {

      $blog_title = str_replace( '###next_blog_id###' , $next_blog_id , $blog_title );

    }

    $network = get_network();

    if ( is_subdomain_install() ) {

      $newdomain = $blog_url . '.' . preg_replace( '|^www\.|' , '' , $network->domain );
      $path = $network->path;

    } else {

      $newdomain = $network->domain;
      $path = $network->path . $blog_url . '/';

    }

    $user_id = email_exists( $admin_email );

    if( ! $user_id ) {

      $password = wp_generate_password( 12 , false );

      $user_id = wpmu_create_user( $blog_url , $password , $admin_email );

      if( ! $user_id ) {

        $error->add( 'invalid_create_user' , __( 'There was an error creating the user.' ) );

        wp_send_json_error( array( 'error' => $error->get_error_message() ) );

      }

    }

    remove_action( 'wpmu_new_blog' , 'newblog_notify_siteadmin' , 10 );

    $created_blog_id = wpmu_create_blog( $newdomain , $path , $blog_title , $user_id , $meta , get_current_network_id() );

    if( is_wp_error( $created_blog_id ) ) {

      $error->add( 'error_create_blog' , $created_blog_id->get_error_message() );

      wp_send_json_error( array( 'error' => $error->get_error_message() ) );

    }

    $new_site_title = $blog_title;

    $new_site_url = self::get_scheme() . $newdomain . $path;

    $data = array(
      'new_site_url' => $new_site_url,
      'new_site_title' => $new_site_title,
      'created_blog_id' => $created_blog_id,
    );

    wp_send_json_success( $data );

  }

  private static function get_scheme() {

    $scheme = '';

    if( ! empty( $_SERVER['REQUEST_SCHEME'] ) ) {

      $scheme = strip_tags( $_SERVER['REQUEST_SCHEME'] ) . '://';

    }

    return $scheme;

  }

  public static function mywp_current_setting_screen_content() {

    $network = get_network();

    $scheme = self::get_scheme();

    ?>
    <h3 class="mywp-setting-screen-subtitle"><?php _e( 'Add Site Auto' , 'mywp-add-site-auto' ); ?></h3>
    <div id="add-site-auto-fields">
      <table class="form-table">
        <tbody>
          <tr>
            <th>
              <?php _e( 'Site Address (URL)' ); ?>
            </th>
            <td>
              <?php if( is_subdomain_install() ) : ?>

                <?php echo $scheme; ?>
                <input type="text" class="regular-text blog_url" id="blog_url" placeholder="<?php echo esc_attr( 'site###next_blog_id###' ); ?>" value="<?php echo esc_attr( 'site###next_blog_id###' ); ?>" /> . <?php echo $network->domain; ?>

              <?php else : ?>

                <?php echo $scheme; ?><?php echo $network->domain; ?><?php echo $network->path; ?>
                <input type="text" class="regular-text blog_url" id="blog_url" placeholder="<?php echo esc_attr( 'site###next_blog_id###' ); ?>" value="<?php echo esc_attr( 'site###next_blog_id###' ); ?>" />

              <?php endif; ?>
              <br />
              <code>###next_blog_id###</code>
            </td>
          </tr>
          <tr>
            <th>
              <?php _e( 'Site Title' ); ?>
            </th>
            <td>
              <input type="text" class="large-text blog_title" id="blog_title" placeholder="<?php echo esc_attr( 'Site ###next_blog_id###' ); ?>" value="<?php echo esc_attr( 'Site ###next_blog_id###' ); ?>" />
              <br />
              <code>###next_blog_id###</code>
            </td>
          </tr>
          <tr>
            <th>
              <?php _e( 'Admin Email' ); ?>
            </th>
            <td>
              <input type="text" class="regular-text admin_email" id="admin_email" placeholder="<?php echo esc_attr( get_site_option( 'admin_email' ) ); ?>" value="<?php echo esc_attr( get_site_option( 'admin_email' ) ); ?>" />
            </td>
          </tr>
          <tr>
            <th>
              <?php _e( 'Add site(s) num' , 'mywp-add-site-auto' ); ?>
            </th>
            <td>
              <input type="number" class="small-text sites_num" id="sites_num" placeholder="<?php echo esc_attr( 100 ); ?>" value="<?php echo esc_attr( 100 ); ?>" />
            </td>
          </tr>
        </tbody>
      </table>
      <p class="submit">
        <input type="button" class="button button-primary" value="<?php echo esc_attr( __( 'Add Sites' , 'mywp-add-site-auto' ) ); ?>" id="do-add-site-auto" />
        <span class="spinner"></span>
      </p>
    </div>

    <p>&nbsp;</p>

    <div id="results-site-auto">

      <ol>
      </ol>

    </div>
    <?php

  }

  public static function mywp_current_setting_screen_after_footer() {

    $is_latest = MywpControllerModuleAddSiteAutoUpdater::is_latest();

    $have_latest = false;

    if( ! is_wp_error( $is_latest ) && ! $is_latest ) {

      $have_latest = MywpControllerModuleAddSiteAutoUpdater::get_latest();

    }

    $plugin_info = MywpAddSiteAutoApi::plugin_info();

    $class_have_latest = '';

    if( $have_latest ) {

      $class_have_latest = 'have-latest';

    }

    ?>
    <p>&nbsp;</p>
    <h3><?php _e( 'Plugin info' , 'mywp-add-site-auto' ); ?></h3>
    <table class="form-table <?php echo esc_attr( $class_have_latest ); ?>" id="version-check-table">
      <tbody>
        <tr>
          <th><?php printf( __( 'Version %s' ) , '' ); ?></th>
          <td>
            <code><?php echo MYWP_ADD_SITE_AUTO_VERSION; ?></code>
            <a href="<?php echo esc_url( $plugin_info['github'] ); ?>" target="_blank" class="button button-primary link-latest"><?php printf( __( 'Get Version %s' ) , $have_latest ); ?></a>
            <p class="already-latest"><span class="dashicons dashicons-yes"></span> <?php _e( 'Using a latest version.' , 'mywp-add-site-auto' ); ?></p>
            <br />
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Check latest' , 'mywp-add-site-auto' ); ?></th>
          <td>
            <button type="button" id="check-latest-version" class="button button-secondary check-latest"><span class="dashicons dashicons-update"></span> <?php _e( 'Check latest version' , 'mywp-add-site-auto' ); ?></button>
            <span class="spinner"></span>
            <div id="check-latest-result"></div>
          </td>
        </tr>
        <tr>
          <th><?php _e( 'Documents' , 'my-wp' ); ?></th>
          <td>
            <a href="<?php echo esc_url( $plugin_info['document_url'] ); ?>" class="button button-secondary" target="_blank"><span class="dashicons dashicons-book"></span> <?php _e( 'Documents' , 'my-wp' ); ?>
          </td>
        </tr>
      </tbody>
    </table>

    <p>&nbsp;</p>
    <?php

  }

  public static function mywp_current_admin_print_footer_scripts() {

?>
<style>
#add-site-auto-fields.checking .spinner {
  visibility: visible;
}
#results-site-auto {
  margin: 0 auto 30px auto;
}
#results-site-auto ol li {
  padding: 2px 4px;
}
#results-site-auto ol li.added {
  background: rgba(0, 130, 180, 0.2);
}
#results-site-auto ol li.error {
  background: rgba(255, 0, 0, 0.2);
  font-weight: bold;
}
#results-site-auto ol li.adding .spinner {
  visibility: visible;
}
#version-check-table .spinner {
  visibility: hidden;
}
#version-check-table.checking .spinner {
  visibility: visible;
}
#version-check-table .link-latest {
  margin-left: 12px;
  display: none;
}
#version-check-table .already-latest {
  display: inline-block;
}
#version-check-table .check-latest {
}
#version-check-table.have-latest .link-latest {
  display: inline-block;
}
#version-check-table.have-latest .already-latest {
  display: none;
}
</style>
<script>
jQuery(document).ready(function($){

  $('#check-latest-version').on('click', function() {

    var $version_check_table = $(this).parent().parent().parent().parent();

    $version_check_table.addClass('checking');

    PostData = {
      action: '<?php echo MywpSetting::get_ajax_action_name( self::$id , 'check_latest' ); ?>',
      <?php echo MywpSetting::get_ajax_action_name( self::$id , 'check_latest' ); ?>: '<?php echo wp_create_nonce( MywpSetting::get_ajax_action_name( self::$id , 'check_latest' ) ); ?>'
    };

    $.ajax({
      type: 'post',
      url: ajaxurl,
      data: PostData
    }).done( function( xhr ) {

      if( typeof xhr !== 'object' || xhr.success === undefined ) {

        $version_check_table.removeClass('checking');

        alert( '<?php _e( 'An error has occurred. Please reload the page and try again.' ); ?>' );

        return false;

      }

      if( ! xhr.success ) {

        $version_check_table.removeClass('checking');

        alert( xhr.data.error );

        return false;

      }

      if( xhr.data.is_latest ) {

        $('#check-latest-result').html( xhr.data.message );

        $version_check_table.removeClass('checking');

        return false;

      }

      location.reload();

      return true;

    }).fail( function( xhr ) {

      $version_check_table.removeClass('checking');

      alert( '<?php _e( 'An error has occurred. Please reload the page and try again.' ); ?>' );

      return false;

    });

  });

  $('#do-add-site-auto').on('click', function() {

    var $add_site_form_fields = $('#add-site-auto-fields');
    var $result_site_auto = $('#results-site-auto');
    var $result_site_auto_list = $result_site_auto.find('ol');

    var blog_url = $('#blog_url').val();
    var blog_title = $('#blog_title').val();
    var admin_email = $('#admin_email').val();
    var sites_num = $('#sites_num').val();

    PostData = {
      action: '<?php echo MywpSetting::get_ajax_action_name( self::$id , 'validate_add_site_auto' ); ?>',
      <?php echo MywpSetting::get_ajax_action_name( self::$id , 'validate_add_site_auto' ); ?>: '<?php echo wp_create_nonce( MywpSetting::get_ajax_action_name( self::$id , 'validate_add_site_auto' ) ); ?>',
      blog_url: blog_url,
      blog_title: blog_title,
      admin_email: admin_email,
      sites_num: sites_num
    };

    $add_site_form_fields.addClass('checking');

    $.ajax({
      type: 'post',
      url: ajaxurl,
      data: PostData
    }).done( function( xhr ) {

      if( typeof xhr !== 'object' || xhr.success === undefined ) {

        alert( '<?php _e( 'An error has occurred. Please reload the page and try again.' ); ?>' );

        return false;

      }

      if( ! xhr.success ) {

        alert( xhr.data.error );

        return false;

      }

      for( i = 0; i < xhr.data.sites_num;  i++ ) {

        html = '<li class="add-site-' + i + ' wait">';

        html += '[<span class="blog-id"> waiting...</span>] ';

        html += '<span class="blog-title"></span> ';

        html += '<span class="spinner"></span> <span class="error"></span>';

        html += '</li>';

        $result_site_auto_list.append( html );

      }

      $result_site_auto.show();

      $( 'html,body' ).animate({
        scrollTop: $('#results-site-auto').offset().top - 100
      }, 500, 'swing');

      add_site_auto( xhr.data );

      return true;

    }).fail( function( xhr ) {

      alert( '<?php _e( 'An error has occurred. Please reload the page and try again.' ); ?>' );

      return false;

    }).always( function() {

      $add_site_form_fields.removeClass('checking');

    });

  });

  function add_site_auto( args ) {

    var $result_site_auto = $(document).find('#results-site-auto');
    var $adding_list = $result_site_auto.find('ol li.wait').first();

    if( $adding_list.length < 1 ) {

      return false;

    }

    $adding_list.removeClass('wait').addClass('adding');

    PostData = {
      action: '<?php echo MywpSetting::get_ajax_action_name( self::$id , 'add_site' ); ?>',
      <?php echo MywpSetting::get_ajax_action_name( self::$id , 'add_site' ); ?>: '<?php echo wp_create_nonce( MywpSetting::get_ajax_action_name( self::$id , 'add_site' ) ); ?>',
      blog_url: args.blog_url,
      blog_title: args.blog_title,
      admin_email: args.admin_email
    };

    $.ajax({
      type: 'post',
      url: ajaxurl,
      data: PostData
    }).done( function( xhr ) {

      if( typeof xhr !== 'object' || xhr.success === undefined ) {

        $adding_list.addClass('error').find('.error').text( '<?php _e( 'An error has occurred. Please reload the page and try again.' ); ?>' );

        return false;

      }

      if( ! xhr.success ) {

        $adding_list.addClass('error').find('.error').text( xhr.data.error );

        return false;

      }

      $adding_list.addClass('added');

      $adding_list.find('.blog-id').text( xhr.data.created_blog_id );

      $adding_list.find('.blog-title').html( '<a href="' + xhr.data.new_site_url + '" target="_blank">' + xhr.data.new_site_title + '</a>' );

      return true;

    }).fail( function( xhr ) {

      $adding_list.addClass('error').find('.error').text( '<?php _e( 'An error has occurred. Please reload the page and try again.' ); ?>' );

      return false;

    }).always( function() {

      $adding_list.removeClass('adding');

      add_site_auto( args );

    });

  }

});
</script>
<?php

  }

}

MywpSettingScreenAddSiteAuto::init();

endif;
