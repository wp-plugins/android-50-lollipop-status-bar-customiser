<?php
/*
Plugin Name: Android 5.0 Lollipop Status Bar Customiser
Plugin URI: http://www.webniraj.com/wordpress/
Description: Use this plugin to change the Status Bar of Devices Running Android 5.0 Lollipop.
Author: Niraj Shah
Version: 1.0
Author URI: http://www.webniraj.com/
*/

class WN_Android_50_Statusbar
{
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  
  /**
   * Start up
   */
  public function __construct()
  {
    if ( is_admin() ) {
      add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
      add_action( 'admin_init', array( $this, 'page_init' ) );
      add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'add_action_links' ) );
    }
    add_action( 'wp_head', array( $this, 'add_android_meta' ) );
  }
  
  /**
   * Add Settings Plugin link
   */
  function add_action_links( $links ) {
    $mylinks = array(
      '<a href="' . admin_url( 'themes.php?page=wn-android-statusbar' ) . '">Settings</a>',
    );
    // remove edit link
    unset( $links['edit'] );
    // return new links
    return array_merge( $mylinks, $links );
  }
  
  /**
   * Add options page
   */
  public function add_plugin_page()
  {
    // This page will be under "Settings"
    add_theme_page(
      'Settings Admin', 
      'Android Status Bar', 
      'manage_options', 
      'wn-android-statusbar', 
      array( $this, 'settings_page' )
    );
  }
  
  /**
   * Options page callback
   */
  public function settings_page()
  {
    // Set class property
    $this->options = get_option( 'wn_android_statusbar' );
    ?>
    <div class="wrap">
        <?php screen_icon(); ?>
        <h2>Androind 5.0 Lollipop Statusbar</h2>           
        <form method="post" action="options.php">
        <?php
          // This prints out all hidden setting fields
          settings_fields( 'wn_android_options' );   
          do_settings_sections( 'wn-android-statusbar' );
          submit_button(); 
        ?>
        </form>
    </div>
    <?php
  }
  
  /**
   * Add android meta data to header
   */
  public function add_android_meta()
  {
    // get options
    $this->options = get_option( 'wn_android_statusbar' );
    
    $output = '<!-- WebNiraj Androind 5.0 Status Bar Plugin -->' . PHP_EOL;
    
    if ( is_home() ) {
      $output .= '<meta name="theme-color" content="#'. ( isset( $this->options['home-colour'] ) ? $this->options['home-colour'] : '#3B9BD6' ) .'">' . PHP_EOL;
    } else if ( is_single() ) {
      $output .= '<meta name="theme-color" content="#'. ( isset( $this->options['post-colour'] ) ? $this->options['post-colour'] : '#57585A' ) .'">' . PHP_EOL;
    } else if ( is_page() ) {
      $output .= '<meta name="theme-color" content="#'. ( isset( $this->options['page-colour'] ) ? $this->options['page-colour'] : '#91268E' ) .'">' . PHP_EOL;
    } else {
      $output .= '<meta name="theme-color" content="#'. ( isset( $this->options['default-colour'] ) ? $this->options['default-colour'] : '#3F51B5' ) .'">' . PHP_EOL;
    }
    
    echo $output;
  }
  
  /**
   * Register and add settings
   */
  public function page_init()
  {        
    register_setting(
      'wn_android_options', // Option group
      'wn_android_statusbar', // Option name
      array( $this, 'sanitize' ) // Sanitize
    );

    add_settings_section(
      'setting_section_id', // ID
      'Status Bar Colour', // Title
      array( $this, 'print_section_info' ), // Callback
      'wn-android-statusbar' // Page
    );

    add_settings_field(
      'default-colour', // ID
      'Default Colour', // Title 
      array( $this, 'colour_callback' ), // Callback
      'wn-android-statusbar', // Page
      'setting_section_id', // Section     
      [ 'id' => 'default-colour' ]            
    );
    
    add_settings_field(
      'home-colour', // ID
      'Home Page Colour', // Title 
      array( $this, 'colour_callback' ), // Callback
      'wn-android-statusbar', // Page
      'setting_section_id', // Section     
      [ 'id' => 'home-colour' ]            
    );
    
    add_settings_field(
      'page-colour', // ID
      'Page Colour', // Title 
      array( $this, 'colour_callback' ), // Callback
      'wn-android-statusbar', // Page
      'setting_section_id', // Section     
      [ 'id' => 'page-colour' ]      
    );
    
    add_settings_field(
      'post-colour', // ID
      'Post Colour', // Title 
      array( $this, 'colour_callback' ), // Callback
      'wn-android-statusbar', // Page
      'setting_section_id', // Section     
      [ 'id' => 'post-colour' ]           
    );
  }
  
  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function sanitize( $input )
  {
    $new_input = array();
    foreach ( $input as $id => $val )
      $new_input[$id] = esc_attr( $val );

    return $new_input;
  }
  
  /** 
   * Print the Section text
   */
  public function print_section_info()
  {
    print 'Enter a <a href="http://www.w3schools.com/tags/ref_colorpicker.asp" target="_blank">HEX colour</a> below:';
  }
  
  /** 
   * Get the settings option array and print one of its values
   */
  public function colour_callback( $args )
  {
    printf(
      '#<input type="text" name="wn_android_statusbar[%s]" value="%s" placeholder="3f51b5" />',
      $args['id'],
      isset( $this->options[ $args['id'] ] ) ? esc_attr( $this->options[ $args['id'] ] ) : ''
    );
  }
  
}

// run plugin
$wn_android_statusbar = new WN_Android_50_Statusbar();