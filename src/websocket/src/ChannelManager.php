<?php

namespace App;

class ChannelManager {
    /**
     * @var array
     */
    private $channels = [];

    /**
     * @param mixed $channelId
     * @param mixed $fd
     *
     * @return void
     */
    public function addConnectionToChannel($channelId, $fd): void {
        if (empty($this->channels[$channelId])) {
            $this->channels[$channelId] = [];
        }

        if (!in_array($fd, $this->channels[$channelId])) {
            $this->channels[$channelId][] = $fd;
        }
    }

    /**
     * @param mixed $fd
     *
     * @return void
     */
    public function removeConnectionFromChannels($fd): void {
        foreach ($this->channels as $channelId => $connections) {
            if (in_array($fd, $connections)) {
                $key = array_search($fd, $connections);
                unset($this->channels[$channelId][$key]);
            }
        }
    }

    /**
     * @param mixed $channelIds
     *
     * @return array
     */
    public function getConnectionsInChannels($channelIds): array {
        $connections = [];

        foreach ($channelIds as $channelId) {
            if (!empty($this->channels[$channelId])) {
                $connections = array_merge($connections, $this->channels[$channelId]);
            }
        }

        return $connections;
    }

}
