<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://www.digitalissimoweb.it
 * @since      1.0.0
 *
 * @package    Fermopoint
 * @subpackage Fermopoint/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Fermopoint
 * @subpackage Fermopoint/public
 * @author     Digitalissimo <developer@digitalissimoweb.it>
 */
class Fermopoint_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		//add_action( 'woocommerce_after_add_to_cart_button', array($this, 'fermopoint_page'));
		add_action('wp_ajax_nopriv_callfermopointapi', array($this, 'callfermopointapi'));
		add_action('wp_ajax_callfermopointapi', array($this, 'callfermopointapi'));
		add_action('wp_ajax_resetfermopoint', array($this, 'resetfermopoint'));
		add_action('wp_ajax_nopriv_resetfermopoint', array($this, 'resetfermopoint'));
		
		add_action('wp_ajax_currentpage', array($this, 'currentpage'));
		add_action('wp_ajax_nopriv_currentpage', array($this, 'currentpage'));
		
		//add_action('woocommerce_after_order_notes', array($this, 'checkfermopoint_book'));
		add_action('woocommerce_checkout_fields', array($this, 'checkfermopoint_book'));
		add_action('woocommerce_checkout_update_order_meta', array($this, 'checkfermopoint_book_fieldupdate'), 10, 2  );
		
		//add_field("woocommerce_default_address_fields", 'custom_override_default_address_fields' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fermopoint-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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
		echo '<script type="text/javascript">
		var ajaxurl = "' .  admin_url( 'admin-ajax.php', 'relative' ) . '";
		var ajaxsec = "' . wp_create_nonce('creajsbarcode') . '";
		</script>';
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fermopoint-public.js', array( 'jquery' ), $this->version, false );

	}
	

	
	public function callfermopointapi() {
		header('Content-Type: application/json');
		//check_ajax_referer('creajsbarcode','security');	
		$request = $this->getfieldfermopoint();
	
		$url = $request["url"] . "/Init";
		  
		$request_url ="  {
       'ClientId': '" .$request["id"] . "',
       'ClientSecret':'" .  $request["secret"] . "', 
       'Links': {
         'CancelUrl': '" . WC()->cart->get_cart_url() . "',
         'ReturnUrl': '" . WC()->cart->get_checkout_url() . "'
       }}";



		
		//$request= http_build_query($request_url) . "\n";
		
		//$request= json_encode($request_url);
		
		$ch = curl_init();
        $connect_timeout = 5; //sec
		
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
        $httpHeader = array(
            "Content-Type: application/json; charset=\"utf-8\"",
			"Content-Length: " . strlen ($request_url)
        );
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $time_limit);
        curl_setopt($ch, CURLOPT_URL, $url);
       
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
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
           curl_close($ch);
		   
         	$ret = json_decode($result);
            if (!is_array($ret)) {
                $ret = $result;
            }
            $return=  array(
                'http_code' => $info['http_code'],
                //'info' => $info,
                'status' => ((empty($info['http_code']) || $info['http_code'] == 200) ? 'OK' : 'ERROR'),
                'errno' => $curl_errno,
                'error' => $curl_error,
                'result' => $ret
            );
        	if (($ret->ResultCode)<"299"){
				wc_setcookie( 'fermopoint_session', $ret);
				wc_setcookie( 'fermopoint_invio', '1');
			}
			else 
				wc_setcookie( 'fermopoint_session', $ret->ResultCode);
		}

		
		echo json_encode($return);
		wp_die();
    }
	
	
	public function resetfermopoint(){
		
		wc_setcookie( 'fermopoint_session', "", strtotime('-1 day'));
		wc_setcookie( 'fermopoint_invio', "", strtotime('-1 day'));
		
		
	
	}
	
	public function getfieldfermopoint(){
			//header('Content-Type: application/json');
			//check_ajax_referer('creajsbarcode','security');
	
			$shippings_wc = new WC_Shipping ;
			$shippings = $shippings_wc->load_shipping_methods();
			$fermopoint = $shippings["Fermo!Point"];
			//echo json_encode($fermopoint->settings);
			$fermopoint_settings = $fermopoint->settings;
			$account_id = $fermopoint_settings["FERMOPOINT_ACCOUNT_ID"];
			$account_secret = $fermopoint_settings["FERMOPOINT_ACCOUNT_SECRET"];
			$sandbox =$fermopoint_settings["sandbox"];
			$url = ($sandbox=="yes")? "http://www.sandbox.fermopoint.it/RedirectApi": "http://www.fermopoint.it/RedirectApi";
			
			
			//echo json_encode($shippings_wc );
			return array("id"=>html_entity_decode($account_id) , "secret"=>html_entity_decode($account_secret), "url"=>$url,"sandbox"=>$sandbox);
			
	}
	
	public function checkfermopoint_book($fields){
		
		//var_dump($fermopoint_session );
		if (isset($_COOKIE['fermopoint_session'])){
				$fermopoint_session = stripslashes($_COOKIE['fermopoint_session']);
			$fermopoint_session = json_decode($fermopoint_session);
				//check_ajax_referer('creajsbarcode','security');	
				
			
				$url = $fermopoint_session->Links->GetBookingUrl;
				  
				$ch = curl_init();
				$connect_timeout = 5; //sec
				
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
				$httpHeader = array(
					"Content-Type: application/json; charset=\"utf-8\"",
					
				);
				curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
				curl_setopt($ch, CURLOPT_TIMEOUT, $time_limit);
				curl_setopt($ch, CURLOPT_URL, $url);
			   
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		
		
				$result = curl_exec($ch);
			
				
				if (!curl_errno($ch)) {
					
					$ret = json_decode($result);
				
					
					//$fields['order']['order_comments']
					add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true');
					unset($fields['shipping']['shipping_first_name']);
					unset($fields['shipping']['shipping_last_name']);
					unset($fields['shipping']['shipping_company']);
					unset($fields['shipping']['shipping_address_1']);
					unset($fields['shipping']['shipping_address_2']);
					unset($fields['shipping']['shipping_city']);
					unset($fields['shipping']['shipping_postcode']);
					unset($fields['shipping']['shipping_country']);
					unset($fields['shipping']['shipping_state']);
					
					
					
					$messaggioFermoPoint =  $ret->Nickname . " - " . $ret->PointName  . " - Fermo!Point";
					
					$messaggioFermoPoint .= "\n" . $ret->Address->Street . " " . $ret->Address->Extended . "  " . $ret->Address->PostalCode . " "  . $ret->Address->Locality . $ret->Address->City;
					
					
					
					$fields["shipping"]["fermopoint_input_shipping"] =
					array(
						'type' =>"textarea",
						'label'     => "Hai scelto il ritiro presso il tuo <b>Fermo!Point</b>, la spedizione verr&agrave; recapitata presso:",
						'default' =>$messaggioFermoPoint ,
						'class'     => array('fermopoint_input'),
						'readonly' => 'readonly'
					 );
					
					
				}
				curl_close($ch);
				
				
			}		
			
			
			
			return $fields;
	
	}
	
	
	
	
	public function checkfermopoint_book_fieldupdate( $order_id, $posted ) {
		  
	
		if (isset($_COOKIE['fermopoint_session'])){
			
				$fermopoint_session = stripslashes($_COOKIE['fermopoint_session']);
				$fermopoint_session = json_decode($fermopoint_session);
				$url = $fermopoint_session->Links->GetBookingUrl;
				 
				$url_approval = $fermopoint_session->Links->ApprovalUrl;
				$url_reject = $fermopoint_session->Links->RejectionUrl;
				  
				$ch = curl_init();
				$connect_timeout = 5; //sec
				
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
				$httpHeader = array(
					"Content-Type: application/json; charset=\"utf-8\"",
					
				);
				curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
				//curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeader);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connect_timeout);
				curl_setopt($ch, CURLOPT_TIMEOUT, $time_limit);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		
		
				$result = curl_exec($ch);
				
				
				if (!curl_errno($ch)) {
					
					$ret = json_decode($result);
				
					update_post_meta( $order_id, '_shipping_first_name',  $ret->Nickname);
					update_post_meta( $order_id, '_shipping_last_name',  " - "  . $ret->PointName . " - Fermo!Point");
					
					update_post_meta( $order_id, '_shipping_company',  "");
					
					update_post_meta( $order_id, '_shipping_address_1',  $ret->Address->Street);
					update_post_meta( $order_id, '_shipping_address_2', $ret->Address->Extended);
					update_post_meta( $order_id, '_shipping_postcode', $ret->Address->PostalCode);
					update_post_meta( $order_id, '_shipping_city', $ret->Address->Locality . " " . $ret->Address->City);
					update_post_meta( $order_id, '_shipping_state', $ret->Address->District);
					update_post_meta( $order_id, '_shipping_postcode', $ret->Address->PostalCode);
					update_post_meta( $order_id, '_shipping_country',"IT");
					
					
					//var_dump($ret);
					update_post_meta( $order_id, '_shipping_ticketId-fermopoint',  $ret->TicketId);
					update_post_meta( $order_id, '_shipping_urlticket-fermopoint', $ret->BookingUrl );
					update_post_meta( $order_id, '_shipping_statebook-fermopoint', $ret->State );
					update_post_meta( $order_id, '_shipping_statenote-fermopoint', $ret->Notes );
					update_post_meta( $order_id, '_shipping_approvalUrl-fermopoint',$url_approval );
					update_post_meta( $order_id, '_shipping_rejectUrl-fermopoint', $url_reject );
					
					
				}
				curl_close($ch);
				
				wc_setcookie( 'fermopoint_session', "", strtotime('-1 day'));
				wc_setcookie( 'fermopoint_invio', "", strtotime('-1 day'));
			}		
			
			
			
	}






}



