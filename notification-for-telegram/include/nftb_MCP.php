<?php
/**
 * MCP / Abilities API Integration
 * 
 * Richiede (opzionali, nessun errore se assenti):
 *  - Plugin "Abilities API" (WordPress/abilities-api)
 *  - Plugin "MCP Adapter"   (WordPress/mcp-adapter)
 *
 * Endpoint risultante:
 *  https://tuosito.com/wp-json/nftb-telegram/mcp
*/

// ════════════════════════════════════════════════════
// BLOCCO 0 — Registra la categoria
// ════════════════════════════════════════════════════

add_action( 'wp_abilities_api_categories_init', 'nftb_register_ability_category' );

function nftb_register_ability_category() {
    wp_register_ability_category(
        'notifications',
        array(
            'label'       => 'Notifications',
            'description' => 'Abilities for sending notifications.',
        )
    );
}


// ════════════════════════════════════════════════════
// BLOCCO 1 — Registra l'Ability
// ════════════════════════════════════════════════════

add_action( 'wp_abilities_api_init', 'nftb_register_mcp_ability' );

function nftb_register_mcp_ability() {

    wp_register_ability(
        'notification-for-telegram/send-message',
        array(
            'label'       => 'Send Telegram Message',
            'description' => 'Sends a text message to the configured Telegram chat(s). Optionally adds an inline button with label and URL.',
            'category'    => 'notifications',

            'input_schema' => array(
                'type'       => 'object',
                'properties' => array(
                    'message' => array(
                        'type'        => 'string',
                        'description' => 'The message text to send.',
                    ),
                    'button_label' => array(
                        'type'        => 'string',
                        'description' => 'Optional: text of the inline button.',
                    ),
                    'button_url' => array(
                        'type'        => 'string',
                        'description' => 'Optional: URL for the inline button.',
                    ),
                    'chat_id' => array(
                        'type'        => 'string',
                        'description' => 'Optional: override the default Telegram chat ID.',
                    ),
                ),
                'required' => array( 'message' ),
            ),

            'execute_callback'    => 'nftb_mcp_do_send_telegram',

            'permission_callback' => function() {
                return current_user_can( 'edit_posts' );
            },

            'meta' => array(
                'mcp' => array( 'public' => true ),
            ),
        )
    );
}


// ════════════════════════════════════════════════════
// BLOCCO 2 — Callback (non tocca nftb_send_teleg_message)
// ════════════════════════════════════════════════════

function nftb_mcp_do_send_telegram( $input ) {
    $message = $input['message']      ?? '';
    $label   = $input['button_label'] ?? null;
    $url     = $input['button_url']   ?? null;
    $chat_id = $input['chat_id']      ?? null;

    nftb_send_teleg_message( $message, $label, $url, $chat_id );

    return array( 'success' => true );
}


// ════════════════════════════════════════════════════
// BLOCCO 3 — Server MCP (silenzioso se plugin assenti)
// ════════════════════════════════════════════════════

add_action( 'plugins_loaded', function() {

    // Esce senza errori se uno dei due plugin non è installato
    if ( ! class_exists( 'WP\MCP\Core\McpAdapter' ) ) {
        return;
    }
    if ( ! function_exists( 'wp_register_ability' ) ) {
        return;
    }

    add_action( 'mcp_adapter_init', function( $adapter ) {
        $adapter->create_server(
            'nftb-telegram-server',                       // id univoco
            'nftb-telegram',                              // namespace → compare nell'URL
            'mcp',                                        // route     → compare nell'URL
            'Telegram Notifier',                          // nome leggibile
            'Send Telegram notifications from WordPress', // descrizione
            '1.0.0',                                      // versione
            [ \WP\MCP\Transport\HttpTransport::class ],
            \WP\MCP\Infrastructure\ErrorHandling\ErrorLogMcpErrorHandler::class,
            \WP\MCP\Infrastructure\Observability\NullMcpObservabilityHandler::class,
            [ 'notification-for-telegram/send-message' ], // ability esposta
            [], // resources
            []  // prompts
        );
    } );

} );