<?php
if ( ! defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

/**
 * Class Zume_FTT_Public_Heatmaps_Menu
 */
class Zume_FTT_Public_Heatmaps_Menu {

    public $token = 'zume_ftt_public_heatmaps';

    private static $_instance = null;

    /**
     * Zume_FTT_Public_Heatmaps_Menu Instance
     *
     * Ensures only one instance of Zume_FTT_Public_Heatmaps_Menu is loaded or can be loaded.
     *
     * @since 0.1.0
     * @static
     * @return Zume_FTT_Public_Heatmaps_Menu instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } // End instance()


    /**
     * Constructor function.
     * @access  public
     * @since   0.1.0
     */
    public function __construct() {

        add_action( "admin_menu", array( $this, "register_menu" ) );

    } // End __construct()


    /**
     * Loads the subnav page
     * @since 0.1
     */
    public function register_menu() {
        add_submenu_page( 'dt_extensions', 'Zume Map/Report', 'Zume Map/Report', 'manage_dt', $this->token, [ $this, 'content' ] );
    }

    /**
     * Menu stub. Replaced when Disciple Tools Theme fully loads.
     */
    public function extensions_menu() {}

    /**
     * Builds page contents
     * @since 0.1
     */
    public function content() {

        if ( !current_user_can( 'manage_dt' ) ) { // manage dt is a permission that is specific to Disciple Tools and allows admins, strategists and dispatchers into the wp-admin
            wp_die( 'You do not have sufficient permissions to access this page.' );
        }

        if ( isset( $_GET["tab"] ) ) {
            $tab = sanitize_key( wp_unslash( $_GET["tab"] ) );
        } else {
            $tab = 'general';
        }

        $link = 'admin.php?page='.$this->token.'&tab=';

        ?>
        <div class="wrap">
            <h2>FTT Map</h2>
            <h2 class="nav-tab-wrapper">
                <a href="<?php echo esc_attr( $link ) . 'general' ?>"
                   class="nav-tab <?php echo esc_html( ( $tab == 'general' || !isset( $tab ) ) ? 'nav-tab-active' : '' ); ?>">General</a>
            </h2>

            <?php
            switch ($tab) {
                case "general":
                    $object = new Zume_FTT_Public_Heatmaps_Tab_General();
                    $object->content();
                    break;

                default:
                    break;
            }
            ?>

        </div><!-- End wrap -->

        <?php
    }
}
Zume_FTT_Public_Heatmaps_Menu::instance();

/**
 * Class Zume_FTT_Public_Heatmaps_Tab_General
 */
class Zume_FTT_Public_Heatmaps_Tab_General {
    public function content() {
        ?>
        <div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-1">
                    <div id="post-body-content">
                        <!-- Main Column -->

                        <?php $this->main_column() ?>
                        <?php DT_Ipstack_API::metabox_for_admin(); ?>

                        <!-- End Main Column -->
                    </div><!-- end post-body-content -->
                    <div id="postbox-container-1" class="postbox-container">
                        <!-- Right Column -->


                        <!-- End Right Column -->
                    </div><!-- postbox-container 1 -->
                    <div id="postbox-container-2" class="postbox-container">
                    </div><!-- postbox-container 2 -->
                </div><!-- post-body meta box container -->
            </div><!--poststuff end -->
        </div><!-- wrap end -->
        <?php
    }

    public function main_column() {
        ?>
        <form method="post">
            <?php wp_nonce_field( 'heatmap_settings_nonce', 'heatmap_settings' )?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
            <tr>
                <th>Heatmaps</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                   FTT Public Heatmap (1000)<br>
                    <a href="<?php echo esc_url( site_url() ) ?>/zume_app/heatmap_1000"><?php echo esc_url( site_url() ) ?>/zume_app/heatmap_1000</a>
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        </form>
        <?php
    }
}

/**
 * Class Zume_FTT_Public_Heatmaps_Tab_General
 */
class Zume_FTT_Public_Heatmaps_Tab_ShortCodes {
    public function content() {
        ?>
        <div class="wrap">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <!-- Main Column -->

                        <?php $this->main_column() ?>

                        <!-- End Main Column -->
                    </div><!-- end post-body-content -->
                    <div id="postbox-container-1" class="postbox-container">
                        <!-- Right Column -->

                        <?php $this->right_column() ?>

                        <!-- End Right Column -->
                    </div><!-- postbox-container 1 -->
                    <div id="postbox-container-2" class="postbox-container">
                    </div><!-- postbox-container 2 -->
                </div><!-- post-body meta box container -->
            </div><!--poststuff end -->
        </div><!-- wrap end -->
        <?php
    }

    public function main_column() {
        DT_Ipstack_API::metabox_for_admin();
    }

    public function right_column() {
        ?>
        <!-- Box -->
        <table class="widefat striped">
            <thead>
            <tr>
                <th>Information</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    Content
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <!-- End Box -->
        <?php
    }
}
