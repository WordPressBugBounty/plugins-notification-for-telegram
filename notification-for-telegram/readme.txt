=== Notification for Telegram ===
Contributors: rainafarai
Donate link: https://www.paypal.com/paypalme/rainafarai
Tags: Telegram, Woocommerce ,Notification, mcp, ai
Requires at least: 4.0
Tested up to: 6.9.1
Stable tag: 3.5.1
Requires PHP: 7.4 
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Sends notifications to Telegram users or groups, when some events occur in WordPress.

== Description ==
The "Notification for Telegram" plugin for WordPress is a tool that allows you to send notifications and messages to a Telegram channel, group or user/s when specific events occur on your WordPress site.
This plugin is useful for monitoring critical events on your site, such as new comments, new user registrations, publishing activities, New forms sent,Woocommerce and Surecart orders, cart and lowstock, Mailchimp and more, by sending notifications directly to a Telegram channel or group or user/s of your choice. It also offers a shortcode to send Telegram notifications on every page of your website or in your code.

Receive Telegram messages notification when:  


* When receive a new order in Woocommerce.
* When a Woocommerce order change status.
* When receive a new order in Surecart. 
* Every event captured by WP Activity Log plugin
* New field in Woocommerce checkout page let customers add the own telegram nickname
* Low Stock Product notifications when a product is low stock conditions.
* Shows Telegram Nick link in admin order details page when present
* When receive new forms (supports Elementor Pro Form, WPForm , CF7 and Ninjaform)
* When new user subscribes  or unsubscribes to mailchimp. MC4WP integration
* When new user registers. Helps identify unauthorized or suspicious registrations.
* When users login or fail login.
* When new comment is posted.
* When someone adds or remove a product in the Woocommerce cart.
* When a new Pending posts is received. (works with any post type)
* Say function to speak to make the bot say Something to the people
* Cron job detect and notify when Plugins & Core need to update. 
* Send custom message with Shortcode anywhere in your WP.
* Should Work on Multisite

You can enable/disable every notification in the Plugin settings page.
 
To configure the plugin, you need a valid Telegram API token. Its easy to get starting a Telegram Bot.
You can learn about obtaining  tokens and generating new ones in 

= Setup Guide =

A complete step-by-step guide is available here:
[View the full documentation](https://docs.google.com/document/d/1HCa54OhOm9Vm0Jz2AUjQUHK71djzOUQBDZF-9NH7irU/edit?tab=t.0)




To get your Bot Token, you can refer to one of these resources:

* [Official Telegram documentation](https://core.telegram.org/bots#6-botfather "Obtaining tokens and generating new ones")
* [Step-by-step guide on Medium](https://medium.com/shibinco/create-a-telegram-bot-using-botfather-and-get-the-api-token-900ba00e0f39 "Create a Telegram bot using BotFather and Get the API Token")

To get your Chat ID, you need at least one recipient ID. You can retrieve it using one of these bots directly in Telegram:

* [@get_id_bot](https://telegram.me/chatIDrobot "@chatIDrobot") — send /start and it will reply with your personal Chat ID
* [@RawDataBot](https://t.me/RawDataBot) — send any message and it will return your full account info including your ID

For Channel and Group Chatid settings see the [FAQ section](#faq).


Once You got the 2 fields save the configuration and try the "TEST" button .. you should receive a message in you telegram : "WOW IT WORKS" !! If not, check token and chatid fields again for the correct values.

this plugin is relying on a 3rd party service to geolocate the Ip address https://ip-api.com/
https://ip-api.com/docs/legal  to see the services’ a terms of use and/or privacy policies


== AI & MCP Compatible ==

This plugin supports the **WordPress Abilities API** and is compatible with 
**MCP (Model Context Protocol)** — the standard that allows AI agents like 
Claude, ChatGPT, and others to interact with WordPress autonomously.

If the **Abilities API** and **MCP Adapter** plugins are installed, any 
authorized AI agent can automatically discover and use the 
**Send Telegram Message** ability — no extra configuration needed.

**What an AI can do with this plugin:**
* Send a Telegram message with custom text
* Add an inline button with label and URL
* Override the default chat ID

= Requirements for MCP =
* WordPress 6.9+
* Plugin: Abilities API (WordPress/abilities-api)
* Plugin: MCP Adapter (WordPress/mcp-adapter)

== MESSAGES TRANSLATION == 
To translate Telegram messages, use WPML or Loco Translate. All notification strings are now translatable.
Go to Loco Translate → Plugins → Notification for Telegram to add your translations.
For WPML, ensure String Translation is enabled to modify notification texts.

== ACTION HOOK for third-party Plugin == 

Notification for Telegram (version > 3.5.1) exposes a dedicated action hook so any third-party plugin can send messages without calling internal functions directly.
Basic usage:
`do_action( 'nftb_send_message', 'Your message here' );`

Look FAQ Section for more esamples.


== SHORTCODE EXAMPLE == 

`[telegram_mess  message="Im so happy" chatids="0000000," token="000000000:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA" showsitename="1" showip="1" showcity="1" ]`


SHORTCODE OPTIONS:

* message : Your message to be sent. Example (message="hello world")

* chatids : Recipient(s) who will receive your message separated by comma (example chatids="0000000,11111111") , If not present this field  the shortcode will use default value in Plugin option page.

* token:  The token looks something like 123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11 
If not present this field, the shortcode will use default value in Plugin option page.

* showsitename: if set to "1" appends sitename after the message. Defaultvalue is "0" Example (showsitename="1")

* showip: if set to "1" appends user ip address after the message. Default value is "0" Example (showip="1")

* showcity: if set to "1" appends user city name after the message. Default value is "0" Example (showcity="1")


USE SHORTCODE IN YOU PHP CODE

`<?php

$date = date("d-m-Y");

do_shortcode('[telegram_mess  message="'.$date .'" chatids="0000000," token="000000000:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA" showsitename="1" showip="1" showcity="1" ]'); 

?>`

== WOOCOMMERCE FILTER HOOKS ==

We have created 4 filter hooks for WooCommerce order notification message. 4 new positions: Message Header, Message Footer, before Items, and after Items. And we have created a filter through which you can add custom code to product rows, and if you want, you can replace and customize the entire row. :

4 new Positions and code axample ( echo payment_status in the 4 positions)

`<?php
add_filter('nftb_order_header_message_hook', 'my_filter_function', 10, 1); 
add_filter('nftb_order_before_items_hook', 'my_filter_function', 10, 1);
add_filter('nftb_order_after_items_hook', 'my_filter_function', 10, 1);
add_filter('nftb_order_footer_message_hook', 'my_filter_function', 10, 1);

function my_filter_function($order_id) {
  $order = wc_get_order($order_id);
  if ($order) {
      // Get order details
      $order_data = $order->get_data();
  
      // Extract specific order information
      
      $payment_status = $order->get_status();
      $payment_method = $order->get_payment_method();  
  }
  return  "\r\n\r\n".$payment_method."(".$payment_status.")\r\n" ;
}
?>`

Product rows Filter with 2 different behaviors ADD or REPLACE LINE 

`<?php
add_filter('nftb_order_product_line_hook', 'my_item_line_function', 10, 3);

function my_item_line_function($message ,$product_id, $item) {

    // ADD SOME CODE $product_id TO ORIGINAL row $message.
    $modified_data = $message. "->".$product_id. "\r\n";

    // REPLACE Product ITEM LINE CODE WITH YOUR CODE  without concatenate $message.
    $modified_data = $product_id. "\r\n";

    return $modified_data;
} 
?>`

== USER LOGIN FILTER HOOKS ==

`<?php
// Triggered on successful user login
add_filter( 'nftb_login_notification', 'custom_message_modifier', 10, 1 );

// Triggered on new user registration
add_filter( 'nftb_user_registered_notification', 'custom_message_modifier', 10, 1 );

// Triggered when an existing user fails to login
add_filter( 'nftb_existing_user_fails_login_notification', 'custom_message_modifier', 10, 1 );

// Triggered when an unknown user fails to login
add_filter( 'nftb_unknown_user_fails_login_notification', 'custom_message_modifier', 10, 1 );

// Example: append the user registration date to the notification
function custom_message_modifier( $user_id ) {
    $user_info = get_userdata( $user_id );

    if ( $user_info ) {
        $registration_date = $user_info->user_registered;
        $timestamp         = strtotime( $registration_date );

        $formatter = new IntlDateFormatter( 'en_US', IntlDateFormatter::LONG, IntlDateFormatter::LONG, 'UTC' );
        $formatter->setPattern( 'd MMMM y HH:mm:ss' );

        $formatted_date = $formatter->format( $timestamp );
        $message = "\r\n\r\nUser Registration Date: " . $formatted_date . "\r\n\r\n";
    } else {
        $message = "\r\nNo info available for this user.\r\n";
    }

    return $message;
}
?>`



== LEGACY FUNCTIONS (kept for backward compatibility) ==

Before hooks were introduced, 3 overridable functions allowed message customization without editing plugin code. These are still supported but we encourage migrating to hooks.

1) Before the product list — example: prepend the Order ID

`<?php
function nftb_order_before_items( $order_id ) {
    return "ORDER ID: " . $order_id;
}
?>`

2) After the product list — example: append the order currency

`<?php
function nftb_order_after_items( $order_id ) {
    $order = wc_get_order( $order_id );
    $data  = $order->get_data();
    return "Currency: " . $data['currency'];
}
?>`

3) At the end of each product line — example: append the product slug

`<?php
function nftb_order_product_line( $product_id, $item ) {
    $product = wc_get_product( $product_id );
    return " | " . $product->get_slug() . " ";
}
?>`

Suggestions for new notifications, hooks, and plugin integrations are always welcome!

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Use the Settings->Plugin Name screen to configure the plugin
1. (Make your instructions match the desired user flow for activating and installing your plugin. Include any steps that might be needed for explanatory purposes)

== Frequently Asked Questions ==

= How do I obtain a Telegram Bot Token? =

When you create a Telegram bot, BotFather generates a unique token for you. BotFather (@botfather) is the official Telegram bot for creating and managing bots — search for it directly inside Telegram.

**Creating a new bot**

1. Open Telegram and search for @botfather.
2. Send the command /newbot.
3. BotFather will ask you for two things:
   - Name — the display name shown in contact details and conversations (e.g. "My Awesome Bot").
   - Username — a short unique handle used in mentions and t.me links (e.g. @my_awesome_bot). Usernames must be 5–32 characters long, can only contain Latin letters, numbers and underscores, and must end in "bot".
4. Once created, BotFather will provide your authorization token.

**What does a token look like?**

A token is a string in this format:
110201543:AAHdqTcvCH1vGWJxfSeofSAs0K5PALDsaw

This token is required to authorize your bot and send requests to the Telegram Bot API. Keep it private — anyone who has it can take full control of your bot.

**What if I lose my token or it gets compromised?**

Send the /token command to @botfather, select your bot, and it will generate a new token. The old one will be immediately invalidated.



= How do I find my personal Telegram Chat ID? =

There are two quick ways:

1. Search for @userinfobot or @get_id_bot on Telegram, send /start and the bot will instantly reply with your numeric user ID (e.g. 29627823).
2. Search for @RawDataBot (https://t.me/RawDataBot), send any message, and it will reply with your full account info including your ID.

= How do I find the Chat ID of a group? =

1. Add your bot to the group as a member.
2. Send any message in the group (or type /start@yourbotname).
3. Open the following URL in your browser, replacing <TOKEN> with your bot token:
   https://api.telegram.org/bot<TOKEN>/getUpdates
4. Look for the "chat" object in the JSON response — the "id" field is your group Chat ID. Group IDs are always negative numbers (e.g. -1001234567890).

Note: if the result is empty, make sure you sent a message after adding the bot, then refresh the URL.

= How do I find the Chat ID of a channel? =

1. Add your bot to the channel and assign it Administrator privileges.
2. Post any message in the channel.
3. Open the following URL in your browser:
   https://api.telegram.org/bot<TOKEN>/getUpdates
4. Find the "chat" object in the JSON — the "id" is your channel Chat ID. Channel IDs always start with -100 (e.g. -1009876543210).

= Is there a universal method that works for users, groups and channels? =

Yes. Add @RawDataBot (https://t.me/RawDataBot) to your group or channel (as admin), or simply message it privately. Send any message and it will reply with a full JSON breakdown of the chat, including the exact Chat ID you need.

= My getUpdates response is empty — what should I do? =

This usually means no messages have been sent to the bot recently. Try the following:
1. Make sure the bot has been added to the chat.
2. Send a new message in the chat (or write directly to the bot in a private conversation).
3. Reload the getUpdates URL in your browser.
If the response is still empty, check that no other application is consuming the bot updates (e.g. a webhook that is already set — in that case you need to delete it first via /deleteWebhook).

= Can i insert more than one recipient chatid? =

Yes you can add more than one chattid  separated by a comma (,)
both in option page and in the shortcode.

= Does this plugin support AI agents and MCP? =
Yes! Starting from version 3.5, this plugin supports the WordPress Abilities API
and is compatible with MCP (Model Context Protocol), the open standard that allows
AI agents like Claude, ChatGPT, and others to interact with WordPress autonomously.

= What do I need to enable MCP support? =
You need to install two additional free plugins:
* Abilities API — github.com/WordPress/abilities-api/releases
* MCP Adapter — github.com/WordPress/mcp-adapter/releases

Once both are active, the "Send Telegram Message" ability is automatically
available to any authorized AI agent. No extra configuration needed.

= How does an AI agent authenticate? =
Via WordPress Application Passwords — go to Users → Your Profile →
Application Passwords, create one, and use it in your MCP client configuration.
Your main password is never exposed.

= What can an AI agent do with this plugin? =
An authorized AI agent can:
* Send a Telegram message with custom text
* Include an optional inline button with label and URL
* Target a specific Telegram chat ID, overriding the default

= How can I test the MCP integration without an AI? =
You can test everything for free using curl from your terminal.

First, make sure the plugin is configured and the built-in "Send Test Message"
button works correctly in the plugin settings.

Then run these commands in order:

Step 1 — Initialize the session and get the Session ID:

curl -X POST "https://YOURSITE.com/wp-json/nftb-telegram/mcp" \
  -H "Content-Type: application/json" \
  -u "ADMIN:APP_PASSWORD" \
  -D - \
  -d '{"jsonrpc":"2.0","id":1,"method":"initialize","params":{"protocolVersion":"2024-11-05","capabilities":{},"clientInfo":{"name":"test","version":"1.0"}}}'

Look for the Mcp-Session-Id value in the response headers and copy it.

Step 2 — Verify the tool is available:

curl -X POST "https://YOURSITE.com/wp-json/nftb-telegram/mcp" \
  -H "Content-Type: application/json" \
  -H "Mcp-Session-Id: YOUR-SESSION-ID" \
  -u "ADMIN:APP_PASSWORD" \
  -d '{"jsonrpc":"2.0","id":2,"method":"tools/list","params":{}}'

You should see "notification-for-telegram-send-message" in the response.

Step 3 — Send a real Telegram message via MCP:

curl -X POST "https://YOURSITE.com/wp-json/nftb-telegram/mcp" \
  -H "Content-Type: application/json" \
  -H "Mcp-Session-Id: YOUR-SESSION-ID" \
  -u "ADMIN:APP_PASSWORD" \
  -d '{"jsonrpc":"2.0","id":3,"method":"tools/call","params":{"name":"notification-for-telegram-send-message","arguments":{"message":"🤖 MCP is working!"}}}'

If the message arrives in your Telegram chat — everything is working correctly.

APP_PASSWORD = a WordPress Application Password (Users → Your Profile → Application Passwords).

= Is MCP support stable? =
This feature is currently experimental. We welcome feedback and bug reports —
if you test it, please leave a review or open an issue on the plugin's GitHub page.

= Where can I find the MCP endpoint? =
Once the required plugins are installed, your MCP endpoint will be available at:
https://yoursite.com/wp-json/nftb-telegram/mcp


= Can I send Telegram messages from my own plugin? =
Yes! Notification for Telegram exposes a dedicated action hook so any third-party plugin can send messages without calling internal functions directly.

Basic usage:
`do_action( 'nftb_send_message', 'Your message here' );`

With an inline button link:
`do_action( 'nftb_send_message', 'New order received!', 'View Order', 'https://yoursite.com/order/123' );`

With a custom Chat ID:
`do_action( 'nftb_send_message', 'Your message', '', '', '123456789' );`

Parameters:
1. `$message`  — (string) required — the text to send
2. `$urlname`  — (string) optional — label of the inline button
3. `$urllink`  — (string) optional — URL of the inline button
4. `$chatid`   — (string) optional — override the default Chat ID from settings

Always check the plugin is active before using the hook:
`if ( is_plugin_active( 'notification-for-telegram/index.php' ) ) {
    do_action( 'nftb_send_message', 'Hello from my plugin!' );
}`



== Screenshots == 

1. This is the Global option page in you Dashboard. Enter Token Chatid
2. Choose which notification you want to receive 
3. A shortcode example.
4. Order Telegram Notification 
5. Login fails result on your Mobile app 
6. Woocommerce Setting Tab
7. Cron Setting Tab keep update your system 
8. Hook Position in Order Notification
9. Hook Position in Login Notification 

== Changelog ==
= 3.5.1 =
- WPForms Pro/Elite compatibility — notifications now should work with both Lite and Pro/Elite versions.
- Security fix: Resolved a stored XSS vulnerability affecting the admin order page by properly escaping Telegram username output and improving input validation.
- Security fix: Resolved CSRF vulnerability and insufficient authorization on AJAX handlers nftb_cron_action and nftb_cron_action_set.
- Security fix: Thanx to Nguyen Ba Khanh, Ahmad and Nguyen Xuan Chien.
- Security fix: Resolved CSRF vulnerability in admin notice dismiss handler by adding nonce verification.
- Added nftb_send_message action hook to allow third-party plugins to send Telegram messages. 
- Correct Small typos errors in options page thx @daymobrew
- Removed: Cron scheduler UI and automatic update notification feature - other plugins do it better!
- Code Cleaning
- Fixed CF7 now hides empty fields

= 3.5 =
- Added: MCP (Model Context Protocol) compatibility
- Added: WordPress Abilities API integration
- AI agents can now send Telegram notifications autonomously
- Fixed PHP 8.2+ deprecation warning on Plugin Option Page. Deprecated: Creation of dynamic property nftb_TelegramNotify::$telegram_notify_options_tab

= 3.4.7 =
- We've integrated real-time Telegram notifications with the WP Activity Log plugin, allowing you to receive instant alerts for all WordPress activity directly in your Telegram messenger. Notifications display complete, expanded event objects with all available metadata. Ensure WP Activity Log plugin is active and remember to enable notification for WP Activity Log in option page "Post / Forms / User"  
- Added a new feature Backtrace that captures the origin of every new user registration.
The system now includes the file name and line number from which the registration was triggered, providing full traceability for debugging and security audits. This information is sent when a new user registers.
- Added Option in "Post/Form/User" Tab, "Disable Backtrace for User Registration" , to disable the Backtrace info in the new user registers notification.

= 3.4.6 =
- Added product sku in the notification when a product is added to the cart (maxivillus request)
- Added product sku in the notification when a product is removed fromn the cart (maxivillus request)
- Added option to hide the product list in notification messages and display only the item count. (mouring request)

= 3.4.5 =
- Removed ver stripping from asset URLs for better cache handling and plugin compatibility.

= 3.4.4 =
- Better logic to add the minimal CSS when main CSS is disable

= 3.4.3 =
- Added a minimal CSS when main CSS is disabled 

= 3.4.2 =
- Added support for Surecart Webhook Endpoint and some events [Beta]: refund.created, refund.succeeded, 
order.cancelled, order.voided , variant.stock_adjusted , order.fulfilled. order.unfulfilled
- added function to hide Surecart Tab if the plugin in is not Active o installed
- Updated CSS to use `.telegram-notify-page` prefix for all styles to prevent conflicts with other plugins and themes (e.g., Elementor).
- Added: New button in the "Telegram Config" tab to disable plugin CSS loading, preventing conflicts with other themes or plugins (e.g., Elementor).
- Removed Bootstrap dependency to reduce plugin footprint and avoid potential conflicts in the WordPress admin area.

= 3.4.1 =
- Added order support for Surecart
- Support for translating Telegram messages using WPML and Loco Translate. 
Now all notification messages can be fully localized

= 3.4 =
- Fix nftb_new_order_id_for_notification_ ( flag to understand if the order notification is already sent) 
in wp_options autoload to "Off" to save memory on sites with many orders.
- Added a check to modify the existing options in wp_options to "off".
- Fix Uncaught TypeError: array_merge():  Ninja Forms (3.8.17 and 3.8.23 ) PHP > 8 thx @scratchycat

= 3.3.7 =
- Added option to hide the "EDIT ORDER" link in the WooCommerce order confirmation message.
- Added the ability to exclude some CF7 forms by ID from notifications 
- Fixed Warning: Undefined array key “nftb_ignore_notyyy” 

= 3.3.6 = 
- Restrict admin Notice ONLY for Admin 
 
= 3.3.3 = 
- Fix unauthorized test message sending due to a missing capability (thx Wordfence)

= 3.3.1 = 
- Added html_entity_decode to clean HTML entity
- fix check if user is set before apply_filters('nftb_login_notification') 
- Fix in user login sometimes the userdata was empty
- Fix Warning: Undefined array key "notify_update" "notify_update_time" php > 8 

= 3.3 = 
- Fix on  apply_filters('nftb_existing_user_* return empty if not set
- Added User Role info in the message when a registered user fails to login
- Added Spam Filter on comment. If enabled you will not receive notification if comment is marked as spam


= 3.2 = 
- Fix: "Wc order change status" was triggering the comment notification remove order_shop post type.
- Small fixes on user message formatting CR 
- Removed all the calls to Extarnal plugin activator & link to webpage


= 3.1 =
- Added notification for new comment 

= 3.0 =
- Added Succes User Login notification 
- Added donation link to block the random message "Im really happy you are using my plugin !!".
- Added a notification for removed products from cart.
- Added Customer Order Note from Wc checkout page.
- Added Option "Do not Remove <html tags> from the telegram messages".
- Added IP address for any User Login Notification not just the map
- Fix Enable notifications when user login fails.

- Added 4 REAL WOOCOMERCE notification HOOKS : nftb_order_product_line_hook, nftb_order_header_message_hook, nftb_order_before_items_hook,
nftb_order_after_items_hook, nftb_order_footer_message_hook .

- Added 4 REAL USER LOGIN notification HOOKS 
nftb_login_notification, nftb_user_registered_notification , nftb_existing_user_fails_login_notification, nftb_unknown_user_fails_login_notification

- Added $item to the function -> nftb_order_product_line($product_id,$item); (wsjrcatarri request)



= 2.9 =
- Added Support for Elementor Form
- User Login notification new Layout more clear 
- Fix Function get_userdatabylogin (deprecated) on User login 
- Better Jquery validations ( token and chatids ) on config page

= 2.8 =
- Fixed Shortcode 

= 2.7 =
- Fixed Many Notice on woocommerce order confirmation Thankyou page. 
- Added customer total orders number in order confirmation message.

= 2.6 =
- Added Customer Phone Number in order message (Everybody Asking :=) )

= 2.5 =
- Formatted fields for CF7 Contact Form no more Var_dump()
- Added 3 Hooks in Order notification to add your custom code without modify plugin code
- Updated instructions to get your telegram chat_id number 

= 2.4 =
- Added Support for WPFORM : all fields in you telegram notification
- New Option in Woocommerce Tab : Hide/view Billing Information
- New Option in Woocommerce Tab : Hide/view Shipping Information
- Small UI fix 

= 2.3 =
- Fix warning on PHP 8

= 2.2 =
- New option to select the woocommerce trigger for the notification with 3 different actions:
	woocommerce_checkout_order_processed / woocommerce_payment_complete / woocommerce_thankyou
- Show items price with tax or not 
- Fixed activation notice error 

= 2.0 =
- New Backend UserGUI Tab division for better User Experience
- Full fields Order Notification (Items shipment, billing, is paid?, and many order details)
- Low Stock Product Enable notifications when a product is low stock conditions.
- Create a input field in wc check-out page for Telegram nickname.
- Say function to speak to make the bot say Something to the people
- MC4WP Mailchimp for WordPress plugin integration send a notification when new user subscribes to mailchimp or unsubscribes
- Cron message Setup a Cron job to keep updated about Plugins & Core Update 
- Added Emoji to messages

= 1.6 =
- now you can enable a new field in Woocommerce checkout page let customers add his telegram nickname

= 1.4 = 
- Fix new order received and Order status change No duplication now !!

= 1.3 =
- Fix message text in shortcode was blank before


= 1.2 =
- Add new option on woocommerce notification : only on new orders or on any order status change

= 1.1 =
- add icons

= 1.0 =
- Initial release



== Upgrade Notice ==
For Old versions Only !!
after updating to version> 2 check the settings again, if you have problems in the update uninstall and reinstall the plug sorry for the problem