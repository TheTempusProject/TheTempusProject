<?php
/**
 * Models/model_blog.php.
 *
 * This class is used for the manipulation of the blog database table.
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

use TempusProjectCore\Core\Controller as Controller;
use TempusProjectCore\Classes\Debug as Debug;
use TempusProjectCore\Classes\Check as Check;
use TempusProjectCore\Classes\Sanitize as Sanitize;
use TempusProjectCore\Classes\Input as Input;

class model_blog extends Controller
{
    public function __construct()
    {
        Debug::log('Model Constructed: '.get_class($this));
        Debug::gend();
    }
    public static function sql() 
    {
        $query = "CREATE TABLE `posts` (
  `ID` int(11) NOT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `author` int(11) NOT NULL,
  `created` int(11) NOT NULL,
  `edited` int(11) NOT NULL,
  `draft` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `posts`
  ADD PRIMARY KEY (`ID`);
ALTER TABLE `posts`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT 'primary index value', AUTO_INCREMENT=0;
INSERT INTO `posts` (`ID`, `title`, `content`, `author`, `created`, `edited`, `draft`) VALUES
(1, 'Welcome', '[p]This is just a simple message to say thank you for installing The Tempus Project. If you have any questions you can find everything through our website [url=http://www.TheTempusProject.com]here[/url].[/p]', 1, 1498534881, 1498534862, 0);";
        return $query;
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
                            Self::$_db->delete('posts', array('ID', '=', $id));
                            Debug::log("Blog post deleted: $id");
                            $x++;
                        }
                    }
                } else {
                    if (Check::ID($instance)) {
                        Self::$_db->delete('posts', array('ID', '=', $instance));
                        Debug::log("Blog post deleted: $instance");
                        $x++;
                    }
                }
                if ($x > 0) {
                    return true;
                }
            }
        } else {
            if (Check::ID($ID)) {
                Self::$_db->delete('posts', array('ID', '=', $ID));
                Debug::log("Blog post deleted: $ID");

                return true;
            }
        }

        return false;
    }
    public static function newPost($data = null) 
    {
        if ($data == 'saveDraft') {
            $draft = 1;
        } else {
            $draft = 0;
        }
        if (!Check::data_title(Input::post('title'))) {
            Debug::info("model_blog: illegal title.");
            
            return false;
        }
    	$fields = array(
            'author' => Self::$_active_user->ID,
            'draft' => $draft,
            'created' => time(),
            'edited' => time(),
            'content' => Sanitize::rich(Input::post('blog_post')),
            'title' => Input::post('title'),
            );
        if (!Self::$_db->insert('posts', $fields)) {
             new Custom_Exception('User_update');
             Debug::error("Blog Post: $data not updated: $fields");

             return false;
        }
    }
    public static function update($data = null) 
    {
        if (Input::post('submit') == 'saveasdraft') {
            $draft = 1;
        } else {
            $draft = 0;
        }
        if (!Check::ID($data)) {
            Debug::info("model_blog: illegal ID.");
            
            return false;
        }
        if (!Check::data_title(Input::post('title'))) {
            Debug::info("model_blog: illegal title.");
            
            return false;
        }
        $fields = array(
            'author' => Self::$_active_user->ID,
            'draft' => $draft,
            'edited' => time(),
            'content' => Sanitize::rich(Input::post('blog_post')),
            'title' => Input::post('title'),
            );
        if (!Self::$_db->update('posts', $data, $fields)) {
             new Custom_Exception('Blog_update');
             Debug::error("Blog Post: $data not updated: $fields");

             return false;
        }
        return true;
    }
    public static function preview() 
    {
        if (!Check::data_title(Input::post('title'))) {
            Debug::info("model_blog: illegal characters.");
            
            return false;
        }
    	$fields = array(
            'author_name' => Self::$_active_user->username,
            'created' => time(),
            'content' => Input::post('blog_post'),
            'title' => Input::post('title'),
            );
        return (object)$fields;
    }
    public static function find($data) 
    {
        if (!Check::ID($data)) {
            Debug::info("blog find: Invalid ID.");

            return false;
        }
    	$data = Self::$_db->get('posts', array('ID', '=', $data));
        if (!$data->count()) {
            Debug::info("No Blog posts found.");

            return false;
        }
        $out = Self::filter_post($data->first());
        return $out;
    }
    public static function archive() 
    {
        $current = time();
        $x = 0;
        $out = array();
        $month = date("F", $current);
        $year = date("Y", $current);
        $previous = date("U", strtotime("$month 1st $year"));
        while ($x <= 5) {
            $data = Self::$_db->get('posts', array('created', '<=', $current, 'AND', 'created', '>=', $previous));
            $x++;
            $month = date("m", $previous);
            $montht = date("F", $previous);
            $year = date("Y", $previous);
            if (!$data) {
                $count = 0;
            } else {
                $count = $data->count();
            }
            $out[] = (object) array(
                'count' => $count,
                'month' => $month,
                'year' => $year,
                'month_text' => $montht,
                );
            $current = $previous;
            $previous = date("U", strtotime("-1 months", $current));
        }
        if (!$data) {
            Debug::info("No Blog posts found.");

            return false;
        }
        return (object) $out;
    }
    public static function recent($limit = null) 
    {
        if (empty($limit)) {
            $data = Self::$_db->get_paginated('posts', '*');
        } else {
            $data = Self::$_db->get('posts', array('ID', '>', '0'), 'ID', 'DESC', array(0,$limit));
        }
        if (!$data->count()) {
            Debug::info("No Blog posts found.");

            return false;
        }
        $out = Self::filter_post($data->results());
        return $out;
    }
    public static function listPosts() 
    {
        $data = Self::$_db->get_paginated('posts', '*');
        if (!$data->count()) {
            Debug::info("No Blog posts found.");

            return false;
        }
        $out = Self::filter_post($data->results());
        return $out;
    }
    public static function byYear($year) 
    {
        if (!Check::ID($year)) {
            Debug::info("Invalid Year");
            return false;
        }
        $first = date("U", strtotime("first day of $year"));
        $last = date("U", strtotime("last day of $year"));
        $data = Self::$_db->get('posts', array('created', '<=', $last, 'AND', 'created', '>=', $first));
        if (!$data->count()) {
            Debug::info("No Blog posts found.");

            return false;
        }
        $out = Self::filter_post($data->results());
        return (object) $out;
    }
    public static function byAuthor($ID) 
    {
        if (!Check::ID($ID)) {
            Debug::info("Invalid Author");
            return false;
        }
        $data = Self::$_db->get_paginated('posts', array('author' => $ID));
        if (!$data->count()) {
            Debug::info("No Blog posts found.");

            return false;
        }
        $out = Self::filter_post($data->results());
        return $out;
    }
    public static function byMonth($month, $year = 2017) 
    {
        if (!Check::ID($month)) {
            Debug::info("Invalid Month");
            return false;
        }
        if (!Check::ID($year)) {
            Debug::info("Invalid Year");
            return false;
        }
        $first = date("U", strtotime("$month/01/$year"));
        $month = date("F", $first);
        $last = date("U", strtotime("last day of $month $year"));
        $data = Self::$_db->get('posts', array('created', '<=', $last, 'AND', 'created', '>=', $first));
        if (!$data->count()) {
            Debug::info("No Blog posts found.");

            return false;
        }
        $out = Self::filter_post($data->results());
        return (object) $out;
    }
    public static function filter_post($data)
    {
        $x = 0;
        $out = array();
        foreach ($data as $instance) 
        {
            $draft = '';
            if (!is_object($instance)) {
                $instance = $data;
                $end = 1;
            }
            $to_array = (array) $instance;
            $aName = Self::$_user->get_username($instance->author);
            $cleaned = Sanitize::content_short($instance->content);
            
            $short = explode(" ", $cleaned);
            $short2 = explode("\n", $cleaned);
            $initCount = count($short,1);
            $initCount2 = count($short2,1);
            $short_comment = implode(" ", array_splice($short, 0, 100));
            $short_comment2 = implode("\n", array_splice($short2, 0, 5));
            if (strlen($short_comment) < strlen($short_comment2)) {
                if ($initCount >= 100) {
                    $short = $short_comment . '... <a href="{base}blog/post/' . $instance->ID . '">Read More</a>';
                } else {
                    $short = $short_comment;
                }
            } else {
                $short = $short_comment2 . '... <a href="{base}blog/post/' . $instance->ID . '">Read More</a>';
            }
            if ($instance->draft != '0') {
                $draft = ' <b>Draft</b>';
            }
            $stuff = array(
                'is_draft' => $draft,
                'author_name' => $aName,
                'content_short' => $short,
                );
            if (isset($end)) {
                unset($end);
                $out = (object) array_merge($stuff, $to_array);
                break;
            }
            $out[$x] = (object) array_merge($stuff, $to_array);
            $x++;
        }
        return (object) $out;
    }
}
