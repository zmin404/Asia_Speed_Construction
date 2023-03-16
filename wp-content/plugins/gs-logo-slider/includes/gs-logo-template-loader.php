<?php

/**
 * Protect direct access
 */
if ( ! defined( 'ABSPATH' ) ) die( GSL_HACK_MSG );

if ( ! class_exists( 'GS_Logo_Template_Loader' ) ) {

    final class GS_Logo_Template_Loader {

        private static $plugin_template_path = '';

        private static $pro_plugin_template_path = '';

        private static $theme_template_path = '';

        private static $_instance = null;
        
        public static function get_instance() {

            if ( is_null( self::$_instance ) ) {
                self::$_instance = new GS_Logo_Template_Loader();
            }

            return self::$_instance;
            
        }

        public function __construct() {

            self::$plugin_template_path = GSL_PLUGIN_DIR . 'templates/';
            
            if ( gs_logo_is_pro_active() ) {
                self::$pro_plugin_template_path = GSL_PRO_PLUGIN_DIR . 'templates/';
            }

            add_action( 'init', [$this, 'set_theme_template_path'] );

        }

        public function set_theme_template_path() {

            $dir = apply_filters( 'gslogo_templates_folder', 'gs-logo' );

            if ( $dir ) {
                $dir = '/' . trailingslashit( ltrim( $dir, '/\\' ) );
                self::$theme_template_path = get_stylesheet_directory() . $dir;
            }

        }

        public static function locate_template( $template_file ) {

            // Default path
            $path = self::$plugin_template_path;

            // Check if requested file exist in plugin
            if ( ! file_exists( $path . $template_file ) ) {
                if ( ! file_exists( self::$pro_plugin_template_path . $template_file ) ) {
                    return new WP_Error( 'gslogo_template_not_found', __( 'Template file not found - GsPlugins', 'gslogo' ) );
                }
                $path = self::$pro_plugin_template_path;
            }

            // Override default template if exist from theme
            if ( file_exists( self::$theme_template_path . $template_file ) ) $path = self::$theme_template_path;

            // Return template path, it can be default or overridden by theme
            return $path . $template_file;

        }

    }

}

GS_Logo_Template_Loader::get_instance();