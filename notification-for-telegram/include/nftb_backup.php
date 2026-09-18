<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// ============================================================
// EXPORT
// ============================================================
add_action( 'admin_post_nftb_export_settings', 'nftb_export_settings' );
function nftb_export_settings() {

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Insufficient permissions.' );
    }
    check_admin_referer( 'nftb_export_action', 'nftb_export_nonce' );

    $data = array(
        'telegram_notify_option_name'      => get_option( 'telegram_notify_option_name' ),
        'telegram_notify_option_name_tab2' => get_option( 'telegram_notify_option_name_tab2' ),
        'telegram_notify_option_name_tab3' => get_option( 'telegram_notify_option_name_tab3' ),
        'telegram_notify_option_name_tab5' => get_option( 'telegram_notify_option_name_tab5' ),
    );

    $site_name = sanitize_title( get_bloginfo( 'name' ) );
    $date      = date( 'Y-m-d' );
    $filename  = 'nftb-backup-' . $site_name . '-' . $date . '.json';

    header( 'Content-Type: application/json' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    header( 'Pragma: no-cache' );
    echo json_encode( $data, JSON_PRETTY_PRINT );
    exit;
}

// ============================================================
// IMPORT
// ============================================================
add_action( 'admin_post_nftb_import_settings', 'nftb_import_settings' );
function nftb_import_settings() {

    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( 'Insufficient permissions.' );
    }
    check_admin_referer( 'nftb_import_action', 'nftb_import_nonce' );

    // Controlla che il file sia presente
    if ( empty( $_FILES['nftb_import_file']['tmp_name'] ) ) {
        wp_redirect( add_query_arg( 'nftb_msg', 'no_file', admin_url( 'admin.php?page=telegram-notify' ) ) );
        exit;
    }

    // Valida estensione file
    $file_type = wp_check_filetype(
        $_FILES['nftb_import_file']['name'],
        array( 'json' => 'application/json' )
    );
    if ( $file_type['ext'] !== 'json' ) {
        wp_redirect( add_query_arg( 'nftb_msg', 'invalid', admin_url( 'admin.php?page=telegram-notify' ) ) );
        exit;
    }

    // Controlla dimensione file - max 1MB
    if ( $_FILES['nftb_import_file']['size'] > 1048576 ) {
        wp_redirect( add_query_arg( 'nftb_msg', 'invalid', admin_url( 'admin.php?page=telegram-notify' ) ) );
        exit;
    }

    // Legge e decodifica il JSON
    $json = file_get_contents( $_FILES['nftb_import_file']['tmp_name'] );
    $data = json_decode( $json, true );

    if ( ! is_array( $data ) ) {
        wp_redirect( add_query_arg( 'nftb_msg', 'invalid', admin_url( 'admin.php?page=telegram-notify' ) ) );
        exit;
    }

    // Solo le chiavi conosciute vengono importate
    $allowed_keys = array(
        'telegram_notify_option_name',
        'telegram_notify_option_name_tab2',
        'telegram_notify_option_name_tab3',
        'telegram_notify_option_name_tab5',
    );

    foreach ( $allowed_keys as $key ) {
        if ( isset( $data[$key] ) && is_array( $data[$key] ) ) {
            // Sanitizza ogni valore prima di salvare
            $clean = array_map( 'sanitize_text_field', $data[$key] );
            update_option( $key, $clean );
        }
    }

    wp_redirect( add_query_arg( 'nftb_msg', 'imported', admin_url( 'admin.php?page=telegram-notify' ) ) );
    exit;
}

// ============================================================
// NOTICE dopo import
// ============================================================
add_action( 'admin_notices', 'nftb_backup_admin_notice' );
function nftb_backup_admin_notice() {
    if ( ! isset( $_GET['nftb_msg'] ) ) return;
    $msg = sanitize_key( $_GET['nftb_msg'] );
    if ( $msg === 'imported' ) {
        echo '<div class="notice notice-success is-dismissible"><p><strong>Notification for Telegram:</strong> Settings imported successfully!</p></div>';
    } elseif ( $msg === 'invalid' ) {
        echo '<div class="notice notice-error is-dismissible"><p><strong>Notification for Telegram:</strong> Invalid file. Please upload a valid JSON backup under 1MB.</p></div>';
    } elseif ( $msg === 'no_file' ) {
        echo '<div class="notice notice-error is-dismissible"><p><strong>Notification for Telegram:</strong> No file selected.</p></div>';
    }
}