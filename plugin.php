<?php
/*
Plugin Name: Exclude Bots from Click Logs
Plugin URI: https://github.com/harindujayakody/exclude-bots-from-click-logs-yourls
Description: A plugin to exclude clicks from known bots and crawlers from being logged
Version: 0.1
Author: Harindu Jayakody
Author URI: https://ekathuwa.org
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// List of known bots and crawlers (user agent substrings)
function get_bot_user_agents() {
    return array(
        'googlebot',
        'bingbot',
        'yandexbot',
        'duckduckbot',
        'slurp',
        'baiduspider',
        'facebookexternalhit',
        'twitterbot',
        'rogerbot',
        'linkedinbot',
        'embedly',
        'quora link preview',
        'showyoubot',
        'outbrain',
        'pinterest/0.',
        'developers.google.com/+/web/snippet',
        'slackbot',
        'vkShare',
        'W3C_Validator',
        'redditbot',
        'Applebot',
        'WhatsApp',
        'flipboard',
        'tumblr',
        'bitlybot',
        'SkypeUriPreview',
        'nuzzel',
        'Discordbot',
        'Google Page Speed',
        'Qwantify',
        // AI crawlers / LLM fetchers (added 2026-11)
        'GPTBot',
        'ChatGPT-User',
        'ClaudeBot',
        'PerplexityBot',
        'Bytespider',
        'CCBot',
        'Meta-ExternalAgent',
        'Applebot-Extended',
    );
}

// Check if user agent is a bot
function is_bot($user_agent) {
    $bots = get_bot_user_agents();
    foreach ($bots as $bot) {
        if (stripos($user_agent, $bot) !== false) {
            return true;
        }
    }
    return false;
}

// Filter clicks from bots.
//
// Verified against YOURLS core (includes/functions.php):
// - `shunt_log_redirect` is applied at the top of yourls_log_redirect() only.
//   Returning true short-circuits that one function (the detailed click log
//   INSERT). It does NOT stop the redirect itself: yourls_redirect_shorturl()
//   calls yourls_log_redirect() and then sends the visitor on with
//   yourls_redirect( $url, 301 ) regardless of the shunt result.
// - The click counter in the main links table is updated separately by
//   yourls_update_clicks(), which has its own shunt, `shunt_update_clicks`.
//   Hooking only `shunt_log_redirect` would still let bot clicks inflate the
//   displayed click count, so both shunts are registered here.
// - Per the YOURLS plugin docs, a shunt filter must return the unmodified
//   value it was passed unless its own conditions match, so non-bot requests
//   return $false untouched (never a hardcoded literal).
yourls_add_filter( 'shunt_log_redirect', 'exclude_bots_from_click_log', 10, 2 );
yourls_add_filter( 'shunt_update_clicks', 'exclude_bots_from_click_log', 10, 3 );
function exclude_bots_from_click_log( $false, $keyword, $clicks = false ) {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if ( is_bot( $user_agent ) ) {
        // Return true to shunt (skip) logging / click counting for this bot.
        return true;
    }
    // Return the passed-in value unchanged to proceed with normal logging.
    return $false;
}
?>
