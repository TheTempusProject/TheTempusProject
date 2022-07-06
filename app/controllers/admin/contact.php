<?php
/**
 * app/controllers/admin/deoendencies.php
 *
 * This is the xxxxxx controller.
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
use TempusProjectCore\Template\Issues;
use TempusProjectCore\Classes\Input;
use TempusProjectCore\Classes\Email;

class Contact extends AdminController
{
    public function index($data = null)
    {
        if (Input::exists('mailType')) {
            $params = [
                'subject' => Input::post('mailSubject'),
                'title'   => Input::post('mailTitle'),
                'message' => Input::post('mailMessage'),
            ];
            switch (Input::post('mailType')) {
                case 'registered':
                    $list = self::$user->userList();
                    foreach ($list as $recipient) {
                        Email::send($recipient->email, 'contact', $params, ['template' => true]);
                    }
                    Issues::add('success', 'Email(s) Sent');
                    break;
                case 'newsletter':
                    $list = self::$user->userList('newsletter');
                    foreach ($list as $recipient) {
                        //make unsub
                        Email::send($recipient->email, 'contact', $params, ['template' => true]);
                    }
                    Issues::add('success', 'Email(s) Sent');
                    break;
                case 'all':
                    $list = self::$user->userList();
                    foreach ($list as $recipient) {
                        //make unsub
                        Email::send($recipient->email, 'contact', $params, ['template' => true]);
                    }
                    $list = self::$subscribe->listSubscribers();
                    foreach ($list as $recipient) {
                        $params['confirmationCode'] = $recipient->confirmationCode;
                        Email::send($recipient->email, 'contact', $params, ['template' => true, 'unsubscribe' => true]);
                    }
                    Issues::add('success', 'Email(s) Sent');
                    break;
                case 'opt':
                    $list = self::$user->userList('newsletter');
                    foreach ($list as $recipient) {
                        //make unsub
                        Email::send($recipient->email, 'contact', $params, ['template' => true]);
                    }
                    $list = self::$subscribe->listSubscribers();
                    foreach ($list as $recipient) {
                        $params['confirmationCode'] = $recipient->confirmationCode;
                        Email::send($recipient->email, 'contact', $params, ['template' => true, 'unsubscribe' => true]);
                    }
                    Issues::add('success', 'Email(s) Sent');
                    break;
                case 'subscribers':
                    $list = self::$subscribe->listSubscribers();
                    foreach ($list as $recipient) {
                        $params['confirmationCode'] = $recipient->confirmationCode;
                        Email::send($recipient->email, 'contact', $params, ['template' => true, 'unsubscribe' => true]);
                    }
                    Issues::add('success', 'Email(s) Sent');
                    break;
                default:
                    Issues::add('error', 'Invalid Request');
                    break;
            }
        }
        Views::view('admin.contact');
    }
}
