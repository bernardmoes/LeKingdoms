<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class UseCmd extends Command
{
    public function __construct($message, $user)
    {
        parent::__construct($message, $user);
    }

    function execute()
    {
        if (count($c) < 2) return $this->reply($user,$p, "use [item]. e.g. use time turner");
        array_shift($c);
        $item = clean_note(implode(' ', $c));

        $hasitem = $this->q('SELECT item, amountleft FROM items WHERE kingdom = "' . clean($user) . '" AND item = "'. clean_note($item) . '";');
        if (!$hasitem || $hasitem['amountleft'] <= 0) return $this->reply($user,$p, "you don't have any " . $item . " to use!");

        // has item

        $this->q('UPDATE items SET amountleft = amountleft - 1 WHERE kingdom = "' . clean($user) . '" AND item = "'. clean_note($item) . '";');

        if ($item == 'time turner') {
            $result = $this->db->query('SELECT * FROM kingdom WHERE username = "' . clean($user) . '";');

            if ($result->num_rows > 0 && $kingdom = $result->fetch_assoc()) {

                $this->turn_kingdom($kingdom);

                $this->reply($user,$p, "a magical dome encases the kingdom, accelerating local time. when the dome recedes you notice that a turn has passed. a report is prepared by your squire...");
                return $this->process_command("report", $user, $type);

            }

            return $this->reply($user,$p, "you don't have a kingdom!");


        } else {
            return $this->reply($user,$p, "using the item does nothing.");

        }


    }
}