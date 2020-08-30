<?php
/**
 * Models/ticket.php
 *
 * This class is used for the manipulation of the subscribers database table.
 *
 * @version 3.0
 *
 * @author  Joey Kimsey <JoeyKimsey@thetempusproject.com>
 *
 * @link    https://TheTempusProject.com
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 *
 * @todo  make this send a confirmation email
 */

namespace TheTempusProject\Models;

use TempusProjectCore\Classes\Check as Check;
use TempusProjectCore\Classes\Code as Code;
use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Input;

class Ticket extends Controller
{
    protected static $user;
    public function __construct()
    {
        Debug::log('Model Constructed: '.get_class($this));
        self::$user = $this->model('user');
    }

    /**
     * Returns the current model version.
     *
     * @return string - the correct model version
     */
    public static function modelVersion()
    {
        return '3.0.0';
    }

    /**
     * Tells the installer which types of integrations your model needs to install.
     *
     * @return array - Install flags
     */
    public static function installFlags()
    {
        $flags = [
            'installDB' => true,
            'installPermissions' => false,
            'installConfigs' => false,
            'installResources' => false,
            'installPreferences' => false
        ];
        return $flags;
    }

    /**
     * This function is used to install database structures needed for this model.
     *
     * @return boolean - The status of the completed install
     */
    public static function installDB()
    {
        self::$db->newTable('tickets');
        self::$db->addfield('submittedBy', 'int', '11');
        self::$db->addfield('submittedOn', 'int', '10');
        self::$db->addfield('updated', 'int', '10');
        self::$db->addfield('live', 'int', '1');
        self::$db->addfield('name', 'varchar', '80');
        self::$db->addfield('branch', 'varchar', '80');
        self::$db->addfield('category', 'varchar', '80');
        self::$db->addfield('project', 'varchar', '80');
        self::$db->addfield('status', 'varchar', '80');
        self::$db->addfield('description', 'text', '');
        self::$db->createTable();
        return self::$db->getStatus();
    }

    /**
     * This method will remove all the installed model components.
     *
     * @return bool - if the uninstall was completed without error
     */
    public static function uninstall()
    {
        self::$db->removeTable('tickets');
        return true;
    }

    /**
     * Compiles a list of all users, allowing for filtering the list.
     *
     * @todo
     *
     * @param  array $filter - A filter to be applied to the users list.
     *
     * @return bool|object - Depending on success.
     */
    public function listTickets($filter = null, $by = null)
    {
        switch ($filter) {
            case 'creator':
                if (!Check::ID($by)) {
                    return false;
                }
                $query = array('submittedBy', '=', $by);
                $data = self::$db->getPaginated('tickets', $query);
                if (!$data->count()) {
                    return false;
                }
                return (object) $this->processTicket($data->results());
            case 'status':
                if (!Check::dataTitle($by)) {
                    return false;
                }
                $query = array('status', '=', $by);
                $data = self::$db->getPaginated('tickets', $query);
                if (!$data->count()) {
                    return false;
                }
                return (object) $this->processTicket($data->results());
            case 'category':
                if (!Check::dataTitle($by)) {
                    return false;
                }
                $query = array('category', '=', $by);
                $data = self::$db->getPaginated('tickets', $query);
                if (!$data->count()) {
                    return false;
                }
                return (object) $this->processTicket($data->results());
            case 'project':
                if (!Check::dataTitle($by)) {
                    return false;
                }
                $query = array('project', '=', $by);
                $data = self::$db->getPaginated('tickets', $query);
                if (!$data->count()) {
                    return false;
                }
                return (object) $this->processTicket($data->results());
            case 'oldest':
                $data = self::$db->getPaginated('tickets', '*', 'ID', 'ASC');
                if (!$data->count()) {
                    return false;
                }
                return (object) $this->processTicket($data->results());
            case 'newest':
                $data = self::$db->getPaginated('tickets', '*');
                if (!$data->count()) {
                    return false;
                }
                return (object) $this->processTicket($data->results());
            case 'live':
                $query = array('status', '=', ($by?1:0));
                $data = self::$db->getPaginated('tickets', $query);
                if (!$data->count()) {
                    return false;
                }
                return (object) $this->processTicket($data->results());
            default:
                $data = self::$db->getPaginated('tickets', "*");
                if (!$data->count()) {
                    return false;
                }
                return (object) $this->processTicket($data->results());
        }
    }
    public static function processTicket($data)
    {
        $projects = array(
            'jk-com' => 'JoeyKimsey-COM',
            'jk-dev' => 'JoeyKimsey-DEV',
            'ttp-com' => 'TheTempusProject-COM',
            'ttp-dev' => 'TheTempusProject-DEV',
            'acc-com' => 'AtlantaCreativeCollective-COM',
            'acc-dev' => 'AtlantaCreativeCollective-DEV',
            'tpc-dev' => 'TPC-DEV',
        );
        $categories = array(
            'fea' => 'New Feature',
            'bug' => 'Bug Fix',
            'vis' => 'Visual Update',
            'rev' => 'Review',
            'con' => 'Content',
            'ser' => 'Server',
            'oth' => 'Other',
        );
        $statuses = array(
            'todo' => 'To Do',
            'onhold' => 'On Hold',
            'inprogress' => 'In Progress',
            'onqa' => 'In QA',
            'readytodeploy' => 'Ready To Deploy',
            'deployed' => 'Deployed',
        );
        $x = 0;
        foreach ($data as $instance) {
            if (!is_object($instance)) {
                $instance = $data;
                $end = true;
            }
            $instance->byUser = self::$user->getUsername($instance->submittedBy);
            $instance->projectText = $projects[$instance->project];
            $instance->liveText = ($instance->live ? 'Yes' : 'No');
            $instance->checked = ($instance->live ? 'checked' : '');
            $instance->statusText = $statuses[$instance->status];
            $instance->categoryText = $categories[$instance->category];
            $out[] = $instance;
            if (!empty($end)) {
                $out = $out[0];
                break;
            }
        }
        return $out;
    }
    /**
     * Adds an email to the subscribers database.
     *
     * @param string $data - the email you are trying to add.
     *
     * @return bool
     */
    public function update($ID)
    {
        if (!Check::ID($ID)) {
            return false;
        }
        $ticket = $this->get($ID);
        if ($ticket == false) {
            return false;
        }
        if (Check::form('ticketUpdate')) {
            $fields = array(
                'updated' => time(),
                'name' => Input::post('name'),
                'description' => Input::post('description'),
                'category' => Input::post('category'),
                'status' => Input::post('status'),
                'live' => (Input::post('live') ? 1 : 0) ,
                'branch' => Input::post('branch'),
                'project' => Input::post('project')
                );
            self::$db->update('tickets', $ID, $fields);

            return true;
        }

        return false;
    }

    /**
     * Adds an email to the subscribers database.
     *
     * @param string $data - the email you are trying to add.
     *
     * @return bool
     */
    public function create()
    {
        if (!Check::form('ticket')) {
            return false;
        }
        $fields = array(
            'submittedBy' => self::$activeUser->ID,
            'submittedOn' => time(),
            'updated' => time(),
            'name' => Input::post('name'),
            'description' => Input::post('description'),
            'category' => Input::post('category'),
            'status' => 'todo',
            'branch' => 'none',
            'live' => (Input::post('live')?1:0),
            'project' => Input::post('project'));
        if (!self::$db->insert('tickets', $fields)) {
            new CustomException('ticket');
            Debug::error("User not created: $fields");

            return false;
        }

        return true;
    }

    /**
     * removes an email from the subscribers database.
     *
     * @param string $data - the email you are trying to remove.
     *
     * @return bool
     */
    public function get($ID)
    {
        if (!Check::ID($ID)) {
            return false;
        }
        $tickets = self::$db->get('tickets', array('ID', '=', $ID));
        if (!$tickets->count()) {
            Debug::info('No ticket found for ID: ' . $ID);
            return false;
        }
        return $this->processTicket($tickets->first());
    }

    /**
     * removes an email from the subscribers database.
     *
     * @param string $data - the email you are trying to remove.
     *
     * @return bool
     */
    public function remove($ID)
    {
        if (Check::ID($ID)) {
            $ticket = self::$db->get('tickets', array('ID', '=', $ID));
            if (!$ticket->count()) {
                return false;
            }
            self::$db->delete('tickets', array('ID', '=', $ID));

            return true;
        }

        return false;
    }
}
