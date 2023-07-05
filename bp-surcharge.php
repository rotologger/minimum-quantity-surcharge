<?php

/*
Plugin Name: Minimum Quantity Surcharge
Version: 1.0
Author: Fabian Genthner
Author URI: https://fabiangenthner.de
Text Domain: bp_surcharge
License: GPLv2
*/

class BP_Surcharge {
    function __construct() {
        add_action( 'admin_menu', [ $this, 'settings_menu' ] );
        add_action( 'admin_init', [ $this, 'admin_init' ] );
        add_action( 'woocommerce_cart_calculate_fees', [ $this, 'add_surcharge_line' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_color_picker' ] );
    }

    function settings_menu() {
        $options_page = add_options_page(
            'Surcharge Settings Page',
            'Surcharge Settings',
            'manage_options',
            'bp-surcharge-settings',
            [ $this, 'config_page' ]
        );
    }

    function config_page() {
        $options = $this->get_options(); ?>

        <div class="wrap">
            <?php if ( isset( $_GET[ 'message' ] ) && $_GET[ 'message' ] == '1' ) { ?>
                <div id="message" class="updated fade">
                    <p><strong><?php _e( 'Settings saved', 'bp_surcharge' ); ?></strong></p>
                </div>
            <?php } ?>

            <h2><?php _e( 'Minimum Quantity Surcharge Settings' ); ?></h2>

            <form method="post" action="admin-post.php">
                <input type="hidden" name="action" value="save_bp_surcharge_options" />
                <?php wp_nonce_field( 'bp_surcharge' ); ?>
            
                <label for="limit"><?php _e( 'Limit', 'bp_surcharge' ); ?></label><br>
                <input 
                    name="limit"
                    type="number" 
                    value="<?php echo esc_attr( $options[ 'limit' ] ); ?>" 
                    step="0.01" 
                /> €
                <br>
                <label for="amount"><?php _e( 'Amount of surcharge', 'bp_surcharge' ); ?></label><br>
                <input 
                    name="amount"
                    type="number"
                    value="<?php echo esc_attr( $options[ 'amount' ] ); ?>"
                    step="0.01"
                /> €
                <br>

                <label for="info"><?php _e( 'Info Message', 'bp_surcharge' ); ?></label><br>
                <textarea name="info" cols="30" rows="5"><?php echo esc_html( $options[ 'info' ] ); ?></textarea>
                <br>

                <label for="color"><?php _e( 'Text Color', 'bp_surcharge' ); ?></label><br>
                <input 
                    name="color" 
                    type="text" 
                    value="<?php echo esc_attr( $options[ 'color' ] ); ?>" 
                    class="color-field" 
                    data-default-color="#fff"
                />
                <br>

                <label for="bg-color"><?php _e( 'Background Color', 'bp_surcharge' ); ?></label><br>
                <input 
                    name="bg_color" 
                    type="text" 
                    value="<?php echo esc_attr( $options[ 'bg_color' ] ); ?>" 
                    class="bg-color-field" 
                    data-default-color="#000"
                />
                <br><br>

                <input type="submit" value="<?php _e( 'Save Settings', 'bp_surcharge' ); ?>" class="button-primary">
            </form>
        </div>
    <?php }

    function get_options() {
        $options = get_option( 'bp_surcharge_options', [] );
        $new_options[ 'limit' ] = 100;
        $new_options[ 'amount' ] = 20;
        $new_options[ 'info' ]  = __( 'Surcharge of 24 $ is due under a total order value of 100 $.', 'bp_surcharge' );
        $new_options[ 'color' ] = '#fff';
        $new_options[ 'bg_color' ] = '#000';

        $merged_options = wp_parse_args( $options, $new_options );
        $compare_options = array_diff_key( $new_options, $options );

        if ( empty( $options ) || !empty( $compare_options ) ) {
            update_option( 'bp_surcharge_options', $merged_options );
        }
        return $merged_options;
    }

    function admin_init() {
        add_action( 'admin_post_save_bp_surcharge_options', [ $this, 'process_options' ] );
    }

    function process_options() {
        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( 'Not allowed' );
        }

        $options = $this->get_options( 'bp_surcharge_options' );

        check_admin_referer( 'bp_surcharge' );

        foreach ( $options as $key => $value ) {
            $options[ $key ] = $_POST[ $key ];
        }

        update_option( 'bp_surcharge_options', $options );

        wp_redirect( add_query_arg(
            [
                'page'      => 'bp-surcharge-settings',
                'message'   => '1'
            ],
            admin_url( 'options-general.php' )
        ) );
        exit;
    }

    function add_surcharge_line( $cart ) {
        [ 'limit' => $limit, 'amount' => $amount ] = get_option( 'bp_surcharge_options' );

        $cart_total = WC()->cart->cart_contents_total;

        if ( $cart_total < $limit ) {
            $cart->add_fee( __( 'Minimum Quantity Surcharge', 'bp_surcharge' ) , $amount );
        }
    }

    function enqueue_assets() {
        [ 
            'info'      => $info, 
            'color'     => $color, 
            'bg_color'  => $bg_color 
        ] = get_option( 'bp_surcharge_options' );

        wp_enqueue_style( 
            'bp-surcharge-style', 
            plugins_url( 'style.css', __FILE__ ),
            NULL, 
            '1.0'
        );
        wp_enqueue_script( 'bp-surcharge-script', plugins_url( 'script.js', __FILE__ ), [ 'jquery' ], '1.0', true );

        wp_localize_script( 
            'bp-surcharge-script', 
            'bpData', 
            [ 
                'info'      => $info,
                'color'     => $color,
                'bgColor'   => $bg_color
            ] 
        );
    }

    function enqueue_color_picker( $hook_suffix ) {
        if ( $hook_suffix === 'settings_page_bp-surcharge-settings' ) {
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_script( 'bp-surcharge-picker', plugins_url( 'color-picker.js', __FILE__ ), [ 'wp-color-picker' ], '1.0', true );
        }
    }
}

$bp_surcharge = new BP_Surcharge();