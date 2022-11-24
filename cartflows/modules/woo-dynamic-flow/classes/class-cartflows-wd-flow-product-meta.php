<?php
/**
 * Dynamic flow product meta.
 *
 * @package CartFlows
 */

/**
 * Initialization
 *
 * @since 1.0.0
 */
class Cartflows_Wd_Flow_Product_Meta {


	/**
	 * Member Variable
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 *  Constructor
	 */
	public function __construct() {

		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_tab' ) );
		add_action( 'woocommerce_product_data_panels', array( $this, 'add_tab_content' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_meta' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'add_cartflows_scipt' ) );
		add_action( 'wp_ajax_wcf_json_search_flows', array( $this, 'json_search_flows' ) );
	}

	/**
	 * Add Script.
	 */
	public function add_cartflows_scipt() {

		if ( ! is_admin() ) {
			return;
		}

		$current_screen = get_current_screen();

		if ( ! empty( $current_screen ) && 'product' === $current_screen->id ) {

			wp_enqueue_script( 'wcf-product-page-setting', CARTFLOWS_URL . 'admin/assets/js/product-page.js', array( 'jquery' ), CARTFLOWS_VER, true );
		}
	}

	/**
	 * Add CartFlows tab.
	 *
	 * @param array $tabs tabs.
	 */
	public function add_tab( $tabs ) {

		$tabs['cartflows'] = array(
			'label'    => __( 'CartFlows', 'cartflows' ),
			'target'   => 'cartflows_product_data',
			'class'    => array(),
			'priority' => 80,
		);

		return $tabs;
	}

	/**
	 * Function to search coupons.
	 */
	public function json_search_flows() {

		if ( isset( $_POST['security'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['security'] ) ), 'wcf_json_search_flows' ) ) {

			global $wpdb;

			$term = (string) urldecode( sanitize_text_field( wp_unslash( $_POST['term'] ) ) ); // phpcs:ignore

			if ( empty( $term ) ) {
				die();
			}

			$posts = $wpdb->get_results( // phpcs:ignore
				$wpdb->prepare(
					"SELECT *
								FROM {$wpdb->prefix}posts
								WHERE post_type = %s
								AND post_title LIKE %s
								AND post_status = %s",
					CARTFLOWS_FLOW_POST_TYPE,
					$wpdb->esc_like( $term ) . '%',
					'publish'
				)
			);

			$flows_found = array();

			if ( $posts ) {
				foreach ( $posts as $post ) {
					$flows_found[ $post->ID ] = get_the_title( $post->ID );
				}
			}

			wp_send_json( $flows_found );

		}
	}

	/**
	 * Tab content.
	 */
	public function add_tab_content() {

		echo '<div id="cartflows_product_data" class="panel woocommerce_options_panel hidden">';

		$this->woocommerce_select2(
			array(
				'id'          => 'cartflows_redirect_flow_id',
				'name'        => 'cartflows_redirect_flow_id',
				'value'       => get_post_meta( get_the_ID(), 'cartflows_redirect_flow_id', true ),
				'label'       => __( 'Select the Flow', 'cartflows' ),
				'class'       => '',
				'placeholder' => __( 'Type to search a flow...', 'cartflows' ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id'          => 'cartflows_add_to_cart_text',
				'value'       => get_post_meta( get_the_ID(), 'cartflows_add_to_cart_text', true ),
				'label'       => __( 'Add to Cart text', 'cartflows' ),
				'name'        => 'cartflows_add_to_cart_text',
				'placeholder' => __( 'Add to cart', 'cartflows' ),
			)
		);

		/* translators: %1$s,%2$s HTML content */
		echo '<span class="wcf-shortcode-notice"><p>' . sprintf( __( 'If you want to start the flow from the product page, select the appropriate flow & button text field if required. Refer %1$sthis article%2$s for more information.', 'cartflows' ), '<a href="https://cartflows.com/docs/how-to-start-a-flow-from-product-page/" style="text-decoration:none;" target="_blank">', '</a>' );

		/* //phpcs:ignore
		Commented.
		echo '<hr>';
		echo '<span class="wcf-shortcode-notice" ><p>' . __( 'If you want to add this product\'s add-to-cart button in the flow\'s landing step, then use the below shortcode.', 'cartflows' );
		echo '<p class="form-field cartflows_atc_shortocde_field ">';
			echo '<label for="cartflows_atc_shortocde">' . __( 'Add to Cart Shortcode', 'cartflows' ) . '</label>';
			echo '<input type="text" class="short" style="" name="cartflows_atc_shortocde" id="cartflows_atc_shortocde" value="' . sprintf( esc_html( '[cartflows_product_add_to_cart id="%s" text="Buy Now" ]' ), get_the_ID() ) . '" readonly="readonly">';
		echo '</p>';
		*/

		echo '</div>';

	}

	/**
	 * Woocommerce Select2 field.
	 *
	 * @param array $field field data.
	 */
	public function woocommerce_select2( $field ) {

		global $woocommerce;

		echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ">
		
		<label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>
		
		<select data-action="wcf_json_search_flows" 
		id="' . esc_attr( $field['id'] ) . '" 
		name="' . esc_attr( $field['name'] ) . '" 
		class="wcf-flows-search ' . esc_attr( $field['class'] ) . '" 
		data-allow_clear="allow_clear" 
		data-placeholder="' . esc_attr( $field['placeholder'] ) . '" 
		style="width:50%" >';

		if ( ! empty( $field['value'] ) ) {
			// posts.
			$post_title = get_the_title( intval( $field['value'] ) );
			echo '<option value="' . $field['value'] . '" selected="selected" >' . $post_title . '</option>';
		}
		echo '</select> ';
		if ( ! empty( $field['description'] ) ) {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}
		echo '<input type="hidden" name="wcf_json_search_flows_nonce" value="' . wp_create_nonce( 'wcf_json_search_flows' ) . '" >';
		echo '</p>';

	}

	/**
	 * Save product meta.
	 *
	 * @param int $post_id product id.
	 */
	public function save_product_meta( $post_id ) {

		$product = wc_get_product( $post_id );

		$next_step = isset( $_POST['cartflows_redirect_flow_id'] ) ? intval( $_POST['cartflows_redirect_flow_id'] ) : ''; //phpcs:ignore

		$add_to_cart_text = isset( $_POST['cartflows_add_to_cart_text'] ) ? sanitize_text_field( $_POST['cartflows_add_to_cart_text'] ) : '';  //phpcs:ignore

		$product->update_meta_data( 'cartflows_redirect_flow_id', $next_step );
		$product->update_meta_data( 'cartflows_add_to_cart_text', $add_to_cart_text );

		$product->save();
	}
}

/**
 *  Kicking this off by calling 'get_instance()' method
 */
Cartflows_Wd_Flow_Product_Meta::get_instance();
