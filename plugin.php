<?php
/*
Plugin Name: Exclude Bots from Click Logs
Plugin URI: https://yourls.org/
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
        'Qwantify'
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

// Filter clicks from bots
yourls_add_filter('shunt_log_redirect', 'exclude_bots_from_click_log');
function exclude_bots_from_click_log($false, $keyword) {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (is_bot($user_agent)) {
        // Return true to shunt (skip) logging
        return true;
    }
    // Return false to proceed with normal logging
    return false;
}
?>
