<?php

namespace App\Helpers;

use App\Services\Service;
use App\ReplacementRequestMessage;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Log;

class WebsocketHelpers implements MessageComponentInterface {
    /** @var \SplObjectStorage $clients */
    protected $clients;
    /** @var array $rooms */
    protected $rooms;
    /** @var array $partisipants */
    protected $partisipants;
    /** @var array $partisipant_names */
    protected $partisipant_names;
    /** @var array $existing_messages */
    protected $existing_messages;
    
    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->rooms = [];
        $this->partisipants = [];
        $this->partisipant_names = [];
        $this->existing_messages = [];

    }

    /**
     * Handle a new WebSocket connection.
     *
     * This method is called when a new client connects to the WebSocket server.
     * It attaches the connection to the clients collection and logs the event.
     *
     * @param ConnectionInterface $conn The WebSocket connection instance.
     */
    public function onOpen(ConnectionInterface $conn): void 
    {
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    /**
     * Handle incoming WebSocket messages for the replacement request chat.
     *
     * This method processes two types of messages:
     * 1. Initial connection: When a user opens a replacement request (open_replacement_request_id),
     *    they are added to the room, and all existing messages are sent to them.
     * 2. New message: When a user sends a new message, it is saved to the database
     *    and broadcasted to all participants in the room.
     *
     * The method manages room membership, participant tracking, and message history.
     *
     * @param ConnectionInterface $from The WebSocket connection of the sender.
     * @param string $msg The raw JSON message from the client.
     */
    public function onMessage(ConnectionInterface $from, $msg): void 
    {
        $msg = json_decode($msg, true);

        if (isset($msg['open_replacement_request_id'])) 
        {
            foreach ($this->rooms as &$room) {
                $key = array_search($from, $room);
                if ($key) {
                    unset($room[$key]);
                    break;
                }
            }
            
            $this->rooms[$msg['open_replacement_request_id']][] = $from;
            $this->partisipants[$from->resourceId] = $msg['open_replacement_request_id'];
            $this->partisipant_names[$from->resourceId] = $msg['author_name'];
            $replacement_request_id = $msg['open_replacement_request_id'];

            $db_existing_messages = ReplacementRequestMessage::with(['author'])->where('replacement_request_id', $msg['open_replacement_request_id'])->orderBy('created_at')->get();
            foreach ($db_existing_messages as $message) {
                $this->existing_messages['existing_messages'][] = [
                    'author_name' => $message->author->name,
                    'date' => date('d.m.y H:i', strtotime($message->created_at)),
                    'body' => $message->body,
                    'is_author' => $this->partisipant_names[$from->resourceId] == $message->author->name,
                ];
            }
            $from->send(json_encode($this->existing_messages));
        } else {
            $replacement_request_id = $msg['replacement_request_id'];
            (new Service)->addOrUpdate($msg, 'App\ReplacementRequestMessage');
            //ModelHelpers::addOrUpdate($msg, 'App\ReplacementRequestMessage');
        }

        foreach ($this->rooms[$replacement_request_id] as $client) {
            $msg['is_author'] = false;
            if ($from === $client) {
                $msg['is_author'] = true;
            }
            $msg['date'] = date('d.m.y H:i');
 
            if (isset($msg['open_replacement_request_id'])) {
                $client->send('{"new_partisipant": "'.$msg['author_name'].'"}');
            } else {
                $client->send(json_encode($msg));
            }
        }
    }

    /**
     * Handle a WebSocket connection closure.
     *
     * This method is called when a client disconnects. It performs cleanup:
     * - Removes the client from all rooms they were in
     * - Notifies remaining participants about the departure
     * - Removes participant tracking data
     * - Detaches the connection from the clients collection
     *
     * @param ConnectionInterface $conn The WebSocket connection that is closing.
    */
    public function onClose(ConnectionInterface $conn): void 
    {
        $is_obj_delited = false;
        foreach ($this->rooms as &$room) {
            foreach ($room as &$obj) {
                if ($obj->resourceId === $conn->resourceId) {
                    unset($obj);
                    $is_obj_delited = true;
                    break;
                }
                if ($is_obj_delited) {
                    break;
                }
            }
        }

        foreach ($this->rooms[$this->partisipants[$conn->resourceId]] as $client) {
            $client->send('{"left_partisipant": "'.$this->partisipant_names[$conn->resourceId].'"}');
        }
        unset($this->partisipant_names[$conn->resourceId]);

        $this->clients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    /**
     * Handle errors that occur on a WebSocket connection.
     *
     * This method logs the error, closes the problematic connection,
     * and prevents further communication with that client.
     *
     * @param ConnectionInterface $conn The WebSocket connection where the error occurred.
     * @param \Exception $e The exception that was thrown.
     */
    public function onError(ConnectionInterface $conn, \Exception $e): void 
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        Log::channel('cron')->error($e->getMessage());

        $conn->close();
    }
}
