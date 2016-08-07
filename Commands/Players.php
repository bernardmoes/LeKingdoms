<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Players extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function execute()
    {
        $result = $this->__db->executeQuery("SELECT username FROM kingdom WHERE locations <> \"\";")->fetchAll(PDO::FETCH_ASSOC);;

        $kingdoms = array();
        if ($result && count($result) > 0) {
            foreach($result as $kingdom)
            {
                $kingdoms[] = $kingdom['username'];
            }
        }
        return $this->__communicator->sendReply($this->__message->getAuthorName(), sprintf("currently active kingdoms: %s", implode(", ", $kingdoms)));
    }
}