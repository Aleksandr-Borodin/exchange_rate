<?php
/**
 * CollectRateDispatcher.php
 * Created: 29.09.2026
 * Author: Alex
 * Project: exchange_rate
 */

declare(strict_types=1);

namespace ExchangeRate\Infrastructure\Messaging;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class CollectRateDispatcher
{
    /**
     * @var string
     */
    private const QUEUE = 'collect_rate';

    /**
     * @param string $_host
     * @param int $_port
     * @param string $_user
     * @param string $_password
     * @param string $_vhost
     */
    public function __construct(private readonly string $_host, private readonly int $_port, private readonly string $_user, private readonly string $_password, private readonly string $_vhost = '/') {}

    /**
     * @param string $dayResult
     * @param string $source
     * @return bool
     */
    public function dispatch(string $dayResult, string $source): bool
    {
        try {
            $connection = new AMQPStreamConnection($this->_host, $this->_port, $this->_user, $this->_password, $this->_vhost);
            $channel = $connection->channel();
            $channel->queue_declare(self::QUEUE, false, true, false, false);
            $message = new AMQPMessage($this->_prepareMessageText($dayResult, $source), $this->_prepareMessageParams());
            $channel->basic_publish($message, '', self::QUEUE);
            $channel->close();
            $connection->close();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @param string $dayResult
     * @param string $source
     * @return string
     */
    private function _prepareMessageText(string $dayResult, string $source): string
    {
        $messageArray = ['date' => $dayResult, 'source' => $source,];
        return json_encode($messageArray, JSON_THROW_ON_ERROR);
    }

    /**
     * @return array
     */
    private function _prepareMessageParams(): array
    {
        return [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
        ];
    }
}
