<?php
require_once "Models/DiscordMessage.php";
/**
 * Created by PhpStorm.
 * User: Bernard
 * Date: 4-8-2016
 * Time: 22:56
 */
class CommandEvaluator
{
    private $_commands = array(
        'play' => 'play()', 'help' => 'help()', 'cash' => 'cash()', 'money' => 'cash()', 'space' => 'space()', 'land' => 'space()',
        'players' => 'players()', 'thieve' => 'steal()', 'steal' => 'steal()', 'spy' => 'spy()', 'espionage' => 'spy()',
        'esp' => 'spy()', 'spells' => 'spells()', 'items' => 'items()', 'use' => 'useCmd()', 'cast' => 'cast()',
        'trade' => 'trade()', 'buy' => 'trade()', 'sell' => 'trade()', 'buymax' => 'buymax()', 'sellall' => 'sellall()',
        'gift' => 'gift()', 'give' => 'gift()', 'g' => 'gift()', 'stats' => 'stats()', 'stat' => 'stats()',
        's' => 'stats()', 'attack' => 'attack()', 'build' => 'build()', 'b' => 'build()', 'buildmax' => 'buildmax()', 'bm' => 'buildmax()',
        'raze' => 'raze()', 'buildings' => 'buildings()', 'annex' => 'annex()', 'obliterate' => 'obliterate()', 'autoannex' => 'autoannex()',
        'yolo' => 'yolo()', 'turn' => 'turn()', 'destroy' => 'destroy()', 'selfdestruct' => 'selfdestruct()', 'fakeplay' => 'fakeplay()',
        'nuke' => 'nuke()', 'rape' => 'rape()', 'bernanke' => 'bernanke()', 'greenspan' => 'greenspan()', 'obama' => 'obama()',
        'godeye' => 'godeye()', 'protect' => 'protect()', 'unprotect' => 'unprotect()', 'abra' => 'abra()', 'doubletime' => 'doubletime()',
        'dice' => 'dice()', 'roll' => 'dice()', 'dicetext' => 'dicetext()', 'dt' => 'dicetext()', 'rt' => 'dicetext()',
        'checkturn' => 'checkturn()', 'report' => 'report()'
    );

    private $_message;

    public function evaluateCommand(DiscordMessage $message)
    {
        $this->_message = $message;
        if(array_key_exists($this->_message->getContentArgs()[0], $this->_commands))
        {
            eval('return $this->'.$this->_commands[$this->_message->getContentArgs()[0]].';');
        } else {
            $this->unknownCommand();
        }
    }

    public function unknownCommand()
    {

    }

    public function play()
    {
        echo "play!";
    }

    public function help()
    {

    }

    public function cash()
    {

    }

    public function space()
    {

    }

    public function players()
    {

    }

    public function steal()
    {

    }

    public function spy()
    {

    }

    public function spells()
    {

    }

    public function items()
    {

    }

    public function useCmd()
    {

    }

    public function cast()
    {

    }

    public function trade()
    {

    }

    public function buymax()
    {

    }

    public function sellall()
    {

    }

    public function gift()
    {

    }

    public function stats()
    {

    }

    public function attack()
    {

    }

    public function build()
    {

    }

    public function buildmax()
    {

    }

    public function raze()
    {

    }

    public function buildings()
    {

    }

    public function annex()
    {

    }

    public function obliterate()
    {

    }

    public function autoannex()
    {

    }

    public function yolo()
    {

    }

    public function turn()
    {
        if($this->_message->isAdmin() || $this->_message->isAuthorizedBot())
        {

        }
    }

    public function destroy()
    {
        if($this->_message->isAdmin())
        {

        }
    }

    public function selfdestruct()
    {

    }

    public function fakeplay()
    {
        if($this->_message->isAdmin())
        {

        }
    }

    public function nuke()
    {
        if($this->_message->isAdmin())
        {

        }
    }

    public function rape()
    {
        if($this->_message->isAdmin())
        {

        }
    }

    public function bernanke()
    {
        if($this->_message->isAdmin())
        {

        }
    }

    public function greenspan()
    {
        if($this->_message->isAdmin())
        {

        }
    }

    public function obama()
    {
        if($this->_message->isAdmin())
        {

        }
    }

    public function godeye()
    {
        if($this->_message->isAdmin())
        {

        }
    }

    public function protect()
    {
        if($this->_message->isAdmin())
        {

        }
    }

    public function unprotect()
    {
        if($this->_message->isAdmin())
        {

        }
    }

    public function abra()
    {
        if($this->_message->isAdmin())
        {

        }
    }

    public function doubletime()
    {
        if($this->_message->isAdmin())
        {

        }
    }

    public function dice()
    {

    }

    public function dicetext()
    {

    }

    public function checkturn()
    {

    }

    public function report()
    {

    }




}