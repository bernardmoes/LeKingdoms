<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class UseCmd extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function execute()
    {
        if (count($c) < 2) return $this->__communicator->sendReply($this->__message->getAuthorName(), "use [item]. e.g. use time turner");
        array_shift($c);
        $item = clean_note(implode(' ', $c));

        $hasitem = $this->__db->executeQuery('SELECT item, amountleft FROM items WHERE kingdom = "' . clean($user) . '" AND item = "'. clean_note($item) . '";');
        if (!$hasitem || $hasitem['amountleft'] <= 0) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you don't have any " . $item . " to use!");

        // has item

        $this->__db->executeQuery('UPDATE items SET amountleft = amountleft - 1 WHERE kingdom = "' . clean($user) . '" AND item = "'. clean_note($item) . '";');

        if ($item == 'time turner') {
            $result = $this->db->query('SELECT * FROM kingdom WHERE username = "' . clean($user) . '";');

            if ($result->num_rows > 0 && $kingdom = $result->fetch_assoc()) {

                $this->turn_kingdom($kingdom);

                $this->__communicator->sendReply($this->__message->getAuthorName(), "a magical dome encases the kingdom, accelerating local time. when the dome recedes you notice that a turn has passed. a report is prepared by your squire...");
                return $this->process_command("report", $user, $type);

            }

            return $this->__communicator->sendReply($this->__message->getAuthorName(), "you don't have a kingdom!");


        } else {
            return $this->__communicator->sendReply($this->__message->getAuthorName(), "using the item does nothing.");

        }


    }
}