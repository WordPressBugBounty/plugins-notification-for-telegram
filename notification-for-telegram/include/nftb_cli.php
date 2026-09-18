<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( defined('WP_CLI') && WP_CLI ) {

    class NFTB_CLI_Command {

        /**
         * Send a Telegram message from the shell.
         *
         * ## OPTIONS
         *
         * <message>
         * : The message to send. Use "-" to read from stdin.
         *
         * [--chatid=<chatid>]
         * : Optional. Telegram chat ID. If omitted, uses the plugin global config.
         *
         * [--urlname=<urlname>]
         * : Optional. Label for an inline button link.
         *
         * [--urllink=<urllink>]
         * : Optional. URL for an inline button link (requires --urlname).
         *
         * ## EXAMPLES
         *
         *     wp telegram send "Hello from shell"
         *     wp telegram send "Hello" --chatid=123456789
         *     wp telegram send "$(ls -la /var/www/html)"
         *     df -h | wp telegram send -
         *     wp telegram send "Deploy complete" --urlname="Open site" --urllink="https://example.com"
         *
         * @when after_wp_load
         */
        public function send( $args, $assoc_args ) {

            if ( empty( $args[0] ) ) {
                WP_CLI::error( 'Please provide a message. Use "-" to read from stdin.' );
                return;
            }

            if ( $args[0] === '-' ) {
                $messaggio = stream_get_contents( STDIN );
                if ( empty( trim( $messaggio ) ) ) {
                    WP_CLI::error( 'No input received from stdin.' );
                    return;
                }
            } else {
                $messaggio = $args[0];
            }

            $chatid  = isset( $assoc_args['chatid'] )  ? $assoc_args['chatid']  : null;
            $urlname = isset( $assoc_args['urlname'] ) ? $assoc_args['urlname'] : null;
            $urllink = isset( $assoc_args['urllink'] ) ? $assoc_args['urllink'] : null;

            nftb_send_teleg_message( $messaggio, $urlname, $urllink, $chatid );
            WP_CLI::success( 'Message sent to Telegram.' );
        }


        /**
         * Test the Telegram connection using the plugin configuration.
         *
         * ## EXAMPLES
         *
         *     wp telegram test
         *
         * @when after_wp_load
         */
        public function test( $args, $assoc_args ) {

            $TelegramNotify = new nftb_TelegramNotify();
            $token   = $TelegramNotify->getValuefromconfig('token_0');
            $chatids = $TelegramNotify->getValuefromconfig('chatids_');

            if ( empty( $token ) || empty( $chatids ) ) {
                WP_CLI::error( 'Token or Chat ID not configured. Please check the plugin settings.' );
                return;
            }

            $bloginfo = get_bloginfo('name');
            nftb_send_teleg_message( '🚀 WP-CLI test from ' . $bloginfo . ' — connection is working!' );
            WP_CLI::success( 'Test message sent to Telegram.' );
        }


        /**
         * Show the current plugin configuration.
         *
         * ## EXAMPLES
         *
         *     wp telegram status
         *
         * @when after_wp_load
         */
        public function status( $args, $assoc_args ) {

            $TelegramNotify = new nftb_TelegramNotify();
            $token   = $TelegramNotify->getValuefromconfig('token_0');
            $chatids = $TelegramNotify->getValuefromconfig('chatids_');

            $masked_token = ( strlen($token) > 10 )
                ? substr($token, 0, 6) . '...' . substr($token, -4)
                : '(not set)';

            $chatids_display = ! empty($chatids) ? $chatids : '(not set)';

            WP_CLI::line( '' );
            WP_CLI::line( '🤖 Notification for Telegram — Status' );
            WP_CLI::line( '--------------------------------------' );
            WP_CLI::line( 'Site      : ' . get_bloginfo('name') . ' (' . get_bloginfo('url') . ')' );
            WP_CLI::line( 'Token     : ' . $masked_token );
            WP_CLI::line( 'Chat IDs  : ' . $chatids_display );
            WP_CLI::line( 'Plugin ver: ' . ( defined('NFTB_VERSION') ? NFTB_VERSION : 'n/a' ) );
            WP_CLI::line( '' );

            if ( empty($token) || empty($chatids) ) {
                WP_CLI::warning( 'Token or Chat ID is not configured.' );
            } else {
                WP_CLI::success( 'Configuration looks good. Run wp telegram test to verify.' );
            }
        }


        /**
         * Send a plugin update report to Telegram.
         *
         * ## EXAMPLES
         *
         *     wp telegram report:updates
         *
         *     # Schedule daily via system cron at 8am
         *     # 0 8 * * * cd /var/www/html && wp telegram report:updates --allow-root
         *
         * @when after_wp_load
         */
        public function report_updates( $args, $assoc_args ) {

            if ( ! function_exists('get_plugins') ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            require_once ABSPATH . 'wp-admin/includes/update.php';

            wp_update_plugins();
            $updates  = get_site_transient('update_plugins');
            $plugins  = get_plugins();
            $bloginfo = get_bloginfo('name');
            $list     = '';
            $count    = 0;

            foreach ( $plugins as $file => $plugin ) {
                if ( isset( $updates->response[$file] ) ) {
                    $count++;
                    $list .= "\r\n• " . $plugin['Name'] . ' → v' . $updates->response[$file]->new_version;
                }
            }

            if ( $count === 0 ) {
                $message = '✅ ' . $bloginfo . ': all plugins are up to date.';
            } else {
                $message = '🔔 ' . $bloginfo . ': ' . $count . ' plugin(s) need update:' . $list;
            }

            nftb_send_teleg_message( $message );
            WP_CLI::success( $count . ' plugin(s) need update. Message sent to Telegram.' );
        }


        /**
         * Send a WordPress core update report to Telegram.
         *
         * ## EXAMPLES
         *
         *     wp telegram report:core
         *
         * @when after_wp_load
         */
        public function report_core( $args, $assoc_args ) {

            global $wp_version;
            require_once ABSPATH . 'wp-admin/includes/update.php';

            wp_version_check();
            $cur_wp_version = preg_replace('/-.*$/', '', $wp_version);
            $core_updates   = (array) get_core_updates();
            $bloginfo       = get_bloginfo('name');

            if (
                ! isset( $core_updates[0]->response ) ||
                $core_updates[0]->response === 'latest' ||
                $core_updates[0]->response === 'development' ||
                version_compare( $core_updates[0]->current, $cur_wp_version, '=' )
            ) {
                $message = '✅ ' . $bloginfo . ': WordPress core is up to date (v' . $cur_wp_version . ').';
                WP_CLI::success( 'WordPress is up to date.' );
            } else {
                $message = '🔔 ' . $bloginfo . ': WordPress core update available → v' . $core_updates[0]->current . ' (current: v' . $cur_wp_version . ')';
                WP_CLI::warning( 'WordPress update available: ' . $core_updates[0]->current );
            }

            nftb_send_teleg_message( $message );
            WP_CLI::success( 'Report sent to Telegram.' );
        }


        /**
         * Show all available WP-CLI commands for this plugin.
         *
         * ## EXAMPLES
         *
         *     wp telegram help
         *
         * @when after_wp_load
         */
        public function help( $args, $assoc_args ) {

            WP_CLI::line( '' );
            WP_CLI::line( '🤖 Notification for Telegram — WP-CLI Commands' );
            WP_CLI::line( '===============================================' );
            WP_CLI::line( '' );
            WP_CLI::line( 'wp telegram send <message>         Send a message to Telegram' );
            WP_CLI::line( '    --chatid=<id>                  Optional: override chat ID' );
            WP_CLI::line( '    --urlname=<label>              Optional: inline button label' );
            WP_CLI::line( '    --urllink=<url>                Optional: inline button URL' );
            WP_CLI::line( '' );
            WP_CLI::line( 'wp telegram send -                 Read message from stdin (pipe)' );
            WP_CLI::line( '' );
            WP_CLI::line( 'wp telegram test                   Test Telegram connection' );
            WP_CLI::line( '' );
            WP_CLI::line( 'wp telegram status                 Show current plugin configuration' );
            WP_CLI::line( '' );
            WP_CLI::line( 'wp telegram report:updates         Send plugin update report to Telegram' );
            WP_CLI::line( '' );
            WP_CLI::line( 'wp telegram report:core            Send WordPress core update report to Telegram' );
            WP_CLI::line( '' );
            WP_CLI::line( '-----------------------------------------------' );
            WP_CLI::line( 'Examples:' );
            WP_CLI::line( '' );
            WP_CLI::line( '  wp telegram send "Backup complete"' );
            WP_CLI::line( '  wp telegram send "$(df -h)"' );
            WP_CLI::line( '  df -h | wp telegram send -' );
            WP_CLI::line( '  wp telegram send "Deploy done" --urlname="Open site" --urllink="https://example.com"' );
            WP_CLI::line( '' );
            WP_CLI::line( 'System cron examples (crontab -e):' );
            WP_CLI::line( '  0 8 * * * cd /var/www/html && wp telegram report:updates --allow-root' );
            WP_CLI::line( '  0 9 * * * cd /var/www/html && wp telegram report:core --allow-root' );
            WP_CLI::line( '' );
        }

    }

    WP_CLI::add_command( 'telegram', 'NFTB_CLI_Command' );
}