<?php
/**
 * Models/model_message.php.
 *
 * Houses all of the functions for the core messaging system.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/TheTempusProject
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 *
 * @todo  no ISUUES in models
 */

namespace TheTempusProject\Models;

use TempusProjectCore\Classes\Check as Check;
use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Config as Config;
use TempusProjectCore\Classes\DB as DB;
use TempusProjectCore\Classes\Session as Session;
use TempusProjectCore\Classes\Cookie as Cookie;
use TempusProjectCore\Classes\Log as Log;
use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Email as Email;
use TempusProjectCore\Classes\Issue as Issue;
use TempusProjectCore\Classes\Sanitize as Sanitize;

class model_message extends Controller
{
    private $_messages;
    private $_usernames;
    public function __construct()
    {
        Debug::log('Model Constructed: '.get_class($this));
        Debug::gend();
    }
    public static function sql() 
    {
        $query = "CREATE TABLE `messages` (
  `ID` int(11) NOT NULL,
  `user_to` int(5) NOT NULL COMMENT 'User ID of the message recipient',
  `user_from` int(5) NOT NULL COMMENT 'User ID of the message sender',
  `message` text NOT NULL COMMENT 'Contents of the message',
  `subject` text NOT NULL COMMENT 'Subject of the message',
  `sent` int(10) NOT NULL COMMENT 'Time the message was sent',
  `sender_deleted` int(1) NOT NULL DEFAULT '0' COMMENT 'has the sender deleted the message',
  `reciever_deleted` int(1) NOT NULL DEFAULT '0' COMMENT 'whether the receiver has deleted the message',
  `is_read` int(1) NOT NULL DEFAULT '0' COMMENT 'whether this message has been read or not',
  `last_reply` int(10) NOT NULL COMMENT 'Time of the last reply',
  `parent` int(5) NOT NULL DEFAULT '0' COMMENT 'ID of the parent message or zero if it is a main message'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='message storage for the messaging system';
ALTER TABLE `messages`
  ADD PRIMARY KEY (`ID`);
ALTER TABLE `messages`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary index value', AUTO_INCREMENT=0;";
        return $query;
    }



    /**
     * Since we need a cache of the usernames, we use this function 
     * to find/return all usernames based on ID.
     * 
     * @param  $ID - The ID of the user you are looking for.
     * 
     * @return string - Either the username or unknown will be returned.
     *
     * @todo  Depricated, use class in user model!
     * 
     */
    private function get_username($ID)
    {
        if (!Check::ID($ID)) {
            return false;
        }
        if (!isset($this->_usernames[$ID])) {
            if (Self::$_user->get($ID)) {
                $this->_usernames[$ID] = Self::$_user->data()->username;
            } else {
                $this->_usernames[$ID] = 'Unknown';
            }
        }
        return $this->_usernames[$ID];
    }

    /**
     * This function is to prep our messages for display. An array of raw messages
     * sent through this function will automatically have all the user ID's filter 
     * into actual usernames.
     *  
     * @param  $message_array - This is an array of messages that need to be processed.
     * 
     * @return array - It will return the same message array after being processed.
     *
     * @todo  add filtering for BB-code.
     */
    private function process_message($message_array) {
        foreach ($message_array as &$message)
        {
            $cleaned = Sanitize::content_short($message->message);
            $short = explode(" ", $cleaned);
            $summary = implode(" ", array_splice($short, 0, 25));
            $initCount = count($short,1);
            if ($initCount >= 25) {
                $message->summary = $summary . '...';
            } else {
                $message->summary = $summary;
            }
            if ($message->is_read == 1) {
                $message->read = '';
            } else {
                $message->read = Self::$_template->standard_view('message.unread.badge');
            }
            $message->from_avatar = Self::$_user->get_avatar($message->user_from);
            $message->user_to = $this->get_username($message->user_to);
            $message->user_from = $this->get_username($message->user_from);
        }
        return $message_array;
    }

    /**
     * This calls a view of the requested message.
     * 
     * @param  int $ID - The message ID you are looking for.
     * 
     * @return null
     */
    public function view_message($ID)
    {
        if (!Check::ID($ID)) {
            Issue::error('There was an error with your request, please go back and try again.');
            Debug::info('Invalid ID');
            return false;
        }
        $result = Self::$_db->get('messages', array('ID', '=', $ID));
        if ($result->count() == 0) {
            Issue::error('There was an error with your request, please go back and try again.');
            Debug::info('Message not found.');
            return false;
        }
        $newData = $result->first();
        if ($newData->user_to == Self::$_active_user->ID) {
            $c = 1;
            if ($newData->reciever_deleted == 1) {
                Issue::error('There was an error with your request, please go back and try again.');
                Debug::info('Receiver deleted this message.');
                return false;
            }
        }
        if ($newData->user_from == Self::$_active_user->ID) {
            $c = 1;
            if ($newData->sender_deleted == 1) {
                Issue::error('There was an error with your request, please go back and try again.');
                Debug::info('Sender deleted this message.');
                return false;
            }
        }
        if ($newData->parent != 0) {
            $find = $newData->parent;
        } else {
            $find = $newData->ID;
        }
        if ($c === 1) {
            $where = array('ID', '=', $find, 'OR', 'Parent', '=', $find, 'AND', 'ID', '>=', $find);
            $result = Self::$_db->get('messages', $where, 'ID', 'DESC');
            $thread = $result->results();
            Self::$_template->set('PID', $ID);
            foreach ($thread as &$message) {
                Self::mark_read($message->ID);
            }
            $this->view('message', $this->process_message($thread));
        } else {
        	unset($c);
            Issue::error('You do not have permission to view this message');
        }
    }

    /**
     * This function calls the view for the message inbox.
     * 
     * @return null
     *
     * @todo no views in the models
     */
    public function inbox()
    {
        $result = Self::$_db->get_paginated('messages', array('user_to', '=', Self::$_active_user->ID, "AND", 'parent', '=', 0, "AND", 'reciever_deleted', '=', 0));
        $list = $result->results();
        $this->view('message.inbox', $this->process_message($list));
    }

    /**
     * This function calls the view for the message inbox.
     * 
     * @return null
     */
    public function recent()
    {
        $result = Self::$_db->get('messages', array('user_to', '=', Self::$_active_user->ID, "AND", 'parent', '=', 0, "AND", 'reciever_deleted', '=', 0), 'ID', 'DESC', array(0,5));
        if ($result->count() == 0) {
            Debug::info('No messages found');
            return false;
        }
        return $this->process_message($result->results());
    }

    public function unread_count()
    {
        if (empty(Self::$_active_user->ID)) {
            return 0;
        }
        $result = Self::$_db->get('messages', array('user_to', '=', Self::$_active_user->ID, "AND", 'is_read', '=', 0, "AND", 'reciever_deleted', '=', 0));
        return $result->count();
    }

    /**
     * This function calls the view for the message outbox.
     * 
     * @return null
     */
    public function outbox()
    {
        $result = Self::$_db->get_paginated('messages', array('user_from', '=', Self::$_active_user->ID, "AND", 'parent', '=', 0, "AND", 'sender_deleted', '=', 0));
        $list = $result->results();
        $this->view('message.outbox', $this->process_message($list));
    }

    /**
     * This function will either send a submitted message or display the new message view.
     * 
     * @return function
     */
    public function new_message()
    {
        if (Input::exists()) {
            if (Input::post('to_user')) {
                $this->send(Input::post('to_user'));
            } else {
                return Issue::error('You must specify a valid username to send a message.');
            }
        }
        Debug::warn('No Input for New Message.');
        return false;
    }

    /**
     * Function to check input and save messages to the DB.
     * 
     * @param  string $data - Username of the person receiving the sent message.
     * 
     * @return function
     */
    private function send($data)
    {
        if (!Check::username_exists($data)) {
            return Issue::error('No user by that name was found!');
        }
        $working_user = Self::$_user->get($data);
        $fields = array(
            'user_to' => $working_user->ID,
            'user_from' => Self::$_active_user->ID,
            'message' => Input::post('message'),
            'subject' => Input::post('subject'),
            'sent' => time(),
            'last_reply' => time(),
        );
        if (!Self::$_db->insert('messages', $fields)) {
            new CustomException('message_send');
        }
        return Issue::success('Message sent.');
    }

    /**
     * Marks a message as read. This is setup to only work 
     * if the message was sent to the active user.
     * 
     * @param  int - The message ID you are marking as read.
     * 
     * @return bool
     */
    public function mark_read($data)
    {
        if (!Check::ID($data)) {
            Issue::error('There was an error with your request, please go back and try again.');
            Debug::info('Invalid ID');
            return false;
        }
        $result = Self::$_db->get('messages', array('ID', '=', $data));
        if ($result->count() == 0) {
            Issue::error('There was an error with your request, please go back and try again.');
            Debug::info('Message not found;');
            return false;
        }
        $message = $result->first();
        if ($message->is_read == 1) {
            Debug::info('Message is already marked as read.');
            return false;
        }
        if ($message->user_to != Self::$_active_user->ID) {
            Debug::info('Cannot "mark_read" messages not to you.');
            return false;
        }
        $fields = array('is_read' => 1);
        if (Self::$_db->update('messages', $data, $fields)) {
            return true;
        }
        Debug::error('Failed to update message as read.');
        return false;
    }

    public function message_title($data)
    {
        if (!Check::ID($data)) {
            Issue::error('There was an error with your request, please go back and try again.');
            Debug::info('Invalid ID');
            return false;
        }
        $result = Self::$_db->get('messages', array('ID', '=', $data));
        if ($result->count() == 0) {
            Issue::error('There was an error with your request, please go back and try again.');
            Debug::info('Message not found;');
            return false;
        }
        $message = $result->first();
        if ($message->is_read == 1) {
            Debug::info('Message is already marked as read.');
            return false;
        }
        $x = 0;
        if ($message->user_to == Self::$_active_user->ID) {
            $x++;
        }
        if ($message->user_from == Self::$_active_user->ID) {
            $x++;
        }
        if ($x !== 1) {
            Debug::info('Access Denied');
            return false;
        }
        return $message->subject;
    }
    /**
     * Function to delete messages from the DB.
     * 
     * @param  int $data - The ID of the message you are trying to delete.
     * @todo  - done at 5 am after no sleep. This can be simplified a lot, i just wanted a working solution ASAP
     * @return bool
     */
    public function delete_message($data)
    {
        if (is_array($data)) {
            foreach($data as $instance)
            {
                $x = 0;
                if (is_array($instance)) {
                    foreach ($instance as $id)
                    {
                        if (Check::ID($id)) {
                            $result = Self::$_db->get('messages', array('ID', '=', $id));
                            if ($result->count() == 0) {
                                Issue::error('There was an error with your request, please try again.');
                                Debug::info('Message not found.');
                                return false;
                            }
                            if ($result->first()->parent != 0) {
                                $result = Self::$_db->get('messages', array('ID', '=', $result->first()->parent));
                                if ($result->count() == 0) {
                                    Issue::error('There was an error with your request, please try again.');
                                    Debug::info('Parent not found');
                                    return false;
                                }
                            }
                            $message = $result->first();
                            if ($message->user_to == Self::$_active_user->ID) {
                                $fields = array('reciever_deleted' => '1');
                            }
                            if ($message->user_from == Self::$_active_user->ID) {
                                $fields = array('sender_deleted' => '1');
                            }
                            if (Self::$_db->update('messages', $id, $fields)) {
                                Debug::log("Message deleted: $id");
                                $x++;
                            }
                        }
                    }
                } else {
                    if (Check::ID($instance)) {
                        $result = Self::$_db->get('messages', array('ID', '=', $instance));
                        if ($result->count() == 0) {
                            Issue::error('There was an error with your request, please try again.');
                            Debug::info('Message not found.');
                            return false;
                        }
                        if ($result->first()->parent != 0) {
                            $result = Self::$_db->get('messages', array('ID', '=', $result->first()->parent));
                            if ($result->count() == 0) {
                                Issue::error('There was an error with your request, please try again.');
                                Debug::info('Parent not found');
                                return false;
                            }
                        }
                        $message = $result->first();
                        if ($message->user_to == Self::$_active_user->ID) {
                            $fields = array('reciever_deleted' => '1');
                        }
                        if ($message->user_from == Self::$_active_user->ID) {
                            $fields = array('sender_deleted' => '1');
                        }
                        if (Self::$_db->update('messages', $instance, $fields)) {
                            Debug::log("Message deleted: $instance");
                            $x++;
                        }
                    }
                }
                if ($x > 0) {
                    Issue::notice('Message deleted.');

                    return true;
                }
            }
        } else {
            if (Check::ID($data)) {
                $result = Self::$_db->get('messages', array('ID', '=', $data));
                if ($result->count() == 0) {
                    Issue::error('There was an error with your request, please try again.');
                    Debug::info('Message not found.');
                    return false;
                }

                $message = $result->first();
                if ($message->user_to == Self::$_active_user->ID) {
                    $fields = array('reciever_deleted' => '1');
                }
                if ($message->user_from == Self::$_active_user->ID) {
                    $fields = array('sender_deleted' => '1');
                }
                if (Self::$_db->update('messages', $data, $fields)) {
                    Issue::notice('Message deleted.');

                    return true;
                }
            }
        }  
        Issue::error('There was an error with your request, please try again.');
        return false;
    }
	    

    /**
     * Function for showing the reply box, or adding a reply to the DB.
     * 
     * @return bool
     */
    public function reply()
    {
        if (!Input::exists('message_ID')) {
            Debug::info("No Message ID.");
            Issue::error('There was an error with your request, please go back and try again.');  
            return false;
        }
        if (!Check::ID(Input::post('message_ID'))) {
            Debug::info('Invalid message ID.');
            Issue::error('There was an error with your request, please go back and try again.');  
            return false;
        }
        $where = array('ID', '=', Input::post('message_ID'));
        $result = Self::$_db->get('messages', $where);
        if ($result->count() == 0) {
            Debug::info("Message not found.");
            Issue::error('There was an error with your request, please go back and try again.');  
            return false;
        }
        if (!Input::exists('message')) {
            Self::$_template->set('message_ID', Input::post('message_ID'));
            return $this->view('message.reply');
        }
        $data = $result->first();
        if ($data->user_to == Self::$_active_user->ID) {
            $to = $data->user_from;
        } elseif ($data->user_from == Self::$_active_user->ID) {
            $to = $data->user_to;
        }
        if (!isset($to)) {
            Debug::info("Permission Denied.");
            Issue::error('There was an error with your request, please go back and try again.');  
            return false; 
        }
        if ($to === Self::$_active_user->ID) {
            Issue::error('You cannot send a message to yourself.');  
            return false; 
        }
        $fields = array(
            'last_reply' => time(),
            'is_read' => 0,
        );
        if (!Self::$_db->update('messages', $data->ID, $fields)) {
            new CustomException('messages_reply_update');
            Issue::error('There was an error with your request, please go back and try again.');  
            return false;
        }
        $fields = array(
            'user_to' => $to,
            'user_from' => Self::$_active_user->ID,
            'message' => Input::post('message'),
            'subject' => 're: ' . $data->subject,
            'sent' => time(),
            'parent' => $data->ID,
            'last_reply' => time(),
        );
        if (!Self::$_db->insert('messages', $fields)) {
            new CustomException('messages_reply_send');
            Issue::error('There was an error with your request, please go back and try again.');
            return false;
        }
        Issue::notice('Message sent.');
        return true;
    }
}