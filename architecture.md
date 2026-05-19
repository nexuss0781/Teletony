# Telegram Admin Attribution Bot Architecture

The Telegram Admin Attribution Bot is designed to identify and log the identity of administrators who post in Telegram channels, effectively bypassing the anonymity provided by the "Hide Poster Name" setting. Standard Telegram Bot API limitations prevent bots from seeing the specific sender of a channel post unless signatures are explicitly enabled. To overcome this, the bot leverages **MadelineProto**, a powerful PHP library that interacts with Telegram's MTProto protocol. This allows the bot to operate with the elevated privileges of a user account, enabling access to the channel's administrative logs.

### Core Operational Logic

The bot functions by monitoring updates across all channels where the associated Telegram account holds administrative rights. Upon detecting a new channel message, the bot invokes the `channels.getAdminLog` method. By filtering the administrative log for recent "post message" events that correspond to the new message's ID, the bot can accurately extract the `user_id` of the performing administrator. Once the identity is confirmed, the bot retrieves the administrator's full name or username and prepares a notification.

Notifications are dispatched to a designated **Central Private Channel**. This channel is identified when the owner executes the `/start` command within it. The bot records this location as the authoritative destination for all attribution reports. Each report includes the source channel's name, the administrator's identity, and a brief snippet of the posted content to provide context while maintaining a concise log.

### Hosting and Deployment Strategy

The deployment target is **InfinityFree**, a popular free hosting service. While MadelineProto is resource-intensive, the bot is optimized to run within the constraints of shared hosting environments. The architecture utilizes a session-based file system to maintain the Telegram connection. Because free hosting often imposes strict execution time limits, the bot is designed to be triggered via web requests or scheduled cron jobs, ensuring it remains active to process incoming updates.

| Component | Description |
| :--- | :--- |
| **MadelineProto** | The engine that interfaces with Telegram MTProto API. |
| **Central Channel** | The private channel used for reporting and command control. |
| **Admin Log** | The data source used to de-anonymize channel posters. |
| **InfinityFree** | The hosting platform where the PHP environment resides. |

### Technical Components

| File | Purpose |
| :--- | :--- |
| `index.php` | Provides a web-based interface for the initial Telegram login and status monitoring. |
| `bot.php` | Contains the primary event handler and logic for log extraction and reporting. |
| `settings.json` | Stores configuration data, including the central channel ID and user preferences. |
| `madeline.php` | The official loader script that manages the MadelineProto library installation. |
