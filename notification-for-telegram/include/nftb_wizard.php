<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_footer', 'nftb_wizard_popup' );
function nftb_wizard_popup() {

    // Mostra solo nella pagina del plugin
    if ( ! isset($_GET['page']) || $_GET['page'] !== 'telegram-notify' ) return;

    // Mostra solo se il token è vuoto
    $options = get_option('telegram_notify_option_name');
    $token   = isset($options['token_0']) ? $options['token_0'] : '';
    if ( ! empty($token) ) return;

    $nonce = wp_create_nonce('nftb_wizard_save');
    ?>
    <style>
        #nftb-wizard-overlay {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #nftb-wizard-box {
            background: #fff;
            border-radius: 8px;
            width: 540px;
            max-width: 95%;
            padding: 32px;
            position: relative;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }
        #nftb-wizard-close {
            position: absolute;
            top: 12px; right: 16px;
            font-size: 22px;
            cursor: pointer;
            color: #999;
            background: none;
            border: none;
            line-height: 1;
        }
        #nftb-wizard-close:hover { color: #333; }
        .nftb-wizard-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #1d2327;
        }
        .nftb-wizard-subtitle {
            color: #666;
            margin-bottom: 24px;
            font-size: 13px;
        }
        .nftb-progress {
            display: flex;
            gap: 6px;
            margin-bottom: 28px;
        }
        .nftb-progress-step {
            flex: 1;
            height: 4px;
            background: #e0e0e0;
            border-radius: 2px;
            transition: background 0.3s;
        }
        .nftb-progress-step.active { background: #2196F3; }
        .nftb-step { display: none; }
        .nftb-step.active { display: block; }
        .nftb-step h3 {
            font-size: 15px;
            margin-bottom: 8px;
            color: #1d2327;
        }
        .nftb-step p {
            color: #555;
            font-size: 13px;
            margin-bottom: 14px;
            line-height: 1.6;
        }
        .nftb-step input[type="text"] {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
            margin-bottom: 12px;
            box-sizing: border-box;
        }
        .nftb-step input[type="text"]:focus {
            border-color: #2196F3;
            outline: none;
            box-shadow: 0 0 0 2px rgba(33,150,243,0.15);
        }
        .nftb-wizard-actions {
            display: flex;
            gap: 10px;
            margin-top: 16px;
            align-items: center;
        }
        .nftb-btn {
            padding: 10px 22px;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            border: none;
            transition: 0.2s;
        }
        .nftb-btn-primary {
            background: #2196F3;
            color: #fff;
        }
        .nftb-btn-primary:hover { background: #1976D2; }
        .nftb-btn-secondary {
            background: #f0f0f0;
            color: #333;
        }
        .nftb-btn-secondary:hover { background: #e0e0e0; }
        .nftb-feedback {
            font-size: 12px;
            margin-top: 8px;
            min-height: 18px;
        }
        .nftb-feedback.ok  { color: #4CAF50; }
        .nftb-feedback.err { color: #f44336; }
        .nftb-step-link {
            color: #2196F3;
            text-decoration: none;
            font-weight: 500;
        }
        .nftb-step-link:hover { text-decoration: underline; }
        #nftb-wizard-success {
            text-align: center;
            padding: 16px 0;
        }
        #nftb-wizard-success .nftb-success-icon {
            font-size: 48px;
            display: block;
            margin-bottom: 12px;
        }
    </style>

    <div id="nftb-wizard-overlay">
        <div id="nftb-wizard-box">

            <button id="nftb-wizard-close" title="<?php _e('Close', 'notification-for-telegram'); ?>">✕</button>

            <div class="nftb-wizard-title">🤖 <?php _e('Welcome to Notification for Telegram!', 'notification-for-telegram'); ?></div>
            <div class="nftb-wizard-subtitle"><?php _e('Let\'s set up your Telegram bot in 3 quick steps.', 'notification-for-telegram'); ?></div>

            <!-- Progress bar -->
            <div class="nftb-progress">
                <div class="nftb-progress-step active" id="nftb-prog-1"></div>
                <div class="nftb-progress-step" id="nftb-prog-2"></div>
                <div class="nftb-progress-step" id="nftb-prog-3"></div>
            </div>

            <!-- STEP 1 — Token -->
            <div class="nftb-step active" id="nftb-step-1">
                <h3>Step 1 — <?php _e('Create your Telegram Bot', 'notification-for-telegram'); ?></h3>
                <p>
                    <?php _e('Open Telegram and start a chat with', 'notification-for-telegram'); ?>
                    <a href="https://t.me/BotFather" target="_blank" class="nftb-step-link">@BotFather</a>.
                    <?php _e('Send the command <strong>/newbot</strong>, follow the instructions and copy the token you receive.', 'notification-for-telegram'); ?>
                </p>
                <input type="text" id="nftb-token-input" placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
                <div class="nftb-feedback" id="nftb-token-feedback"></div>
                <div class="nftb-wizard-actions">
                    <button class="nftb-btn nftb-btn-primary" id="nftb-verify-token"><?php _e('Verify Token', 'notification-for-telegram'); ?></button>
                </div>
            </div>

            <!-- STEP 2 — Chat ID -->
            <div class="nftb-step" id="nftb-step-2">
                <h3>Step 2 — <?php _e('Get your Chat ID', 'notification-for-telegram'); ?></h3>
                <p>
                    <?php _e('Open Telegram and send any message to', 'notification-for-telegram'); ?>
                    <a href="https://t.me/chatIDrobot" target="_blank" class="nftb-step-link">@chatIDrobot</a>
                    <?php _e('— it will reply with your Chat ID. Paste it below.', 'notification-for-telegram'); ?>
                </p>
                <input type="text" id="nftb-chatid-input" placeholder="123456789">
                <div class="nftb-feedback" id="nftb-chatid-feedback"></div>
                <div class="nftb-wizard-actions">

                    <button class="nftb-btn nftb-btn-primary" id="nftb-save-chatid"><?php _e('Save & Continue', 'notification-for-telegram'); ?></button>
                </div>
            </div>

            <!-- STEP 3 — Test -->
            <div class="nftb-step" id="nftb-step-3">
                <h3>Step 3 — <?php _e('Test the connection', 'notification-for-telegram'); ?></h3>
                <p><?php _e('Everything is configured! Send a test message to make sure everything is working correctly.', 'notification-for-telegram'); ?></p>
                <div class="nftb-feedback" id="nftb-test-feedback"></div>
                <div class="nftb-wizard-actions">
                    <button class="nftb-btn nftb-btn-secondary" id="nftb-send-test"><?php _e('Send test message', 'notification-for-telegram'); ?></button>
                    <button class="nftb-btn nftb-btn-primary" id="nftb-wizard-finish"><?php _e('Finish', 'notification-for-telegram'); ?></button>
                </div>
            </div>

            <!-- SUCCESS -->
            <div id="nftb-wizard-success" style="display:none;">
                <span class="nftb-success-icon">🎉</span>
                <div class="nftb-wizard-title"><?php _e('All done!', 'notification-for-telegram'); ?></div>
                <p class="nftb-wizard-subtitle"><?php _e('Your bot is connected and ready. You can now configure notifications from the settings page.', 'notification-for-telegram'); ?></p>
                <button class="nftb-btn nftb-btn-primary" id="nftb-wizard-close-final"><?php _e('Go to settings', 'notification-for-telegram'); ?></button>
            </div>

        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {

        // Chiudi overlay
     $('#nftb-wizard-close').on('click', function() {
            $('#nftb-wizard-overlay').fadeOut(200);
        });

        $('#nftb-wizard-close-final').on('click', function() {
            location.reload();
        });



        // Vai allo step N
        function goToStep(n) {
            $('.nftb-step').removeClass('active');
            $('#nftb-step-' + n).addClass('active');
            $('.nftb-progress-step').removeClass('active');
            for (var i = 1; i <= n; i++) {
                $('#nftb-prog-' + i).addClass('active');
            }
        }

        // STEP 1 — Verifica token server-side
        $('#nftb-verify-token').on('click', function() {
            var token = $('#nftb-token-input').val().trim();
            if (!token) {
                $('#nftb-token-feedback').removeClass('ok').addClass('err').text('<?php _e('Please enter a token.', 'notification-for-telegram'); ?>');
                return;
            }
            $('#nftb-token-feedback').removeClass('ok err').text('<?php _e('Verifying...', 'notification-for-telegram'); ?>');

            $.post(ajaxurl, {
                action: 'nftb_wizard_verify_token',
                token:  token,
                nonce:  '<?php echo esc_js($nonce); ?>'
            }, function(response) {
                if (response.success) {
                    $('#nftb-token-feedback').removeClass('err').addClass('ok')
                        .text('✅ <?php _e('Bot verified:', 'notification-for-telegram'); ?> @' + response.data.username);
                    setTimeout(function() { goToStep(2); }, 800);
                } else {
                    $('#nftb-token-feedback').removeClass('ok').addClass('err')
                        .text('<?php _e('Invalid token. Please check and try again.', 'notification-for-telegram'); ?>');
                }
            });
        });

        // STEP 2 — Salva chatid
        $('#nftb-save-chatid').on('click', function() {
            var chatid = $('#nftb-chatid-input').val().trim();
            if (!chatid) {
                $('#nftb-chatid-feedback').removeClass('ok').addClass('err')
                    .text('<?php _e('Please enter a Chat ID.', 'notification-for-telegram'); ?>');
                return;
            }
            $.post(ajaxurl, {
                action: 'nftb_wizard_save_chatid',
                chatid: chatid,
                nonce:  '<?php echo esc_js($nonce); ?>'
            }, function(response) {
                if (response.success) {
                    goToStep(3);
                }
            });
        });

        // STEP 3 — Test
        $('#nftb-send-test').on('click', function() {
            $('#nftb-test-feedback').removeClass('ok err').text('<?php _e('Sending...', 'notification-for-telegram'); ?>');
            $.post(ajaxurl, {
                action: 'nftb_wizard_test',
                nonce:  '<?php echo esc_js($nonce); ?>'
            }, function(response) {
                if (response.success) {
                    $('#nftb-test-feedback').removeClass('err').addClass('ok')
                        .text('✅ <?php _e('Message sent! Check your Telegram.', 'notification-for-telegram'); ?>');
                } else {
                    $('#nftb-test-feedback').removeClass('ok').addClass('err')
                        .text('❌ <?php _e('Something went wrong. Check your token and Chat ID.', 'notification-for-telegram'); ?>');
                }
            });
        });

        // STEP 3 — Fine
        $('#nftb-wizard-finish').on('click', function() {
            $('.nftb-step, .nftb-progress, .nftb-wizard-title, .nftb-wizard-subtitle').hide();
            $('#nftb-wizard-success').show();

            setTimeout(function() {
                location.reload();
            }, 2000);

            
        });

    });
    </script>
    <?php
}

// ============================================================
// AJAX — Verifica token server-side
// ============================================================
add_action('wp_ajax_nftb_wizard_verify_token', 'nftb_wizard_verify_token');
function nftb_wizard_verify_token() {
    check_ajax_referer('nftb_wizard_save', 'nonce');
    if ( ! current_user_can('manage_options') ) wp_die();

    $token    = sanitize_text_field( $_POST['token'] );
    $response = wp_remote_get(
        'https://api.telegram.org/bot' . $token . '/getMe',
        array('timeout' => 10)
    );

    if ( is_wp_error($response) ) {
        wp_send_json_error();
        return;
    }

    $body = json_decode( wp_remote_retrieve_body($response), true );

    if ( isset($body['ok']) && $body['ok'] === true ) {
        // Salva il token
        $options = get_option('telegram_notify_option_name', array());
        $options['token_0'] = $token;
        update_option('telegram_notify_option_name', $options);
        wp_send_json_success( array('username' => $body['result']['username']) );
    } else {
        wp_send_json_error();
    }
}


// ============================================================
// AJAX — Salva chatid
// ============================================================
add_action('wp_ajax_nftb_wizard_save_chatid', 'nftb_wizard_save_chatid');
function nftb_wizard_save_chatid() {
    check_ajax_referer('nftb_wizard_save', 'nonce');
    if ( ! current_user_can('manage_options') ) wp_die();

    $chatid  = sanitize_text_field( $_POST['chatid'] );
    $options = get_option('telegram_notify_option_name', array());
    $options['chatids_'] = $chatid;
    update_option('telegram_notify_option_name', $options);

    wp_send_json_success();
}

// ============================================================
// AJAX — Test messaggio
// ============================================================
add_action('wp_ajax_nftb_wizard_test', 'nftb_wizard_test');
function nftb_wizard_test() {
    check_ajax_referer('nftb_wizard_save', 'nonce');
    if ( ! current_user_can('manage_options') ) wp_die();

    $bloginfo = get_bloginfo('name');
    nftb_send_teleg_message( '🚀 ' . __('WOW IT WORKS on', 'notification-for-telegram') . ' ' . $bloginfo );

    wp_send_json_success();
}