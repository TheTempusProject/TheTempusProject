<?php
/**
 * Models/model_group.php.
 *
 * This class is used for the manipulation of the groups database table.
 *
 * @version 1.0
 *
 * @author  Joey Kimsey <joeyk4816@gmail.com>
 *
 * @link    https://github.com/JoeyK4816/TheTempusProject
 *
 * @license https://opensource.org/licenses/MIT [MIT LICENSE]
 */

namespace TheTempusProject\Models;

use TempusProjectCore\Classes\Check as Check;
use TempusProjectCore\Classes\Code as Code;
use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Config as Config;
use TempusProjectCore\Classes\DB as DB;
use TempusProjectCore\Classes\Session as Session;
use TempusProjectCore\Classes\Cookie as Cookie;
use TempusProjectCore\Classes\Log as Log;
use TempusProjectCore\Classes\Input as Input;
use TempusProjectCore\Classes\Email as Email;

class model_comment extends Controller
{
    public function __construct()
    {
        Debug::log('Model Constructed: '.get_class($this));
        Self::$_db = DB::getInstance();
        Debug::gend();
    }
    public static function sql() 
    {
        $query = "CREATE TABLE `comments` (
  `ID` int(5) NOT NULL,
  `author` int(5) NOT NULL,
  `content_id` int(5) NOT NULL,
  `content_type` varchar(32) NOT NULL,
  `created` int(11) NOT NULL,
  `edited` int(11) NOT NULL,
  `approved` int(1) NOT NULL,
  `content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `comments`
  ADD PRIMARY KEY (`ID`);
ALTER TABLE `comments`
  MODIFY `ID` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";
        return $query;
    }
    public static function find_by_ID($data) 
    {
        if (!Check::ID($data)) {
            Debug::info("comments: illegal ID.");
            
            return false;
        }
        $data = Self::$_db->get('comments', array('ID', '=', $data));
        if (!$data->count()) {
            Debug::info("No Blog posts found.");

            return false;
        }
        $out = Self::filter_comments($data->results());
        return $out;
    }
    public static function delete($ID) 
    {
        if (is_array($ID)) {
            foreach($ID as $instance)
            {
                $x = 0;
                if (is_array($instance)) {
                    foreach ($instance as $id)
                    {
                        if (Check::ID($id)) {
                            Self::$_db->delete('comments', array('ID', '=', $id));
                            Debug::log("comment deleted: $id");
                            $x++;
                        }
                    }
                } else {
                    if (Check::ID($instance)) {
                        Self::$_db->delete('comments', array('ID', '=', $instance));
                        Debug::log("comment deleted: $instance");
                        $x++;
                    }
                }
                if ($x > 0) {
                    return true;
                }
            }
        } else {
            if (Check::ID($ID)) {
                Self::$_db->delete('comments', array('ID', '=', $ID));
                Debug::log("comment deleted: $ID");

                return true;
            }
        }

        return false;
    }
    public static function count($contentType, $contentID) 
    {
        if (!Check::ID($contentID)) {
            Debug::info("Comments: illegal ID.");
            
            return false;
        }
        if (!Check::data_title($contentType)) {
            Debug::info("Comments: illegal Type.");
            
            return false;
        }
        $where = array('content_type', '=', $contentType, 'AND', 'content_id', '=', $contentID);
        $data = Self::$_db->get('comments', $where);
        if (!$data->count()) {
            Debug::info("No comments found.");

            return false;
        }
        return $data->count();
    }
    public static function display($displayCount, $contentType, $contentID) 
    {
        if (!Check::ID($contentID)) {
            Debug::info("Comments: illegal ID.");
            
            return false;
        }
        if (!Check::data_title($contentType)) {
            Debug::info("Comments: illegal Type.");
            
            return false;
        }
        $where = array('content_type', '=', $contentType, 'AND', 'content_id', '=', $contentID);
        $data = Self::$_db->get('comments', $where, 'created', 'DESC', array(0,$displayCount));
        if (!$data->count()) {
            Debug::info("No comments found.");

            return false;
        }
        $out = Self::filter_comments($data->results());
        return $out;
    }
    public static function update($data) 
    {
        if (!Check::ID($data)) {
            Debug::info("Comments: illegal ID.");
            
            return false;
        }
        $fields = array(
            'edited' => time(),
            'content' => Input::post('comment'),
            'approved' => 1,
            );
        if (!Self::$_db->update('comments', $data, $fields)) {
             new Custom_Exception('User_update');
             Debug::error("Post: $data not updated: $fields");

             return false;
        }
        return true;
    }
    public static function create($contentType, $contentID) 
    {
        if (!Check::ID($contentID)) {
            Debug::info("Comments: illegal ID.");
            
            return false;
        }
        if (!Check::data_title($contentType)) {
            Debug::info("Comments: illegal Type.");
            
            return false;
        }
        $fields = array(
            'author' => Self::$_active_user->ID,
            'edited' => time(),
            'created' => time(),
            'content' => Input::post('comment'),
            'content_type' => $contentType,
            'content_id' => $contentID,
            'approved' => 0,
            );
        if (!Self::$_db->insert('comments', $fields)) {
             new Custom_Exception('new_comment');
             Debug::error("Comments: $data not created: $fields");

             return false;
        }
        return true;
    }
    public static function filter_comments($data)
    {
        $x = 0;
        foreach ($data as $instance) 
        {
            if (!is_object($instance)) {
                $instance = $data;
                $continue = true;
            }
            $to_array = (array) $instance;
            $aName = Self::$_user->get_username($instance->author);
            $aAvatar = Self::$_user->get_avatar($instance->author);
            switch ($instance->content_type) {
                case 'blog':
                    $content = Self::$_blog->find($instance->content_id);
                    $title = $content->title;
                    break;
                default:
                    break;
            }
            if (!isset($title)) {
                $title = 'Unknown';
            }
            $stuff = array(
                'avatar' => $aAvatar,
                'author_name' => $aName,
                'content_title' => $title,
                );
            $out[$x] = (object) array_merge($stuff, $to_array);
            $x++;
            if (!empty($continue)) {
                break;
            }
        }
        return $out;
    }
    public static function recent($contentType = 'all', $limit = null) 
    {
        switch ($contentType) {
            case 'blog':
                if (empty($limit)) {
                    $data = Self::$_db->get_paginated('comments', '*');
                } else {
                    $data = Self::$_db->get('comments', array('content_type', '=', $contentType), 'created', 'DESC', array(0,$limit));
                }
                break;
            default:
                if (empty($limit)) {
                    $data = Self::$_db->get_paginated('comments', '*');
                } else {
                    $data = Self::$_db->get('comments', array('ID', '>', '0'), 'created', 'DESC', array(0,$limit));
                }
                break;
        }
        
        if (!$data->count()) {
            Debug::info("No comments found.");

            return false;
        }
        $out = Self::filter_comments($data->results());
        return $out;
    }

}
