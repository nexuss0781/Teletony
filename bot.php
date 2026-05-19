<?php

if (!file_exists('madeline.php')) {
    copy('https://phar.madelineproto.xyz/madeline.php', 'madeline.php');
}
include 'madeline.php';

use danog\MadelineProto\EventHandler;
use danog\MadelineProto\Tools;
use danog\MadelineProto\API;

class MyEventHandler extends EventHandler
{
    private $centralChannelId;

    public function __construct($MadelineProto)
    {
        parent::__construct($MadelineProto);
        $this->loadSettings();
    }

    private function loadSettings()
    {
        if (file_exists('settings.json')) {
            $settings = json_decode(file_get_contents('settings.json'), true);
            $this->centralChannelId = $settings['central_channel'] ?? null;
        }
    }

    private function saveSettings()
    {
        file_put_contents('settings.json', json_encode(['central_channel' => $this->centralChannelId]));
    }

    public function onUpdateNewChannelMessage($update)
    {
        $message = $update['message'];
        $peerId = $this->getId($message['peer_id']);
        
        // Handle /start command in the private channel
        if (isset($message['message']) && strpos($message['message'], '/start') === 0) {
            $this->centralChannelId = $peerId;
            $this->saveSettings();
            $this->messages->sendMessage(['peer' => $peerId, 'message' => "✅ This channel is now set as the Central Reporting Channel."]);
            return;
        }

        // Only process messages from other channels if we have a central channel set
        if (!$this->centralChannelId || $peerId == $this->centralChannelId) {
            return;
        }

        try {
            // Wait a moment for the admin log to be populated
            Tools::wait(1);

            // Fetch admin log to find who posted the message
            $adminLog = $this->channels->getAdminLog([
                'channel' => $peerId,
                'q' => '',
                'events_filter' => ['post_message' => true],
                'limit' => 5,
            ]);

            $posterName = "Unknown Admin";
            foreach ($adminLog['events'] as $event) {
                if (isset($event['action']['message']['id']) && $event['action']['message']['id'] == $message['id']) {
                    $userId = $event['user_id'];
                    $user = $this->getInfo($userId);
                    $posterName = ($user['User']['first_name'] ?? '') . ' ' . ($user['User']['last_name'] ?? '');
                    if (isset($user['User']['username'])) {
                        $posterName .= " (@" . $user['User']['username'] . ")";
                    }
                    break;
                }
            }

            // Prepare snippet (max 50 chars)
            $textSnippet = mb_substr($message['message'] ?? "[Media/No Text]", 0, 50);
            $channelInfo = $this->getInfo($peerId);
            $channelName = $channelInfo['Chat']['title'] ?? "Channel";

            $report = "📢 **New Post in {$channelName}**\n";
            $report .= "👤 **Poster:** {$posterName}\n";
            $report .= "📝 **Snippet:** {$textSnippet}...";

            $this->messages->sendMessage([
                'peer' => $this->centralChannelId,
                'message' => $report,
                'parse_mode' => 'Markdown'
            ]);

        } catch (\Exception $e) {
            // Log error silently or send to central channel for debugging
            $this->logger("Error identifying poster: " . $e->getMessage());
        }
    }
}

$settings = [
    'logger' => [
        'logger' => 3, // Log to file
    ],
    'serialization' => [
        'cleanup_before_serialization' => true,
    ],
];

$MadelineProto = new API('session.madeline', $settings);
$MadelineProto->startAndLoop(MyEventHandler::class);
