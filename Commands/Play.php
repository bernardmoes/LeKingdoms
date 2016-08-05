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

    public function makeNewPlayer($u) {
        $u = clean($u);

        $details = $this->q('SELECT username, locations FROM kingdom WHERE username = "' . clean($u) . '";');

        if ($details['locations'] == "") {

            $this->q('DELETE FROM kingdom WHERE username = "' . clean($u) . '";');
            //$this->q('DELETE FROM spells WHERE castby = "' . clean($u) . '" OR caston = "' . clean($u) . '";');
            $details = false;
        }
        if ($details === false) {


            $alreadynewplayer = $this->q("SELECT * FROM spells WHERE castby = \"" . clean($u) . "\" AND caston = \"" . clean($u) . "\" AND spell = \"(newplayer)\" LIMIT 1;");
            if ($alreadynewplayer) {
                return "you've already created a new kingdom this turn. your people grow weary of being raped and murdered and cannot be compelled to form a new kingdom until next turn";
            } else {


                $this->q('DELETE FROM spells WHERE castby = "' . clean($u) . '" OR caston = "' . clean($u) . '";');
                $this->q('DELETE FROM items WHERE kingdom = "' . clean($u) );
                $this->q('DELETE FROM turnnotes WHERE fromuser = "' . clean($u) . '" OR touser = "' . clean($u) . '";');

                $loc = $this->random_location();
                while ($this->get_username_at_location($loc)) $loc = $this->random_location();

                $this->both($u, "creating new kingdom for " . $u . " at " . $loc . ". welcome to kingdoms!.");

                $this->q(
                    'INSERT INTO kingdom (username, locations, L, G, I, H, P, S,FA, BT, WO, R, D) VALUES ("' . clean($u) . '", "' . $loc . '", ' . self::$LPK . ', ' . self::$SG . ', ' . self::$SI . ', ' . self::$SH . ',' . self::$SP . ',' . self::$SS . ',' . self::$SFA . ',' . self::$SBT. ', ' . self::$SW . ', ' . self::$SR . ', ' . self::$SD . ');'
                );
                $details = $this->q('SELECT username, locations FROM kingdom WHERE username = "' . clean($u) . '";');
                $this->both($u, "to play, type commands here. check out the play guide for detailed help");

                $this->q("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($u) . "\", \"" . clean($u) . "\", \"(newplayer)\", 1);");
                $this->q("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($u) . "\", \"" . clean($u) . "\", \"protection\", 5);");
                $this->q("INSERT INTO items (kingdom, item, amountleft) VALUES (\"" . clean($u) . "\", \"time turner\",  20);");

            }


        }  else {
            $this->both($u, "welcome " . $u . "! your kingdom is at the following location(s): " . $details['locations'] );
            $this->both($u, "to play, you may either type commands in /kingdoms, or you can private message me. type !help for commands");
        }
    }

    function execute()
    {
        $this->reply($user, $p, $this->makeNewPlayer($user));
    }
}