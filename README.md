# Exclude Bots from Click Logs (YOURLS)

Excludes clicks from known bots and crawlers from YOURLS click logs, so bot
traffic does not inflate your stats.

## How it works

The plugin hooks YOURLS' `shunt_log_redirect` and `shunt_update_clicks`
filters. When the request's user agent matches a known bot, both the detailed
click log entry and the click counter update are skipped. Human visitors are
unaffected: the redirect itself is never blocked (verified against YOURLS
core — `shunt_log_redirect` only short-circuits `yourls_log_redirect()`; the
301 redirect is sent afterwards by `yourls_redirect_shorturl()`).

The bot list covers the major search/social crawlers plus AI crawlers and
LLM fetchers (GPTBot, ChatGPT-User, ClaudeBot, PerplexityBot, Bytespider,
CCBot, Meta-ExternalAgent, Applebot-Extended). Matching is a case-insensitive
substring check on the user agent string.

## Install

1. Copy this folder to `YOURLS_ROOT/user/plugins/exclude-bots-from-click-logs-yourls/`.
2. In the YOURLS admin area, go to **Manage Plugins** and activate
   **Exclude Bots from Click Logs**.

## Requirements

- YOURLS 1.7 or later (uses the `shunt_log_redirect` and
  `shunt_update_clicks` filters).
- No configuration needed. To add a bot, append its user-agent substring to
  the list in `get_bot_user_agents()` in `plugin.php`.
