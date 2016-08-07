<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:44
 */
class UseCmd extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $c = $this->__message->getContentArgs();
        if (count($c) < 2) return $this->__communicator->sendReply($this->__message->getAuthorName(), "use [item]. e.g. use time turner");
        array_shift($c);
        $item = clean_note(implode(' ', $c));

        $hasitem = $this->__db->executeQuery('SELECT item, amountleft FROM items WHERE kingdom = "' . clean($this->__message->getAuthorName()) . '" AND item = "'. clean_note($item) . '";')->fetch(PDO::FETCH_ASSOC);
        if (!$hasitem || $hasitem['amountleft'] <= 0) return $this->__communicator->sendReply($this->__message->getAuthorName(), "you don't have any " . $item . " to use!");

        // has item

        $this->__db->executeQuery('UPDATE items SET amountleft = amountleft - 1 WHERE kingdom = "' . clean($this->__message->getAuthorName()) . '" AND item = "'. clean_note($item) . '";');

        if ($item == 'time turner') {
            $result = $this->__db->executeQuery('SELECT * FROM kingdom WHERE username = "' . clean($this->__message->getAuthorName()) . '";')->fetchAll(PDO::FETCH_ASSOC);

            if (count($result) > 0)
            {
                (new Turn($this->__commandEvaluator))->turn_kingdom($this->__kingdom);

                $this->__communicator->sendReply($this->__message->getAuthorName(), "a magical dome encases the kingdom, accelerating local time. when the dome recedes you notice that a turn has passed. a report is prepared by your squire...");

                $this->__commandEvaluator->report();
                return $this->__commandEvaluator->executeCommand();
            }

            return $this->__communicator->sendReply($this->__message->getAuthorName(), "you don't have a kingdom!");


        } else {
            return $this->__communicator->sendReply($this->__message->getAuthorName(), "using the item does nothing.");

        }


    }
}