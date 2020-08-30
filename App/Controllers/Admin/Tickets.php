<?php
/**
 * Controllers/Admin/Tickets.php
 *
 * This is the tickets controller.
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

use TempusProjectCore\Classes\Debug;
use TempusProjectCore\Classes\Issue;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Check;
use TheTempusProject\Controllers\AdminController;

require_once 'AdminController.php';

class Tickets extends AdminController
{
    protected static $ticket;
    protected static $user;
    protected static $comment;

    public function __construct()
    {
        parent::__construct();
        Debug::log('Controller Constructing: ' . get_class($this));
        self::$title = 'Admin - Email Subscribers';
        $this->view('navigation.ticket');
        self::$ticket = $this->model('ticket');
        self::$user = $this->model('user');
        self::$comment = $this->model('comment');
    }
    public function index($data = null)
    {
        Debug::log('Controller initiated: '.__METHOD__.'.');
        self::$title = 'Admin - Tickets';
        $this->view('admin.ticketList', self::$ticket->listTickets());
        exit();
    }
    public function status($data = null)
    {
        $this->view('navigation.ticketStatus');
        if (!empty($data)) {
            $this->view('admin.ticketList', self::$ticket->listTickets('status', $data));
        } else {
            $this->view('admin.ticketList', self::$ticket->listTickets('newest'));
        }
        exit();
    }
    public function category($data = null)
    {
        $this->view('navigation.ticketCategory');
        if (!empty($data)) {
            $this->view('admin.ticketList', self::$ticket->listTickets('category', $data));
        } else {
            $this->view('admin.ticketList', self::$ticket->listTickets('newest'));
        }
        exit();
    }
    public function project($data = null)
    {
        $this->view('navigation.ticketProject');
        if (!empty($data)) {
            $this->view('admin.ticketList', self::$ticket->listTickets('project', $data));
        } else {
            $this->view('admin.ticketList', self::$ticket->listTickets('newest'));
        }
        exit();
    }
    public function newest($data = null)
    {
        $this->view('admin.ticketList', self::$ticket->listTickets('newest'));
        exit();
    }
    public function oldest($data = null)
    {
        $this->view('admin.ticketList', self::$ticket->listTickets('oldest'));
        exit();
    }
    public function listTickets($data = null, $data2 = null)
    {
        $this->view('admin.ticketList', self::$ticket->listTickets($data, $data2));
        exit();
    }
    public function viewTicket($data = null)
    {
        $ticketData = self::$ticket->get($data);
        if ($ticketData !== false) {
            if (Input::exists('submit')) {
                if (!Check::form('newComment')) {
                    Issue::error('There was a problem posting your comment.', Check::userErrors());
                } elseif (self::$comment->create('ticket', $ticketData->ID, Input::post('comment'))) {
                    Issue::success('Comment posted');
                } else {
                    Issue::error('There was an error posting you comment, please try again.');
                }
            }
            self::$template->set('NEWCOMMENT', self::$template->standardView('commentNew'));
            self::$template->set('count', self::$comment->count('ticket', $ticketData->ID));
            self::$template->set('COMMENTS', self::$template->standardView('commentList', self::$comment->display(25, 'ticket', $ticketData->ID)));
            $this->view('admin.ticketView', $ticketData);
            exit();
        }
        Issue::error('Ticket not found.');
        $this->index();
        // self::$comment->create('ticket', $post->ID, Input::post('comment'))) {
    }
    public function newTicket($data = null)
    {
        if (Input::exists('submit')) {
            if (self::$ticket->create()) {
                Issue::success('Ticket created');
            }
        } else {
            self::$template->set('categorySelect', self::$template->standardView('admin.ticketSelectCategory'));
            self::$template->set('projectSelect', self::$template->standardView('admin.ticketSelectProject'));
            $this->view('admin.ticketNew');
            exit();
        }
        $this->index();
    }
    public function edit($data = null)
    {
        if (Input::exists('submit')) {
            if (self::$ticket->update($data)) {
                Issue::success('Ticket updated');
            }
        } else {
            $ticket = self::$ticket->get($data);
            self::$template->selectOption($ticket->project);
            self::$template->selectOption($ticket->category);
            self::$template->selectOption($ticket->status);
            self::$template->set('categorySelect', self::$template->standardView('admin.ticketSelectCategory'));
            self::$template->set('projectSelect', self::$template->standardView('admin.ticketSelectProject'));
            self::$template->set('statusSelect', self::$template->standardView('admin.ticketSelectStatus'));
            $this->view('admin.ticketEdit', $ticket);
            exit();
        }
        $this->index();
    }
}
