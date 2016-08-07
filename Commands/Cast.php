<?php
/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 19:41
 */
class Cast extends Command
{
    public function __construct(CommandEvaluator $evaluator)
    {
        parent::__construct($evaluator);
    }

    function castNow($spell, $victim, $attacker)
    {
        $victim = strtolower(clean($victim));
        $attacker = strtolower(clean($attacker));
        if (!isset(KingdomHelper::$spells[$spell])) return "not a valid spell. try !spells for a list";
        $s = KingdomHelper::$spells[$spell];
        $v = $this->__db->getKingdom($victim);
        if ($v === false) return $v . " does not have a kingdom";
        $a = $this->__db->getKingdom($attacker);
        if ($a === false) return $a . " cannot cast a spell because he/she does not own a kingdom. try !play";


        if ($victim != $attacker) {
            $protectedplayer = $this->__db->executeQuery("SELECT * FROM spells WHERE castby = \"" . clean($victim) . "\" AND caston = \"" . clean($victim) . "\" AND spell = \"protection\" LIMIT 1;")->fetch(PDO::FETCH_ASSOC);
            if ($protectedplayer !== false) return "spell was unsuccessful, as " . $victim. " is magically protected for " . $protectedplayer['duration'] . " more turns.";

            $protectedplayer = $this->__db->executeQuery("SELECT * FROM spells WHERE caston = \"" . clean($attacker) . "\" AND spell = \"protection\" LIMIT 1;")->fetch(PDO::FETCH_ASSOC);;
            if ($protectedplayer !== false) return "you cannot cast spells on other kingdoms while under a shield";
        }

        if ($a['M'] < $s['r']) return "you do not have enough magical runes to cast this spell!";

        if ($spell == 'shield') $spell = 'protection';

        if ($spell == 'protection' && $victim != $attacker) return "you can only cast protection on yourself";


        $r = $this->__db->executeQuery("SELECT * FROM spells WHERE castby = \"" . clean($attacker) . "\" AND caston = \"" . clean($victim) . "\" AND spell = \"" . clean($spell) . "\" LIMIT 1;")->fetch(PDO::FETCH_ASSOC);;
        if ($r !== false) return "you've already cast this spell on " . $victim . " for this turn, please wait until the spell runs out before re-casting it.";

        $this->__db->executeQuery("INSERT INTO spells (castby, caston, spell, duration) VALUES (\"" . clean($attacker) . "\", \"" . clean($victim) . "\", \"" . clean($spell) . "\", " . intval($s['l']) . ");");


        $a['M'] -= $s['r'];
        $this->__db->saveKingdom($a);
        $this->__communicator->sendPublic($attacker . " cast " . $spell . " on " . $victim . ". it will be active for " . $s['l'] . " turns");
        KingdomHelper::add_turn_note($attacker, $victim, $spell . " was cast on your kingdom by " . $attacker);
        return $spell . " was cast on " . $victim . " successfully";
    }

    function execute()
    {
        $c = $this->__message->getContentArgs();
        if (count($c) < 3) return$this->__communicator->sendReply($this->__message->getAuthorName(), "you can cast a spell like so: !cast fire nn:mm, or !cast fire username. for a list of spells try !spells");
        $loc =  KingdomHelper::resolve_location_from_input($c[2]);
        if ($loc === false) return$this->__communicator->sendReply($this->__message->getAuthorName(), "cannot cast a spell on " . $c[2] . (strrpos($loc, ":") === false ? ", user does not have a kingdom" : " there is no kingdom there."));
        $c[1] = clean($c[1]);
        $c[2] = clean($c[2]);
        if (!isset(KingdomHelper::$spells[$c[1]])) return$this->__communicator->sendReply($this->__message->getAuthorName(), "spell " . $c[1] . " is not a valid spell. try !spells for a list");
       $this->__communicator->sendReply($this->__message->getAuthorName(), $this->castNow($c[1], $c[2], $this->__message->getAuthorName()));
    }



}