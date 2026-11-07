<?php
/*
Plugin Name: Exclude Bots from Click Logs
Plugin URI: https://github.com/harindujayakody/exclude-bots-from-click-logs-yourls
Description: A plugin to exclude clicks from known bots and crawlers from being logged
Version: 1.0
Author: Harindu Jayakody
Author URI: https://ekathuwa.org
*/

// No direct call
if( !defined( 'YOURLS_ABSPATH' ) ) die();

// List of known bots and crawlers (user agent substrings, lowercase)
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
        'vkshare',
        'w3c_validator',
        'redditbot',
        'applebot',
        'applebot-extended',
        'whatsapp',
        'flipboard',
        'tumblr',
        'bitlybot',
        'skypeuripreview',
        'nuzzel',
        'discordbot',
        'google page speed',
        'qwantify',
        // Modern AI and Search crawlers
        'gptbot',
        'chatgpt-user',
        'claudebot',
        'perplexitybot',
        'bytespider',
        'ccbot',
        'meta-externalagent',
        'amazonbot',
        'facebookcatalog'
    );
}

// Check if user agent is a bot
function is_bot($user_agent) {
    if (empty($user_agent)) {
        return false;
    }
    $bots = get_bot_user_agents();
    foreach ($bots as $bot) {
        if (stripos($user_agent, $bot) !== false) {
            return true;
        }
    }
    return false;
}

// Filter clicks from bots
// Note: shunt_log_redirect specifically shunts yourls_log_redirect() (click statistics logging).
// Returning true prevents the click from being recorded in the database, while the HTTP 301/302
// redirect in yourls_redirect() proceeds unimpeded.
if (function_exists('yourls_add_filter')) {
    yourls_add_filter('shunt_log_redirect', 'exclude_bots_from_click_log');
}

function exclude_bots_from_click_log($false, $keyword = '') {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (is_bot($user_agent)) {
        // Return true to shunt (skip) logging
        return true;
    }
    // Return false to proceed with normal logging
    return false;
}
