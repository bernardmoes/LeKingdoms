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

    /** @var DiscordMessage */
    private $_message;
    /** @var Command */
    private $_command;
    private $_kingdom;
    private $_communicator;
    public function __construct($kingdom, $communicator)
    {
        $this->_kingdom = $kingdom;
        $this->_communicator = $communicator;
    }

    public function evaluateCommand(DiscordMessage $message)
    {
        $this->_message = $message;
        if(array_key_exists($this->_message->getContentArgs()[0], $this->_commands))
        {
            eval('return $this->'.$this->_commands[$this->_message->getContentArgs()[0]].';');
        } else {
            $this->unknownCommand();
        }

        $this->executeCommand();
        return;
    }

    public function executeCommand()
    {
        if($this->_command != null)
        {
            $result = $this->_command->execute();
        }
    }

    public function assertHasKingdom()
    {
        if ($this->_kingdom === false)
        {
            $this->_communicator->sendReply($this->_message->getAuthorName(), "you don't own a kingdom. try !play");
            return false;
        }
        return true;
    }

    public function unknownCommand()
    {

    }

    public function play()
    {
        $this->_command = new Play($this->_message, $this->_kingdom, $this->_communicator);
    }

    public function help()
    {
        $this->_command = new Help($this->_message, $this->_kingdom, $this->_communicator);
    }

    public function cash()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Cash($this->_message, $this->_kingdom, $this->_communicator);
        }
    }

    public function space()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Space($this->_message, $this->_kingdom, $this->_communicator);
        }
    }

    public function players()
    {
        $this->_command = new Players($this->_message, $this->_kingdom, $this->_communicator);
    }

    public function steal()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Steal($this->_message, $this->_kingdom, $this->_communicator);
        }
    }

    public function spy()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Spy($this->_message, $this->_kingdom, $this->_communicator);
        }
    }

    public function spells()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Spells($this->_message, $this->_kingdom, $this->_communicator);
        }
    }

    public function items()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Items($this->_message, $this->_kingdom, $this->_communicator);
        }
    }

    public function useCmd()
    {
        if($this->assertHasKingdom())
        {
            
        }
    }

    public function cast()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function trade()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function buymax()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function sellall()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function gift()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function stats()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function attack()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function build()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function buildmax()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function raze()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function buildings()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function annex()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function obliterate()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function autoannex()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function yolo()
    {
        if($this->assertHasKingdom())
        {

        }
    }

    public function turn()
    {
        if(($this->_message->isAdmin() || $this->_message->isAuthorizedBot()))
        {
            $this->_command = new Turn($this->_message, $this->_kingdom, $this->_communicator);
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
        $this->_command = new SelfDestruct($this->_message, $this->_kingdom, $this->_communicator);
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
        if($this->_message->isAdmin() && $this->assertHasKingdom())
        {

        }
    }

    public function greenspan()
    {
        if($this->_message->isAdmin() && $this->assertHasKingdom())
        {

        }
    }

    public function obama()
    {
        if($this->_message->isAdmin() && $this->assertHasKingdom())
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
        if($this->_message->isAdmin() && $this->assertHasKingdom())
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
        $this->_command = new Dice($this->_message, $this->_kingdom, $this->_communicator);
    }

    public function dicetext()
    {
        $this->_command = new DiceText($this->_message, $this->_kingdom, $this->_communicator);
    }

    public function checkturn()
    {
        $this->_command = new CheckTurn($this->_message, $this->_kingdom, $this->_communicator);
    }

    public function report()
    {
        $this->_command = new Report($this->_message, $this->_kingdom, $this->_communicator);
    }




}