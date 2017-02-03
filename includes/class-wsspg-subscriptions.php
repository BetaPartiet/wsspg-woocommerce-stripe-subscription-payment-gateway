<?php
/**
 * Wsspg Subscriptions
 *
 * Handles the subscription process.
 *
 * @since       1.0.0
 * @package     Wsspg
 * @subpackage  Wsspg/includes
 * @author      wsspg <wsspg@mail.com>
 * @license     https://www.gnu.org/licenses/gpl-3.0.txt
 * @copyright   2016 (c) http://wsspg.co
 */

if( ! defined( 'ABSPATH' ) ) exit; // exit if accessed directly.

/**
 * Wsspg Subscriptions Class
 *
 * @since  1.0.0
 * @class  Wsspg_Subscriptions
 */
class Wsspg_Subscriptions {
	
	/**
	 * Enabled/disabled.
	 *
	 * @since  1.0.0
	 */
	private $enabled;
	
	/**
	 * Gateway settings.
	 *
	 * @since  1.0.0
	 */
	private $settings;
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since  1.0.0
	 */
	public function __construct() {
		
		$this->enabled = Wsspg::subscriptions_enabled();
		$this->settings = Wsspg::get_settings();
	}
	
	/**
	 * Add product data tab.
	 *
	 * @since  1.0.0
	 */
	public function wsspg_subscriptions_woocommerce_product_data_tabs( $product_data_tabs ) {
		
		$product_data_tabs['wsspg-subscription-product-data-tab'] = array(
			'label' => __( 'Subscription', 'wsspg' ),
			'target' => 'wsspg-subscription-product-data-tab',
			'class' => 'wsspg-subscription-product-data-tab-label show_if_wsspg_subscription'
		);
		return $product_data_tabs;
	}
	
	/**
	 * Output product data panel.
	 *
	 * @since  1.0.0
	 */
	public function wsspg_subscriptions_woocommerce_product_data_panels() {
		
		echo '<div id="wsspg-subscription-product-data-tab" class="panel woocommerce_options_panel">';
		global $woocommerce, $post;
		$meta = get_post_meta( $post->ID );
		$plan = array(
			'id' => '',
			'name' => '',
			'amount' => '',
			'currency' => '',
			'interval' => '',
			'placeholder' => ''
		);
		if( array_key_exists( '_wsspg_stripe_plan_id', $meta ) ) {
			$plan['id'] = $meta['_wsspg_stripe_plan_id'][0];
		}
		if( isset( $plan['id'] ) && $plan['id'] !== '' && !array_key_exists( '_wsspg_stripe_plan_name', $meta ) ) {
			$plan['placeholder'] = sprintf( __( 'No such plan: %s', 'wsspg' ), $plan['id'] );
			$plan['id'] = '';
		}
		woocommerce_wp_text_input( array(
			'id'=> 'wsspg-subscription-product-stripe-plan-id',
			'label' => __( 'Stripe Plan ID', 'wsspg' ),
			'placeholder' => $plan['placeholder'],
			'value' => $plan['id'],
			'description' => sprintf(
				__( 'Find the plan ID on your %sStripe Dashboard%s.%s', 'wsspg' ),
				'<a href="https://dashboard.stripe.com/plans" target="_blank">',
				'</a>',
				'<hr>'
			),
			'type'=> 'text'
		) );
		if( array_key_exists( '_wsspg_stripe_plan_name', $meta ) ) {
			woocommerce_wp_text_input( array(
				'id'=> 'wsspg-subscription-product-stripe-plan-name',
				'label' => __( 'Name', 'wsspg' ),
				'placeholder' => $meta['_wsspg_stripe_plan_name'][0],
				'description' => '',
				'type'=> 'text',
				'value' => $meta['_wsspg_stripe_plan_name'][0],
			) );
		}
		if( array_key_exists( '_wsspg_stripe_plan_amount', $meta ) ) {
			woocommerce_wp_text_input( array(
				'id'=> 'wsspg-subscription-product-stripe-plan-amount',
				'label' => __( 'Amount', 'wsspg' ),
				'placeholder' => $meta['_wsspg_stripe_plan_amount'][0],
				'description' => '',
				'type'=> 'text',
				'value' => $meta['_wsspg_stripe_plan_amount'][0],
			) );
		}
		if( array_key_exists( '_wsspg_stripe_plan_currency', $meta ) ) {
			woocommerce_wp_text_input( array(
				'id'=> 'wsspg-subscription-product-stripe-plan-currency',
				'label' => __( 'Currency', 'wsspg' ),
				'placeholder' => $meta['_wsspg_stripe_plan_currency'][0],
				'description' => '',
				'type'=> 'text',
				'value' => $meta['_wsspg_stripe_plan_currency'][0],
			) );
		}
		if( array_key_exists( '_wsspg_stripe_plan_interval', $meta ) ) {
			woocommerce_wp_text_input( array(
				'id'=> 'wsspg-subscription-product-stripe-plan-interval',
				'label' => __( 'Interval', 'wsspg' ),
				'placeholder' => $meta['_wsspg_stripe_plan_interval'][0],
				'description' => '',
				'type'=> 'text',
				'value' => $meta['_wsspg_stripe_plan_interval'][0],
			) );
		}
		if( array_key_exists( '_wsspg_stripe_plan_interval_count', $meta ) ) {
			woocommerce_wp_text_input( array(
				'id'=> 'wsspg-subscription-product-stripe-plan-interval-count',
				'label' => __( 'Interval Count', 'wsspg' ),
				'placeholder' => $meta['_wsspg_stripe_plan_interval_count'][0],
				'description' => '',
				'type'=> 'text',
				'value' => $meta['_wsspg_stripe_plan_interval_count'][0],
			) );
		}
		if( array_key_exists( '_wsspg_stripe_plan_trial_period_days', $meta ) ) {
			woocommerce_wp_text_input( array(
				'id'=> 'wsspg-subscription-product-stripe-plan-trial-period-days',
				'label' => __( 'Trial Period Days', 'wsspg' ),
				'placeholder' => $meta['_wsspg_stripe_plan_trial_period_days'][0],
				'description' => '<hr>',
				'type'=> 'text',
				'value' => $meta['_wsspg_stripe_plan_trial_period_days'][0],
			) );
		}
		$plan = array(
			'enable_multiple_subscriptions' => 'no',
		);
		if( array_key_exists( '_wsspg_enable_multiple_subscriptions', $meta ) ) {
			$plan['enable_multiple_subscriptions'] = $meta['_wsspg_enable_multiple_subscriptions'][0];
		}
		woocommerce_wp_checkbox( array(
			'id'=> 'wsspg-enable-multiple-subscriptions',
			'label' => __( 'Multiple Subscriptions', 'wsspg' ),
			'value' => $plan['enable_multiple_subscriptions'],
			'description' => sprintf(
				__( 'Enable this to allow customers to sign up to this plan more than once.', 'wsspg' )
			),
			'type'=> 'checkbox'
		) );
		$roles = array();
		if( array_key_exists( '_wsspg_subscription_product_user_roles', $meta ) ) {
			$roles = maybe_unserialize( $meta['_wsspg_subscription_product_user_roles'][0] );
		}
		woocommerce_wp_text_input( array(
			'id'=> 'wsspg-subscription-product-user-roles',
			'label' => __( 'User Roles', 'wsspg' ),
			'placeholder' => '',
			'value' => implode( ', ', $roles ),
			'desc_tip' => 'true',
			'description' => __( 'Add roles to registered users when they purchase this subscription. Accepts comma-separated list of values.', 'wsspg' ),
			'type'=> 'text'
		) );
		echo '</div>';
	}
	
	/**
	 * Filters add_to_cart_validation for Wsspg settings.
	 *
	 * @since  1.0.0
	 */
	public function wsspg_subscriptions_woocommerce_add_to_cart_validation( $validation, $object_id ) {
		
		return $validation;
	}
	
	/**
	 * Filters is_purchasable.
	 *
	 * Returns FALSE if:
	 * - subscriptions are disabled.
	 * - the user is not logged in and guest subscriptions have been disabled.
	 * - multiple subscriptions are disabled for the product and it has already been purchased.
	 *
	 * @since  1.0.0
	 */
	public function wsspg_subscriptions_woocommerce_is_purchasable( $is_purchasable, $object ) {
		
		if( $is_purchasable ) {
			global $user_ID;
			$user = wp_get_current_user();
			$product = wc_get_product( $object->id );
			if( $product->is_type( 'wsspg_subscription' ) ) {
				if( ! $this->enabled ) return false;
				if( '' == $user_ID && $this->settings['guest_subscriptions'] !== 'enabled' ) return false;
				$plan = $product->product_custom_fields['_wsspg_stripe_plan_id'];
				$ems = $product->product_custom_fields['_wsspg_enable_multiple_subscriptions'];
				if( isset( $ems ) && $ems[0] === 'no' ) {
					$meta = maybe_unserialize( get_user_meta( $user_ID, WSSPG_PLUGIN_MODE.'_subscriptions', true ) );
					if( isset( $meta ) && is_array( $meta ) && in_array( $plan[0], $meta ) ) return false;
				}
			}
		}
		return $is_purchasable;
	}
	
	/**
	 * Filters is_sold_individually.
	 *
	 * Returns TRUE if:
	 * - the product is not already marked as sold individually and the product is not enabled for multiple subscriptions.
	 *
	 * @since  1.0.0
	 */
	public function wsspg_subscriptions_woocommerce_is_sold_individually( $is_sold_individually, $object ) {
		
		$id = $object->id;
		$type = $object->product_type;
		$fields = print_r( $object->product_custom_fields, true );
		$ems = get_post_meta( $id, '_wsspg_enable_multiple_subscriptions', true );
		$print = "<h2>{$id}</h2><p>{$type}</p><p>{$ems}</p><p>{$fields}</p>";
		if( $type === 'wsspg_subscription' && ! $is_sold_individually && $ems !== 'yes' ) return true;
		return $is_sold_individually;
	}
	
	/**
	 * Filters is_visible.
	 *
	 * Returns FALSE if:
	 * - subscriptions are disabled.
	 *
	 * @since  1.0.0
	 */
	public function wsspg_subscriptions_woocommerce_product_is_visible( $is_visible, $object_id ) {
		
		if( $is_visible ) {
			$product = wc_get_product( $object_id );
			if( $product->is_type( 'wsspg_subscription' ) && ! $this->enabled ) return false;
		}
		return $is_visible;
	}
	
	/**
	 * Alter the main query to include/exclude subscriptions.
	 *
	 * @since  1.0.0
	 */
	public function wsspg_subscriptions_pre_get_posts( $q ) {
		
		if( ! $q->is_main_query() ) return;
		if( ! $q->is_post_type_archive() ) return;
		if( ! is_admin() ) {
			if( ! $this->enabled ) {
				$q->set( 'meta_query', array( array(
					'key' => '_wsspg_subscription',
					'value' => 'yes',
					'compare' => 'NOT IN'
				) ) );
			}
		}
		remove_action( 'pre_get_posts', 'wsspg_subscriptions_pre_get_posts' );
	}
	
	/**
	 * Add product type.
	 *
	 * @since  1.0.0
	 */
	public function wsspg_subscriptions_product_type_selector( $types ) {
		
		$types[ 'wsspg_subscription' ] = __( 'Wsspg Subscription Product', 'wsspg' );
		return $types;
	}
	
	/**
	 * Process and save product meta fields.
	 *
	 * @since  1.0.0
	 */
	public function wsspg_subscriptions_woocommerce_process_product_meta( $post_id ) {
		
		$this->wsspg_subscriptions_save_post( $post_id );
	}
	
	/**
	 * Save product settings fields.
	 *
	 * @since  1.0.0
	 */
	public function wsspg_subscriptions_save_post( $post_id ) {
		
		if( isset( $_POST['product-type'] ) && isset( $_POST['wsspg-subscription-product-stripe-plan-id'] ) ) {
			$product_type  = $_POST['product-type'];
			$plan_id       = $_POST['wsspg-subscription-product-stripe-plan-id'];
			if( !empty( $plan_id ) && $product_type === 'wsspg_subscription' ) {
				//	is a wsspg subscription.
				update_post_meta(
					$post_id,
					'_wsspg_subscription',
					'yes'
				);
				//	plan id.
				update_post_meta(
					$post_id,
					'_wsspg_stripe_plan_id',
					$plan_id
				);
				//	inventory controls.
				$enable_multiple_subscriptions = isset( $_POST['wsspg-enable-multiple-subscriptions'] ) ? 'yes' : 'no';
				update_post_meta(
					$post_id,
					'_wsspg_enable_multiple_subscriptions',
					$enable_multiple_subscriptions
				);
				//	roles
				$user_roles = array();
				if( isset( $_POST['wsspg-subscription-product-user-roles'] ) ) {
					$string = $_POST['wsspg-subscription-product-user-roles'];
					$string = explode( ',', preg_replace( '/\s+/', '', $string ) );
					foreach( $string as $value )  $user_roles[] = $value;
				}
				update_post_meta(
					$post_id,
					'_wsspg_subscription_product_user_roles',
					$user_roles
				);
				$response = Wsspg_Api::request( "plans/{$plan_id}", Wsspg::get_api_key( 'secret' ) );
				if( is_wp_error( $response ) || !isset( $response ) || isset( $response->error ) ) {
					delete_post_meta( $post_id, '_wsspg_stripe_plan_name' );
					delete_post_meta( $post_id, '_wsspg_stripe_plan_amount' );
					delete_post_meta( $post_id, '_wsspg_stripe_plan_currency' );
					delete_post_meta( $post_id, '_wsspg_stripe_plan_interval' );
					delete_post_meta( $post_id, '_wsspg_stripe_plan_interval_count' );
					delete_post_meta( $post_id, '_wsspg_stripe_plan_trial_period_days' );
				} else {
					$plan = $response;
					$meta_array = array(
						'_price'                                 => $plan->amount / 100,
						'_regular_price'                         => $plan->amount / 100,
						'_sale_price'                            => '',
						'_wsspg_stripe_plan_id'                 => $plan->id,
						'_wsspg_stripe_plan_name'               => $plan->name,
						'_wsspg_stripe_plan_amount'             => $plan->amount / 100,
						'_wsspg_stripe_plan_currency'           => $plan->currency,
						'_wsspg_stripe_plan_interval'           => $plan->interval,
						'_wsspg_stripe_plan_interval_count'     => $plan->interval_count,
						'_wsspg_stripe_plan_trial_period_days'  => $plan->trial_period_days,
					);
					foreach( $meta_array as $meta_key => $meta_value ) {
						//	plan details.
						update_post_meta(
							$post_id,
							$meta_key,
							print_r( $meta_value, true )
						);
					}
				}
			} else {
				//	is not a wsspg subscription.
				update_post_meta(
					$post_id,
					'_wsspg_subscription',
					'no'
				);
				update_post_meta(
					$post_id,
					'_wsspg_stripe_plan_id',
					''
				);
			}
		}
	}
	
	/**
	 * Output the add to cart for subscriptions.
	 *
	 * @since  1.0.0
	 */
	public function wsspg_subscriptions_woocommerce_wsspg_subscription_add_to_cart() {
		
		if( Wsspg::subscriptions_enabled() ) {
			wc_get_template(
				'single-product/add-to-cart/wsspg_subscription.php',
				$args = array(),
				$template_path = '',
				WSSPG_PLUGIN_DIR_PATH . 'templates/'
			);
		}
	}
}
