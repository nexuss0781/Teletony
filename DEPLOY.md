# Deployment Guide for InfinityFree

This bot uses **MadelineProto** to identify anonymous posters in Telegram channels. Follow these steps to deploy it on your InfinityFree hosting.

## 1. Prerequisites
- An **InfinityFree** account with a website created.
- A Telegram account to use as the "Userbot" (it is recommended to use a secondary account).
- **API ID** and **API Hash** from [my.telegram.org](https://my.telegram.org).

## 2. Uploading Files
Upload the following files to your `htdocs` folder via FTP (e.g., using FileZilla):
- `index.php`
- `bot.php`
- `architecture.md` (Optional, for your reference)

*Note: `madeline.php` and `session.madeline` will be created automatically when you first run the bot.*

## 3. Configuration
1.  Open your website URL (e.g., `http://your-site.infinityfreeapp.com/index.php`).
2.  The page will prompt you for your **API ID**, **API Hash**, and **Phone Number**.
3.  Enter the code sent to your Telegram account to complete the login.
4.  Once logged in, the session will be saved on the server.

## 4. Setting up the Bot
1.  Add the Telegram account you logged in with as an **Administrator** in all the channels you want to monitor.
2.  Create a **Private Channel** (the Central Channel) where you want to receive reports.
3.  Add the account as an Admin to this private channel as well.
4.  In the private channel, send the message: `/start`.
5.  The bot will reply confirming it is set up.

## 5. Keeping the Bot Alive
InfinityFree and other free hosts often kill long-running PHP scripts. To keep the bot active:
- **Option A (Manual):** Keep the `index.php` page open in your browser.
- **Option B (Cron Job):** In your InfinityFree Control Panel, look for **Cron Jobs**. Set a cron job to run every 5 minutes targeting `bot.php`.
    - Command: `php /home/volX_X/epizy_XXXXX/htdocs/bot.php` (adjust the path based on your hosting details).

## Important Notes
- **Privacy:** MadelineProto stores sensitive session data. Ensure your `htdocs` folder is not publicly indexable.
- **Limitations:** Free hosting has memory limits. If the bot crashes, refresh `index.php` to restart the session.
- **Admin Log:** The bot relies on the "Admin Log" feature. The account MUST have the "View Admin Log" permission in all channels.
