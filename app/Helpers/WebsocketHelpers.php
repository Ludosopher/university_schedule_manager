<?php

namespace App\Helpers;

use App\ReplacementRequestMessage;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebsocketHelpers implements MessageComponentInterface {
    protected $clients;
    protected $rooms;
    protected $partisipants;
    protected $partisipant_names;
    protected $existing_messages;
    
    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->rooms = [];
        $this->partisipants = [];
        $this->partisipant_names = [];
        $this->existing_messages = [];

    }

    public function onOpen(ConnectionInterface $conn) {
        
        $this->clients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {

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
            ModelHelpers::addOrUpdate($msg, 'App\ReplacementRequestMessage');
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

    public function onClose(ConnectionInterface $conn) {
       
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

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        Log::channel('cron')->error($e->getMessage());

        $conn->close();
    }
}
