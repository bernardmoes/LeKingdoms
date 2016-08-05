<?php
/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:41
 */
class Cast extends Command
{
    public function __construct($message, $kingdom, $communicator)
    {
        parent::__construct($message, $kingdom, $communicator);
    }

    function castNow()
    {
        $victim = clean($victim);
        $attacker = clean($attacker);
        if (!isset(self::$spells[$spell])) return "not a valid spell. try !spells for a list";
        $s = self::$spells[$spell];
        $v = $this->get_kingdom($victim);
        if ($v === false) return $v . " does not have a kingdom";
        $a = $this->get_kingdom($attacker);
        if ($a === false) return $a . " cannot cast a spell because he/she does not own a kingdom. try !play";


        if ($victim != $attacker) {
            $protectedplayer = $this->q("SELECT * FROM spells WHERE castby = \"" . clean($victim) . "\" AND caston = \"" . clean($victim) . "\" AND spell = \"protection\" LIMIT 1;");
            if ($protectedplayer !== false) return "spell was unsuccessful, as " . $victim. " is magically protected for " . $protectedplayer['duration'] . " more turns.";

            $protectedplayer = $this->q("SELECT * FROM spells WHERE caston = \"" . clean($attacker) . "\" AND spell = \"protection\" LIMIT 1;");
            if ($protectedplayer !== false) return "you cannot cast spells on other kingdoms while under a shield";


        }




        if ($a['M'] < $s['r']) return "you do not have enough magical runes to cast this spell!";

        if ($spell == 'shield') $spell = 'protection';

        if ($spell == 'protection' && $victim != $attacker) return "you can only cast protection on yourself";


        $r = $this->q("SELECT * FROM spells WHERE castby = \"" . clean($attacker) . "\" AND caston = \"" . clean($victim) . "\" AND spell = \"" . clean($spell) . "\" LIMIT 1;");
        if ($r !== false) return "you've already cast this spell on " . $victim . " for this turn, please wait until the spell runs out before re-casting it.";

        $this->q("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($attacker) . "\", \"" . clean($victim) . "\", \"" . clean($spell) . "\", " . intval($s['l']) . ");");


        $a['M'] -= $s['r'];
        $this->save_kingdom($a);
        $this->room($attacker . " cast " . $spell . " on " . $victim . ". it will be active for " . $s['l'] . " turns");
        $this->add_turn_note($attacker, $victim, $spell . " was cast on your kingdom by " . $attacker);
        return $spell . " was cast on " . $victim . " successfully";
    }

    function execute()
    {
        if (count($c) < 3) return $this->reply($user,$p, "you can cast a spell like so: !cast fire nn:mm, or !cast fire username. for a list of spells try !spells");
        $loc =  $this->resolve_location_from_input($c[2]);
        if ($loc === false) return $this->reply($user,$p, "cannot cast a spell on " . $c[2] . (strrpos($loc, ":") === false ? ", user does not have a kingdom" : " there is no kingdom there."));
        $c[1] = clean($c[1]);
        $c[2] = clean($c[2]);
        if (!isset(self::$spells[$c[1]])) return $this->reply($user,$p, "spell " . $c[1] . " is not a valid spell. try !spells for a list");
        $this->reply($user,$p, $this->cast($c[1], $c[2], $user));
    }



}