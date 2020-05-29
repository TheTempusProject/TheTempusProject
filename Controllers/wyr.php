<?php
/**
 * Controllers/wyr.php
 *
 * This is the would you rather controller.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 *
 * @link    https://TheTempusProject.com
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Controllers;

use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Issue as Issue;
use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Hash as Hash;
use TempusProjectCore\Classes\Code as Code;

class Wyr extends Controller
{
    public function __construct()
    {
        self::$template->noIndex();
        if (!self::$isMember) {
            Issue::error('You do not have permission to view this page.');
            exit();
        }
    }
    
    public function __destruct()
    {
        Debug::log('Controller Destructing: ' . get_class($this));
        self::$session->updatePage(self::$title);
        $this->build();
        Debug::closeAllGroups();
    }

    public function index()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Would You rather?';
        $this->view('wyr.select');
        exit();
    }

    public function createdeck()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Create Deck - {SITENAME}';
        self::$pageDescription = '';
        if (!Input::exists()) {
            $this->view('wyr.create.deck');
            exit();
        }
        // if (!Check::form('createDeck')) {
        //     Issue::error('There was an error creating your deck.', Check::userErrors());
        //     $this->view('wyr.create.deck');
        //     exit();
        // }
        self::$wyrDeck->create(self::$activeUser->ID, Input::post('title'), Input::post('entry'));
        Redirect::to('wyr/index');
        Session::flash('success', 'Your deck has been created.');
    }

    public function createcard()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Create Card - {SITENAME}';
        self::$pageDescription = '';
        $decks = self::$template->standardView('wyr.deck.select', self::$wyrDeck->list());
        self::$template->set('deckSelect', $decks);
        if (!Input::exists()) {
            $this->view('wyr.create.card');
            exit();
        }
        // if (!Check::form('createDeck')) {
        //     Issue::error('There was an error creating your deck.', Check::userErrors());
        //     $this->view('wyr.create.deck');
        //     exit();
        // }
        self::$wyr->create(self::$activeUser->ID, intval(Input::post('deckSelect')), Input::post('entry'));
        Redirect::to('wyr/index');
        Session::flash('success', 'Your Card has been created.');
    }

    public function viewdecks()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Would You rather?';
        $this->view('wyr.decks');
        exit();
    }

    public function viewcards()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Would You rather?';
        $this->view('wyr.card');
        exit();
    }

    public function play()
    {
        Debug::log("Controller initiated: " . __METHOD__ . ".");
        self::$title = 'Would You rather?';
        $decks = self::$template->standardView('wyr.deck.select', self::$wyrDeck->list());
        self::$template->set('deckSelect', $decks);
        if (!Input::exists()) {
            $this->view('wyr');
            exit();
        }
        self::$template->set('deck_title', self::$wyrDeck->get(intval(Input::post('deckSelect')))[0]->title);
        $max = self::$wyrDeck->countCards(intval(Input::post('deckSelect')));
        $randomID = intval(rand(1,$max));
        // echo var_export(self::$wyr->get($randomID)[0],true);
        // exit;
        $randomCard = self::$wyr->getRandFromDeck(intval(Input::post('deckSelect')), $randomID);
        $this->view('wyr.card', $randomCard);
        $this->view('wyr');
        exit();
    }
}
