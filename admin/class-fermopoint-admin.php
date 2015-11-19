<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://www.digitalissimoweb.it
 * @since      1.0.0
 *
 * @package    Fermopoint
 * @subpackage Fermopoint/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fermopoint
 * @subpackage Fermopoint/admin
 * @author     Digitalissimo <developer@digitalissimoweb.it>
 */
class Fermopoint_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
	public $availability;
	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->init();

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	 
	public function init(){
		
		
	
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		
		add_action( 'woocommerce_admin_order_data_after_shipping_address', 'fermopoint_datafield', 10, 1 );

		add_action('woocommerce_order_status_changed', 'fermopoint_order_status', 10, 3);
		
		add_action( 'woocommerce_email_before_order_table', 'add_order_email_fermopoint', 10, 3 );
		
		add_action('admin_menu', 'register_menuFermopoint');

		
		
		
		
		
	
	} 
	 
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fermopoint_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fermopoint_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fermopoint-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Fermopoint_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Fermopoint_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fermopoint-admin.js', array( 'jquery' ), $this->version, false );

	}
	
	
	
	public static function plugin_row_meta( $links, $file ) {
	
		
		if ( $file == "fermopoint-for-woocommerce/fermopoint-for-woocommerce.php") {
			$row_meta = array(
				'docs'    => '<a href="' . esc_url( apply_filters( 'woocommerce_docs_url', 'http://www.fermopoint.it/business/vantaggi-ecommerce' ) ) . '" title="' . esc_attr( __( 'View Fermo!Point Documentation', 'woocommerce' ) ) . '">' . __( 'Docs', 'woocommerce' ) . '</a>',
				'apidocs' => '<a href="' . esc_url( apply_filters( 'woocommerce_apidocs_url', 'http://api.fermopoint.it/Content/files/ApiReference.pdf' ) ) . '" title="' . esc_attr( __( 'View  API Docs', 'woocommerce' ) ) . '">' . __( 'API Docs', 'woocommerce' ) . '</a>',
				'support' => '<a href="' . esc_url( apply_filters( 'woocommerce_support_url', 'http://www.fermopoint.it/contratto-merchant' ) ) . '" title="' . esc_attr( __( 'Contratto', 'woocommerce' ) ) . '">Contratto</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
	
	

	
	
	
	
	
	
	
	

}








/**
 * Check if WooCommerce is active
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
 
	function fermopoint_shipping_method_init() {
		if ( ! class_exists( 'FermoPoint_Shipping_Method' ) ) {
			class FermoPoint_Shipping_Method extends WC_Shipping_Method {
				/**
				 * Constructor for your shipping class
				 *
				 * @access public
				 * @return void
				 */
				public function __construct() {
					$this->id                 = 'Fermo!Point'; // Id for your shipping method. Should be uunique.
					$this->method_title       = __( 'Fermo!Point' );  // Title shown in admin
					$this->method_description = __( 'Metodo di spedizione per ricevere gli acquisti dei clienti in uno dei punti di ritiro Fermo!Point (<a href="www.fermopoint.it" target="new">www.fermopoint.it</a>)' ); // Description shown in admin
 
					$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
					$this->title              = "Corriere con consegna Fermo!Point"; // This can be added as an setting but for this example its forced.
 
					$this->init();
						
					
		
					add_action( 'woocommerce_after_mini_cart', array($this, 'fermopoint_page'));   
					
				}
 
				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init() {
					// Load the settings API
					
					$this->init_form_fields();
					$this->init_settings();
 
 					$this->title = $this->settings['title'];
 					$this->description = $this->settings['description'];
 					$this->cost = $this->settings['cost'];
					
					//$this->availability = $this->get_option( 'availability' );
					//$this->countries    = $this->get_option( 'countries' );
					
					$this->availability = "specific";
					$this->countries    = array("IT");
					
				//	var_dump($this);
				//	die();
					
					// Save settings in admin if you have any defined
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
					
				}
 
				/**
				 * calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				 
				 /**
			 * Initialise Gateway Settings Form Fields
			 */
			 function init_form_fields() {
				 $this->form_fields = array(
					'enabled' => array(
						'title'         => __( 'Enable/Disable', 'woocommerce' ),
						'type'          => 'checkbox',
						'label'         => __( 'Enable Fermo!Point', 'woocommerce' ),
						'default'       => 'yes'
					),
					 'title' => array(
						  'title' => __( 'Title', 'woocommerce' ),
						  'type' => 'text',
						  'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
						  'default' => __( 'Fermo!Point', 'woocommerce' )
					  ),
					 'description' => array(
						  'title' => __( 'Description', 'woocommerce' ),
						  'type' => 'textarea',
						  'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce' ),
						  'default' => __("Ricevi il tuo acquisto in uno dei punti di ritiro Fermo!Point (www.fermopoint.it)", 'woocommerce')
					   ),
					'cost' => array(
							'title'         => __( 'Cost per order', 'woocommerce' ),
							'type'          => 'price',
							
							'description'   => __( 'Enter a cost (excluding tax) per order, e.g. 5.00. Default is 0.', 'woocommerce' ),
							'default'       => '',
					  		'desc_tip'      => true,
					  		 'placeholder'	=> wc_format_localized_price( 2.50 ),
					 ),
				 	
					
				/*	'availability' => array(
						'title' 		=> __( 'Availability', 'woocommerce' ),
						'type' 			=> 'select',
						'default' 		=> 'specific',
						'class'			=> 'availability wc-enhanced-select',
						'options'		=> array(
							'all' 		=> __( 'All allowed countries', 'woocommerce' ),
							'specific' 	=> __( 'Specific Countries', 'woocommerce' ),
						),
				),
				'countries' => array(
					'title' 		=> __( 'Specific Countries', 'woocommerce' ),
					'type' 			=> 'multiselect',
					'class'			=> 'wc-enhanced-select',
					'css'			=> 'width: 450px;',
					'default' 		=> 'IT',
					'options'		=> WC()->countries->get_shipping_countries(),
					'custom_attributes' => array(
						'data-placeholder' => __( 'Select some countries', 'woocommerce' )
					)
				),*/
					
					
					'sandbox' => array(
						'title'         => __( 'Attiva Sandbox', 'woocommerce' ),
						'type'          => 'checkbox',
						'label'         => __( 'Attiva la modalità Sandbox per ambiente di test', 'woocommerce' ),
						'default'       => 'no',
						
						'description' => __("La modalità Sandbox serve per verificare la buona riuscita dell'installazione del modulo e per effettuare test per l'integrazione con un eventuale template grafico personalizzato. Una volta terminati i test, è necessario disattivare la modalità Sandbox per essere a tutti gli effetti online nell'ambiente di produzione")
						
					),
					
					
			
					
					 'FERMOPOINT_ACCOUNT_ID' => array(
						  'title' => __( 'Inserisci il "Client ID"', 'woocommerce' ),
						  'type' => 'text',
						  'description' => __( 'Il codice utente ricevuto al momento dell\'iscrizione al servizio', 'woocommerce' )
					  ),
					  
					  
					   'FERMOPOINT_ACCOUNT_SECRET' => array(
						  'title' => __( 'Inserisci il "Client Secret"', 'woocommerce' ),
						  'type' => 'text',
						  'description' => __( 'La password ricevuta al momento dell\'iscrizione al servizio', 'woocommerce' )
					  ),
					  
					  
					 /* 'FERMOPOINT_URLRETURN' => array(
						  'title' => __( 'Pagina per la Prenotazione autorizzata', 'woocommerce' ),
						  'type' => 'text',
						  'description' => __( 'Link alla pagina per far procedere l\'utente allo step successivo ', 'woocommerce' )
					  ),
					  
					  'FERMOPOINT_URLCANCEL' => array(
						  'title' => __( 'Pagina per la Prenotazione annullata', 'woocommerce' ),
						  'type' => 'text',
						  'description' => __( 'Il cliente non ha deciso di spedire con Fermo!Point, quindi si torna alla pagina della spedizione', 'woocommerce' )
					  ),*/
					
					
					
					
					
				);
			
				
				 
	}
		 
				 
	public function calculate_shipping( $package ) {
		$rate = array(
			'id' => $this->id,
			'label' => $this->title,
			'cost' => $this->cost,
			'description' =>$this->description
		);
	
		// Register the rate
		$this->add_rate( $rate );
	}
				
				
				

				
	public function is_available( $package ) {
	//var_dump ($package );
	//var_dump ($this);
	//die ();
	$is_available = true;
			if ( $this->availability === 'specific' ) {
				$ship_to_countries = $this->countries;
			} else {
				$ship_to_countries = array_keys( WC()->countries->get_shipping_countries() );
			}
			if ( is_array( $ship_to_countries ) && ! in_array( $package['destination']['country'], $ship_to_countries ) ) {
				$is_available = false;
			}

	
		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', $is_available, $package );
	}	
				
		
				
				
			}
		}
	}
 
	add_action( 'woocommerce_shipping_init', 'fermopoint_shipping_method_init' );
 
 
 
 
	function add_FermoPoint_shipping_method( $methods ) {
		$methods[] = 'FermoPoint_Shipping_Method';
		return $methods;
	}
 
	add_filter( 'woocommerce_shipping_methods', 'add_FermoPoint_shipping_method' );
	

	
	function fermopoint_datafield($order){
		
		$ticket_fermopoint = get_post_meta( $order->id, '_shipping_ticketId-fermopoint', true );
		
		if ($ticket_fermopoint!=""){
			
			 $url =  get_post_meta( $order->id, '_shipping_urlticket-fermopoint', true );
			
   			 echo '<p><strong>'.__('Ticket Fermo!Point').':</strong> <a target="new" href="' .  $url . '">' . $ticket_fermopoint . '</a></p>';
		}
	}
	
	
	function fermopoint_order_status( $order_id, $old_status, $new_status ) {
		
		$order = new WC_Order($order_id);
		$ticket_fermopoint = get_post_meta( $order_id, '_shipping_ticketId-fermopoint', true );
		
		if ($ticket_fermopoint!=""){
		$url="";
			if ('processing' == $new_status || 'completed' == $new_status) {
				if (count($order->get_items()) > 0) {
				
					//Approval Url
					$url =  get_post_meta($order_id, '_shipping_approvalUrl-fermopoint', true );
				}
			} else if ('cancelled' == $new_status || 'failed' == $new_status  || 'refunded' == $new_status) {
				
					//Reject Url
					$url =  get_post_meta( $order_id, '_shipping_rejectUrl-fermopoint', true );
					
			}
				
				$ch = curl_init();
				$connect_timeout = 5; //sec
				//echo $url;
				//$request = $_POST["request"];
				// $url = $_POST["urlfermo"];
				
				$base_time_limit = (int) ini_get('max_execution_time');
				if ($base_time_limit < 0) {
					$base_time_limit = 0;
				}
				 $time_limit = $base_time_limit - $connect_timeout - 2;
				if ($time_limit <= 0) {
					$time_limit = 20; //default
				}
				$request_url="";
				$httpHeader = array(
					"Content-Type: application/json; charset=\"utf-8\"",
					"Content-Length: " . strlen ($request_url)
				);
				curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
				curl_setopt($ch, CURLOPT_TIMEOUT, $time_limit);
				curl_setopt($ch, CURLOPT_URL, $url);
			   
				// curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$request_url);
				$result = curl_exec($ch);
				$info = curl_getinfo($ch);
				if (!isset($info['http_code'])) {
					$info['http_code'] = '';
				}
				
				$curl_errno = curl_errno($ch);
				$curl_error = curl_error($ch);
		
				if (curl_errno($ch)) {
					$return= array(
						'http_code' => $info['http_code'],
						//'info' => $info,
						'status' => 'ERROR1',
						'errno' => $curl_errno,
						'error' => $curl_error,
						'result' => NULL
					);
				} else {
				   $ret = json_decode($result); 
				}
				//var_dump($result);
				//echo $url;
				//wp_die();
				 curl_close($ch);
		}
	

}
	
	
	
	
	
 
function add_order_email_fermopoint( $order, $sent_to_admin,$plain_text  ) {
  
 
    if ( ! $sent_to_admin ) {
  
  $ticket_fermopoint = get_post_meta( $order->id, '_shipping_ticketId-fermopoint', true );
		
		if ($ticket_fermopoint!=""){
			
			 $url =  get_post_meta( $order->id, '_shipping_urlticket-fermopoint', true );
			
   			 echo '<p><strong>'.__('Ticket Fermo!Point').':</strong> <a target="new" href="' .  $url . '">' . $ticket_fermopoint . '</a></p>';
		}
  
   }
  
  
  
  
}
	
	
function register_menuFermopoint() {
		add_submenu_page( 'woocommerce', 'Fermo!Point Ticket', 'Fermo!Point Ticket', 'manage_options', 'checkfermopoint_merchant', 'checkfermopoint_merchant_callback' ); 
	}	
	
function checkfermopoint_merchant_callback() {
		echo "<h1>Riepilogo ticket Fermo!Point</h1>"; 
		 
		$shippings_wc = new WC_Shipping ;
		$shippings = $shippings_wc->load_shipping_methods();
		$fermopoint = $shippings["Fermo!Point"];
		//echo json_encode($fermopoint->settings);
		$fermopoint_settings = $fermopoint->settings;
		$account_id = $fermopoint_settings["FERMOPOINT_ACCOUNT_ID"];
		$account_secret = $fermopoint_settings["FERMOPOINT_ACCOUNT_SECRET"];
		$sandbox =$fermopoint_settings["sandbox"];
		$url = ($sandbox=="yes")? "http://www.sandbox.fermopoint.it/api/v1.2/merchant": "http://api.fermopoint.it/api/v1.2/merchant";	
		 
		 
		 $ts = gmdate('Y-m-d\TH:i:s.u\Z');
		
		$auth_token = hash_hmac("sha256", (string) $ts, (string) $account_id . $account_secret, false);
		$request_url ='{
			"client_id": "' . $account_id  . '",
			"ts": "' . $ts . '",
			"auth_token": "' . $auth_token . '",
			"data":{}
		}';
	
		$request = json_encode(array(
                                    "client_id" => $account_id,
                                    "auth_token" =>$auth_token,
                                    "ts" => $ts,
                                    "data" => array()
                                ));
	
			$httpHeader = array(
            "Content-Type: application/json; charset=\"utf-8\"",
			"Accept: text/json"
        );
			
		$ch = curl_init();
		$connect_timeout = 5; //sec	
		$base_time_limit = (int) ini_get('max_execution_time');
		if ($base_time_limit < 0) {
			$base_time_limit = 0;
		}
		$time_limit = $base_time_limit - $connect_timeout - 2;
		if ($time_limit <= 0) {
			$time_limit = 20; //default
		}
		
		
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $time_limit);
        curl_setopt($ch, CURLOPT_URL, $url );
       
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request );
		
		curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
		
		
		$result = curl_exec($ch);
		$info = curl_getinfo($ch);
		if (!isset($info['http_code'])) {
		$info['http_code'] = '';
		}
		
		$curl_errno = curl_errno($ch);
		$curl_error = curl_error($ch);
		
		if (curl_errno($ch)) {
			$result= array(
				'http_code' => $info['http_code'],
				//'info' => $info,
				'status' => 'ERROR1',
				'errno' => $curl_errno,
				'error' => $curl_error,
				'result' => NULL
			);
		} else {
			$ret = json_decode($result); 
		
		}		
		
		//var_dump($result);
		//var_dump($ret);
		?>
        
        <h2>Crediti restati: <?php echo $ret->credits;?></h2>
		<h2>Ordini totali: <?php echo $ret->orders_count;?></h2>
        
        
        <table>
        
        	<?php 
			$orders = $ret->orders;
			if (count($orders)>0){
				foreach ($orders as $order){
				
					?>
						<tr>
							<?php 
								foreach ($order as $col){
									
									?><td><?php echo $col;?></td><?php
							
								}
						
							?>
						</tr>
					<?php
				
				}
			}
			?>
        
        </table>
        
		<?php
					
		curl_close($ch);
		
			
	}	
	
	
}



		
