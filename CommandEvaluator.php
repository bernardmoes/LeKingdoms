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
        'play' => 'play',
        'help' =>  'help',
        'cash' => 'cash',
        'money' => 'cash',
        'space' => 'space',
        'land' => 'space',
        'players' => 'players',
        'thieve' => 'steal',
        'steal' => 'steal',
        'spy' => 'spy',
        'espionage' => 'spy',
        'esp' => 'spy',
        'spells' => 'spells',
        'items' => 'items',
        'use' => 'useCmd',
        'cast' => 'cast',
        'trade' => 'trade',
        'buy' => 'trade',
        'sell' => 'trade',
        'buymax' => 'buymax',
        'sellall' => 'sellall',
        'gift' => 'gift',
        'give' => 'gift',
        'g' => 'gift',
        'stats' => 'stats',
        'stat' => 'stats',
        's' => 'stats',
        'attack' => 'attack',
        'build' => 'build',
        'b' => 'build',
        'buildmax' => 'buildmax',
        'bm' => 'buildmax',
        'raze' => 'raze',
        'buildings' => 'buildings',
        'annex' => 'annex',
        'obliterate' => 'obliterate',
        'autoannex' => 'autoannex',
        'yolo' => 'yolo',
        'turn' => 'turn',
        'destroy' => 'destroy',
        'selfdestruct' => 'selfdestruct',
        'fakeplay' => 'fakeplay',
        'nuke' => 'nuke',
        'rape' => 'rape',
        'bernanke' => 'bernanke',
        'greenspan' => 'greenspan',
        'obama' => 'obama',
        'godeye' => 'godeye',
        'protect' => 'protect',
        'unprotect' => 'unprotect',
        'abra' => 'abra',
        'doubletime' => 'doubletime',
        'dice' => 'dice',
        'roll' => 'dice',
        'dicetext' => 'dicetext',
        'dt' => 'dicetext',
        'rt' => 'dicetext',
        'checkturn' => 'checkturn',
        'report' => 'report'
    );

    /** @var DiscordMessage */
    private $_message;
    /** @var Command */
    private $_command;
    private $_kingdom;
    private $_communicator;

    public function __construct($kingdom, $communicator, $message)
    {
        $this->_kingdom = $kingdom;
        $this->_communicator = $communicator;
        $this->_message = $message;
    }

    public function evaluateCommand()
    {
        if(array_key_exists($this->_message->getContentArgs()[0], $this->_commands))
        {
            $this->{$this->_commands[$this->_message->getContentArgs()[0]]}();
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

    public function getMessage()
    {
        return $this->_message;
    }

    public function getKingdom()
    {
        return $this->_kingdom;
    }

    public function getCommunicator()
    {
        return $this->_communicator;
    }

    public function unknownCommand()
    {

    }

    public function play()
    {
        $this->_command = new Play($this);
    }

    public function help()
    {
        $this->_command = new Help($this);
    }

    public function cash()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Cash($this);
        }
    }

    public function space()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Space($this);
        }
    }

    public function players()
    {
        $this->_command = new Players($this);
    }

    public function steal()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Steal($this);
        }
    }

    public function spy()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Spy($this);
        }
    }

    public function spells()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Spells($this);
        }
    }

    public function items()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Items($this);
        }
    }

    public function useCmd()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new UseCmd($this);
        }
    }

    public function cast()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Cast($this);
        }
    }

    public function trade()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Trade($this);
        }
    }

    public function buymax()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new BuyMax($this);
        }
    }

    public function sellall()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new SellAll($this);
        }
    }

    public function gift()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Gift($this);
        }
    }

    public function stats()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Stats($this);
        }
    }

    public function attack()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Attack($this);
        }
    }

    public function build()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Build($this);
        }
    }

    public function buildmax()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new BuildMax($this);
        }
    }

    public function raze()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Raze($this);
        }
    }

    public function buildings()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Buildings($this);
        }
    }

    public function annex()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Annex($this);
        }
    }

    public function obliterate()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Obliterate($this);
        }
    }

    public function autoannex()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new AutoAnnex($this);
        }
    }

    public function yolo()
    {
        if($this->assertHasKingdom())
        {
            $this->_command = new Yolo($this);
        }
    }

    public function turn()
    {
        if(($this->_message->isAdmin() || $this->_message->isAuthorizedBot()))
        {
            $this->_command = new Turn($this);
        }
    }

    public function destroy()
    {
        if($this->_message->isAdmin())
        {
            $this->_command = new Destroy($this);
        }
    }

    public function selfdestruct()
    {
        $this->_command = new SelfDestruct($this);
    }

    public function fakeplay()
    {
        if($this->_message->isAdmin())
        {
            $this->_command = new FakePlay($this);
        }
    }

    public function nuke()
    {
        if($this->_message->isAdmin())
        {
            $this->_command = new Nuke($this);
        }
    }

    public function rape()
    {
        if($this->_message->isAdmin())
        {
            $this->_command = new Rape($this);
        }
    }

    public function bernanke()
    {
        if($this->_message->isAdmin() && $this->assertHasKingdom())
        {
            $this->_command = new Bernanke($this);
        }
    }

    public function greenspan()
    {
        if($this->_message->isAdmin() && $this->assertHasKingdom())
        {
            $this->_command = new GreenSpan($this);
        }
    }

    public function obama()
    {
        if($this->_message->isAdmin() && $this->assertHasKingdom())
        {
            $this->_command = new Obama($this);
        }
    }

    public function godeye()
    {
        if($this->_message->isAdmin())
        {
            $this->_command = new Godeye($this);
        }
    }

    public function protect()
    {
        if($this->_message->isAdmin())
        {
            $this->_command = new Protect($this);
        }
    }

    public function unprotect()
    {
        if($this->_message->isAdmin())
        {
            $this->_command = new UnProtect($this);
        }
    }

    public function abra()
    {
        if($this->_message->isAdmin() && $this->assertHasKingdom())
        {
            $this->_command = new Abra($this);
        }
    }

    public function doubletime()
    {
        if($this->_message->isAdmin())
        {
            $this->_command = new DoubleTime($this);
        }
    }

    public function dice()
    {
        $this->_command = new Dice($this);
    }

    public function dicetext()
    {
        $this->_command = new DiceText($this);
    }

    public function checkturn()
    {
        $this->_command = new CheckTurn($this);
    }

    public function report()
    {
        $this->_command = new Report($this);
    }




}