<?php
if (!defined('YOURLS_ABSPATH')) {
    define('YOURLS_ABSPATH', true);
}

require_once __DIR__ . '/plugin.php';

function run_tests() {
    $bots = [
        'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
        'Twitterbot/1.0',
        'Mozilla/5.0 (compatible; ClaudeBot/1.0; +claudebot@anthropic.com)',
        'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; GPTBot/1.2; +https://openai.com/gptbot)',
        'Mozilla/5.0 (compatible; PerplexityBot/1.0; +https://perplexity.ai/perplexitybot)',
        'WhatsApp/2.21.12.21 A',
        'Slackbot-LinkExpanding 1.0 (+https://api.slack.com/robots)',
        'Discordbot/2.0; +https://discordapp.com',
        'Mozilla/5.0 (compatible; Bytespider; https://zhanzhang.toutiao.com/)',
    ];

    $real_users = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/130.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.0 Safari/605.1.15',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.6 Mobile/15E148 Safari/604.1',
    ];

    foreach ($bots as $ua) {
        if (!is_bot($ua)) {
            echo "FAIL: Expected '$ua' to be detected as a bot\n";
            exit(1);
        }
    }

    foreach ($real_users as $ua) {
        if (is_bot($ua)) {
            echo "FAIL: Expected real user agent '$ua' not to be detected as bot\n";
            exit(1);
        }
    }

    // Verify filter function
    $_SERVER['HTTP_USER_AGENT'] = $bots[0];
    if (exclude_bots_from_click_log(false, 'test') !== true) {
        echo "FAIL: Expected shunt filter to return true for bot UA\n";
        exit(1);
    }

    $_SERVER['HTTP_USER_AGENT'] = $real_users[0];
    if (exclude_bots_from_click_log(false, 'test') !== false) {
        echo "FAIL: Expected shunt filter to return false for real user UA\n";
        exit(1);
    }

    echo "All 13 bot filter tests passed successfully!\n";
}

run_tests();
