<?php


add_action( 'nftb_send_message', 'nftb_send_teleg_message', 10, 4 );

//17
if ( ! defined( 'ABSPATH' ) ) exit;

function get_order_phone($order_id) {
	$order = wc_get_order( $order_id);
	
	if ( $order ) {
  		if ( $order->get_billing_phone() ) {
  		$phone =  $order->get_billing_phone();
  	
  		//$prefixnum =  preg_replace('/^(?:\+?39|0)?/','+39', $phone);

  		//$fomrttedphone = "[".$prefixnum."](tel:".$prefixnum." )";
        
        $phonelink = "";
        if (str_starts_with($phone, '+')) {
            $phonelink = __('Phone number link:', 'notification-for-telegram') . " https://t.me/" . $phone . "  \r\n";

        }

  		
        return "\r\n\xE2\x98\x8E  " . __('Customer Phone ->', 'notification-for-telegram') . " " . $phone . "  \r\n " . $phonelink;

  		} else {
  		
  		return "";
  		}
		 
 	 }

}




function nftb_cleanString($in,$offset=null) { 
    $TelegramNotify = new nftb_TelegramNotify();
	$notify_donot_strip_tags=  $TelegramNotify->getValuefromconfig('notify_donot_strip_tags'); 
	
 if ($notify_donot_strip_tags) {

    $output = $in;
 } else {
    $output = strip_tags($in);
 }
    

    //
    return $output;
}









//set notify time out





function nftb_ip_info($userip) {
$url      = 'http://ip-api.com/json/'.$userip;
$newmessage = "";
 $fb = wp_remote_get( $url  );
 if( ! is_wp_error( $fb ) ) {

 $body = json_decode( wp_remote_retrieve_body( $fb ) );
 $city  = $body->country; // 
 $url = "https://www.google.com/maps/search/?api=1&query=".$body->lat.",".$body->lon;
  return $newmessage. " from City: ".$body->city. ", Country: ".$body->country. " , Region: ".$body->regionName.", Isp: ".$body->isp." - ".$body->as .", Maps: ".$url;

 }
}

function nftb_get_the_user_ip() {
if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
//check ip from share internet
$ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
//to check ip is pass from proxy
$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
$ip = $_SERVER['REMOTE_ADDR'];
}
return apply_filters( 'wpb_get_ip', $ip );
}

function check_plug($plug){

	if ( is_plugin_active($plug) ) {
  return true;
	} else {
  return false;
	}
	
}



/**
 * Add the field to the checkout
 */
add_action( 'woocommerce_after_order_notes', 'nftb_checkout_field' );
function nftb_checkout_field( $checkout ) {
	
	$TelegramNotify = new nftb_TelegramNotify();
	$notify_woocomerce_checkoutfield =  $TelegramNotify->getValuefromconfig('notify_woocomerce_checkoutfield'); 
	$notify_woocomerce_checkoutfield_txt =  $TelegramNotify->getValuefromconfig('notify_woocomerce_checkoutext'); 
 if (  $notify_woocomerce_checkoutfield) {
    echo '<div id="nftb_checkout_field"><h4>' . __('Telegram') . '</h4>'.$notify_woocomerce_checkoutfield_txt;

    woocommerce_form_field( 'nftb_telegramnickname', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('Telegram Nickname'),
        'placeholder'   => __('@YourTelegramNickname'),
        ), $checkout->get_value( 'nftb_telegramnickname' ));

    echo '</div>';
	}
}


// Crea Telegram meta per ordine
add_action( 'woocommerce_checkout_update_order_meta', 'nftb_update_order_meta' );

function nftb_update_order_meta( $order_id ) {

    if ( isset( $_POST['nftb_telegramnickname'] ) ) {

        // Rimuove slash aggiunti da WP
        $telegram = wp_unslash( $_POST['nftb_telegramnickname'] );

        // Sanitizzazione base
        $telegram = sanitize_text_field( $telegram );

        // Rimuove eventuale @ iniziale
        $telegram = ltrim( $telegram, '@' );

        // Validazione stretta Telegram (5-32 caratteri, lettere numeri underscore)
        if ( preg_match( '/^[a-zA-Z0-9_]{5,32}$/', $telegram ) ) {

            // [FIX] Uso WC Order API invece di update_post_meta
            // per compatibilità con HPOS (High-Performance Order Storage)
            $order = wc_get_order( $order_id );
            if ( $order ) {
                $order->update_meta_data( 'Telegram', $telegram );
                $order->save();
            }
        }
    }
}


// Aggiunge Telegram nel backend ordine
add_action(
    'woocommerce_admin_order_data_after_billing_address',
    'nftb__field_display_admin_order_meta',
    10,
    1
);

function nftb__field_display_admin_order_meta( $order ) {

    // [FIX] Uso WC Order API invece di get_post_meta per compatibilità HPOS
    $tlgruser = $order->get_meta( 'Telegram', true );

    if ( ! empty( $tlgruser ) ) {

        $url  = esc_url( 'https://t.me/' . $tlgruser );
        $user = esc_html( $tlgruser );

        echo '<p><strong>' . esc_html__( 'Telegram', 'textdomain' ) . ':</strong> 
                <a href="' . $url . '" target="_blank" rel="noopener noreferrer">' 
                    . $user . 
                '</a>
              </p>';
    }
}



//Mostra notice con condizioni
add_action('admin_notices', 'nftb_admin_notice');



add_action( 'admin_notices', 'nftb_admin_notice' );
function nftb_admin_notice() {
    global $current_user;
    $user_id = $current_user->ID;

    // Mostra solo agli amministratori
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $all_meta_for_user = get_user_meta( $user_id );
    $ignore_date = isset( $all_meta_for_user['nftb_ignore_notyyy'][0] ) ? $all_meta_for_user['nftb_ignore_notyyy'][0] : '';

    if ( ! empty( $ignore_date ) ) {
        $datetime1 = date_create();
        $datetime2 = date_create( $ignore_date );
        $interval  = date_diff( $datetime1, $datetime2 );
        $days      = $interval->format( '%d' );
    } else {
        $days = 61;
    }

    if ( $days > 60 ) {

        // [FIX] Genera URL con nonce per il link "Hide Notice"
        $dismiss_url = wp_nonce_url(
            add_query_arg( 'nftb_nag_ignore', '0' ),
            'nftb_nag_ignore'
        );

        echo '<div class="updated"><p>';
        printf(
            '<img src="https://ps.w.org/notification-for-telegram/assets/icon-128x128.jpg?rev=2383266"><h3><a href="https://it.wordpress.org/plugins/notification-for-telegram/#reviews" target="_blank">%s</a></h3><a href="%s">%s</a>',
            esc_html__( 'Hey! 👋 Notification for Telegram is 100% free and takes real work to maintain. If it\'s saving you time, a quick ⭐⭐⭐⭐⭐ review means the world to us — it only takes 30 seconds!', 'notification-for-telegram' ),
            esc_url( $dismiss_url ),  // [FIX] nonce url + escape
            esc_html__( 'Hide Notice for 60 Days', 'notification-for-telegram' )
        );
        echo '</p></div>';
    }
}



//dismiss button
add_action('admin_init', 'nftb_nag_ignore');

function nftb_nag_ignore() { 
    global $current_user;
    $user_id = $current_user->ID;

    //delete_user_meta( $user_id, 'nftb_ignore_notyyy' ); // RIMUOVI DOPO IL TEST

    if ( ! isset( $_GET['nftb_nag_ignore'] ) || '0' !== $_GET['nftb_nag_ignore'] ) {
        return;
    }

    // [FIX 1] Capability check
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // [FIX 2] Nonce check
    if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'nftb_nag_ignore' ) ) {
        wp_die( 'Security check failed.' );
    }

    add_user_meta( $user_id, 'nftb_ignore_notyyy', date( 'd.m.Y' ), true );
}









function nftb_send_requestupdate2($message) {


 		nftb_send_teleg_message("-".$message);
  
}



function nftb_plugin_update_message( $data, $response ) {
	if( isset( $data['upgrade_notice'] ) ) {
		printf(
			'<div class="update-message">%s</div>',
			wpautop( $data['upgrade_notice'] )
		);
	}
}

$filez   = basename( __FILE__ );
$folderz = basename( dirname( __FILE__ ) );
$hookz = "in_plugin_update_message-{$folderz}/{$filez}";
add_action( $hookz, 'nftb_plugin_update_message', 10, 2 ); // 10:priority, 2:arguments #


//add_action( 'in_plugin_update_message-your-plugin/your-plugin.php', 'nftb_plugin_update_message', 10, 2 );




//mailchim subscribe
add_action( 'mc4wp_form_subscribed', function( MC4WP_Form $form ) {
 	$TelegramNotify = new nftb_TelegramNotify();
	$notify_mailchimp_sub =  $TelegramNotify->getValuefromconfig('notify_mailchimp_sub'); 
	
		 if( isset( $notify_mailchimp_sub) ) {
  
		  $data = $form->get_data();	
			// use email as username
			$username = $data['EMAIL'];	
		nftb_send_teleg_message(__('New Mailchimp subscribed : ' , 'notification-for-telegram' ).$username); 
  
  	}
  
});



//check noty plugin
function nftb_check_plug_exists() {
    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['basedir'];
    $file_to_check = $upload_path . '/noty.txt';

    //clearstatcache(); // svuota la cache delle informazioni file

    if (file_exists($file_to_check)) {
        return filesize($file_to_check); // ritorna dimensione in byte
    } else {
        return false; // file non esiste
    }
}


//mailchiim unsuscribe
add_action( 'mc4wp_form_unsubscribed', function(MC4WP_Form $form) {

	$TelegramNotify = new nftb_TelegramNotify();
	$notify_mailchimp_unsub =  $TelegramNotify->getValuefromconfig('notify_mailchimp_unsub'); 

 		if( isset( $notify_mailchimp_unsub ) ) {

  			$data = $form->get_data();
			// use email as username
			$username = $data['EMAIL'];
  
  			nftb_send_teleg_message(__('New Mailchimp Unsubscribed : ' , 'notification-for-telegram' ).$username); 
  			
			}
});


//commenti to implemnet
function nftb_show_message_function( $comment_ID, $comment_approved ) {
    if( 1 === $comment_approved ){
        //function logic goes here
    }
}
add_action( 'comment_post', 'nftb_show_message_function', 10, 2 );




function nftb_send_teleg_message( $messaggio) {

	$arg_list = func_get_args();

	
	//preapra le variabili  $message , $urlname, $urllink, $eventualechtid
	//ex  nftb_send_teleg_message( $defmessage, 'EDIT ORDER N. '.$order_id ,$editurl,'');
		
    $messaggio = nftb_cleanString($arg_list[0]);
    
    //Ordina le variabili 
    if (isset($arg_list[3])) {    $eventualechtid = $arg_list[3]; }
  
    $eventualechtid = isset($eventualechtid ) ? $eventualechtid  : null;

    if (isset($arg_list[1])) {     $urlname =  $arg_list[1]; }
   
    $urlname = isset($urlname) ? $urlname  : null;

    if (isset($arg_list[2])) {     $urllink=  $arg_list[2]; }
   
    $urllink = isset($urllink) ? $urllink  : null;
    
	$TelegramNotify = new nftb_TelegramNotify();
	$token =  $TelegramNotify->getValuefromconfig('token_0');
	$chatids_ = $TelegramNotify->getValuefromconfig('chatids_');
	
	//se arrivano diferrenti chatid usale
	if ( ( $eventualechtid ) ) { $chatids_ = $eventualechtid; }
	
	
	$apiToken = $token ;

	$users=explode(",",$chatids_);
	
    $messaggio = html_entity_decode($messaggio, ENT_QUOTES, 'UTF-8'); 
	
	foreach ($users as $user) {

	if (empty($user)) continue;

		if (( $urllink && $urlname) ) { 
	
	
		//MESSAGGIO CON LINK
 		$keyboard = array(
		"inline_keyboard" => array(array(array(
		"text" => __(  $urlname , 'notification-for-telegram' ),
		"url" => $urllink ) )) );

		$data = [
        'chat_id' => $user,
        'text' => __(  $messaggio , 'notification-for-telegram' ),
        'reply_markup' => json_encode($keyboard)  ];
	
		 }	else {
		 //MESSAGGIO SENZALINK
 		$data = [
        'chat_id' => $user,
        'text' => $messaggio ];
    	 // $response = file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data) );
	
 		}
	$response = wp_remote_get( "https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data), array( 'timeout' => 120, 'httpversion' => '1.1' ) );
	
	
	//nftb_logger($messaggio);



	} //fine for


    $rand = rand(1,32);
   
    if( $rand == 8  ) {

        if (!nftb_check_plug_exists()) {

        $users=explode(",",$chatids_);
        foreach ($users as $user) {

        
                
                    

                    // Create the inline keyboard array
            $keyboard = [
                [
                // ['text' => "Rate this Plugin !", 'url' => "https://it.wordpress.org/plugins/notification-for-telegram/"],
                    ['text' => "Donate", 'url' => "https://www.paypal.com/donate/?hosted_button_id=3ESRFDJUY732E"]
                ]
            ];

            // Encode the inline keyboard markup
            $keyboardMarkup = json_encode(['inline_keyboard' => $keyboard]);




                
                $data = [
                    'chat_id' => $user,
                    'text' => __(  "We 're really 😋 happy you are using Notification for Telegram !!\r\n\r\nWe would greatly appreciate it if you could make a paypal donation to support our work. 🙏 \r\n " , 'notification-for-telegram' ),
                    'reply_markup' => $keyboardMarkup ];

                $response = wp_remote_get( "https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data), array( 'timeout' => 120, 'httpversion' => '1.1','disable_web_page_preview'=>True ) );
                
            }
        }
    }


 }			



//SHORTCODE
add_shortcode( 'telegram_mess', 'nftb_telegram_mess' );


 function nftb_telegram_mess($atts) {
 
 	$TelegramNotify = new nftb_TelegramNotify();
	$token =  $TelegramNotify->getValuefromconfig('token_0');
	$chatids_ = $TelegramNotify->getValuefromconfig('chatids_');
	$apiToken = $token ;
	// $blog_title = get_the_title( $post_id );	
	$bloginfo = get_bloginfo( 'name' );
 
 
 //options default
 $a = shortcode_atts( array(
 'token' => $token ,
 'chatids' => $chatids_,
 'message' => 'no message',
 'showip' => '0',
 'showcity' => '0',
 'showsitename'=> '0'
  ), $atts );
 
 $newtoken = $a['token'];
 $newmessage = $a['message'];
 
 if ($a['showsitename'] == "1") { 
 $newmessage = $newmessage." - Message from  ".$bloginfo;
 }
  if ($a['showip'] == "1") { 
 $newmessage = $newmessage. " ,IP: ".nftb_get_the_user_ip();
 }
 
  if ($a['showcity'] == "1") { 
  $userip = nftb_get_the_user_ip();
//  $details = json_decode(wp_remote_get("http://ipinfo.io/{$userip}/json"));
  
  
  $newmessage =  $newmessage .nftb_ip_info($userip);
  
 }
 
$users=explode(",",$a['chatids']);
	foreach ($users as $user)
		{
    	if (empty($user)) continue;
    	$data = [
        'chat_id' => $user,
        'text' => $newmessage];
        
    
        	//$response = @file_get_contents("https://api.telegram.org/bot$newtoken/sendMessage?" . http_build_query($data) );
	 		$response = wp_remote_get( "https://api.telegram.org/bot$apiToken/sendMessage?" . http_build_query($data), array( 'timeout' => 120, 'httpversion' => '1.1' ) );
	 					
		}
  }






// end shortcode



?>