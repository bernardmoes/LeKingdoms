<?php

/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:43
 */
class Play extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }


    function execute()
    {
        $u = clean($this->__message->getAuthorName());

        $details = $this->__db->executeQuery('SELECT username, locations FROM kingdom WHERE username = "' . clean($u) . '";')->fetch(PDO::FETCH_ASSOC);;

        if ($details['locations'] == "") {

            $this->__db->executeQuery('DELETE FROM kingdom WHERE username = "' . clean($u) . '";');
            $details = false;
        }

        if ($details === false) {
            $alreadynewplayer = $this->__db->executeQuery("SELECT * FROM spells WHERE castby = \"" . clean($u) . "\" AND caston = \"" . clean($u) . "\" AND spell = \"(newplayer)\" LIMIT 1;")->fetchAll(PDO::FETCH_ASSOC);;
            if ($alreadynewplayer) {
                $this->__communicator->sendReply($u, "you've already created a new kingdom this turn. your people grow weary of being raped and murdered and cannot be compelled to form a new kingdom until next turn");
            } else {
                $this->__db->executeQuery('DELETE FROM spells WHERE castby = "' . clean($u) . '" OR caston = "' . clean($u) . '";');
                $this->__db->executeQuery('DELETE FROM items WHERE kingdom = "' . clean($u) );
                $this->__db->executeQuery('DELETE FROM turnnotes WHERE fromuser = "' . clean($u) . '" OR touser = "' . clean($u) . '";');

                $loc = KingdomHelper::random_location();
                while (KingdomHelper::get_username_at_location($loc)) $loc = KingdomHelper::random_location();

                $this->__communicator->sendBoth($u, sprintf("creating new kingdom for %s at %s. welcome to kingdoms!.", $u, $loc));
                $this->__db->executeQuery(
                    'INSERT INTO kingdom (username, locations, L, G, I, H, P, S,FA, BT, WO, R, D) VALUES ("' . clean($u) . '", "' . $loc . '", ' . LAND_PER_KINGDOM . ', ' . START_GOLD . ', ' . START_IRON . ', ' . START_HOUSES . ',' . START_POPULATION . ',' . START_SOLDIERS . ',' . START_FARMS . ',' . START_BATTLEMENTS. ', ' . START_WOOD . ', ' . START_STONE . ', ' . START_DAMS . ');'
                );
                $details = $this->__db->executeQuery('SELECT username, locations FROM kingdom WHERE username = "' . clean($u) . '";')->fetch(PDO::FETCH_ASSOC);;
                $this->__communicator->sendBoth($u, "to play, type commands here. check out the play guide for detailed help");

                $this->__db->executeQuery("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($u) . "\", \"" . clean($u) . "\", \"(newplayer)\", 1);");
                $this->__db->executeQuery("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($u) . "\", \"" . clean($u) . "\", \"protection\", 5);");
                $this->__db->executeQuery("INSERT INTO items (kingdom, item, amountleft) VALUES (\"" . clean($u) . "\", \"time turner\",  20);");
            }
        }  else {
            $this->__communicator->sendBoth($u, sprintf("welcome %s! your kingdom is at the following location(s): %s", $u, $details['locations']));
            $this->__communicator->sendBoth($u, "to play, you may either type commands in /kingdoms, or you can private message me. type !help for commands");
        }
    }
}