<?php





// function nftb_after_install_or_update( $upgrader_object, $options ) {
//     // Controlla se l'operazione riguarda i plugin
//     if ( isset( $options['type'] ) && 'plugin' === $options['type'] ) {
//         // Nome del plugin da controllare
//         $plugin_slug = 'notification-for-telegram/index.php';

//         // Per aggiornamenti
//         if ( isset( $options['action'] ) && 'update' === $options['action'] ) {
//             if ( isset( $options['plugins'] ) && in_array( $plugin_slug, $options['plugins'], true ) ) {
//                 error_log('Notification for Telegram Plugin Updated: ' . $plugin_slug);
//                 nftb_optimize_nftb_plugin_database();
//             }
//         }

//         // Per installazioni
//         if ( isset( $options['action'] ) && 'install' === $options['action'] ) {
//             error_log('Notification for Telegram Plugin Installed: ' . $plugin_slug);
//             nftb_optimize_nftb_plugin_database();
//         }
//     }
// }

// add_action( 'upgrader_process_complete', 'nftb_after_install_or_update', 20, 2 );








function nftb_optimize_nftb_plugin_database() {
    global $wpdb;
  

    // Controlla se l'operazione è già stata eseguita
    if ( get_option( 'nftb_fix_1' ) === '1' ) {
        //error_log( 'Notification for Telegram - The operation nftb_new_order_id_for_notification_ set autoload to none has already been completed. No changes are necessary.' );
        return; // Termina l'esecuzione della funzione
    }

    $option_prefix = 'nftb_new_order_id_for_notification_';

    // Esegui la query per aggiornare il campo autoload a 'none'
    $rows_affected = $wpdb->query(
        $wpdb->prepare(
            "UPDATE {$wpdb->options}
             SET autoload = %s
             WHERE option_name LIKE %s",
            'off', // Valore per il campo autoload
            $option_prefix . '%' // Pattern per il nome delle opzioni
        )
    );

    
    if ( $rows_affected === false ) {
       
        error_log( 'Notification for Telegram - An error occurred while executing the plugin optimization query.' );
        
    } elseif ( $rows_affected === 0 ) {
        
        
        error_log( 'Notification for Telegram -  No options were updated. Check the prefix and try again.' );
    } else {
      
        error_log( 'Notification for Telegram - We have optimized the database nftb_new_order_id_for_notification_ to make the Notification for Telegram plugin faster. The update modified ' . $rows_affected . ' records.' );
      
        
    }
    update_option( 'nftb_fix_1', '1', true);
}




