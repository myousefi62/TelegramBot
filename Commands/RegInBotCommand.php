<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\PhotoSize;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\DB;

/**
 * User "/survey" command
 *
 * Command that demonstrated the Conversation funtionality in form of a simple survey.
 */
class reginbotCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'RegInBot';

    /**
     * @var string
     */
    protected $description = 'RegInBot for bot users';

    /**
     * @var string
     */
    protected $usage = '/reginbot';

    /**
     * @var string
     */
    protected $version = '0.3.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Conversation Object
     *
     * @var \Longman\TelegramBot\Conversation
     */
    protected $conversation;

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    /*public function execute()
    {
        $message = $this->getMessage();

        $chat    = $message->getChat();
        $user    = $message->getFrom();
        $text    = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        //Preparing Response
        $data = [
            'chat_id' => $chat_id,
        ];

        if ($chat->isGroupChat() || $chat->isSuperGroup()) {
            //reply to message id is applied by default
            //Force reply is applied by default so it can work with privacy on
            $data['reply_markup'] = Keyboard::forceReply(['selective' => true]);
        }

        //Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        //cache data from the tracking session if any
        $state = 0;
        if (isset($notes['state'])) {
            $state = $notes['state'];
        }

        $result = Request::emptyResponse();

        //State machine
        //Entrypoint of the machine state if given by the track
        //Every time a step is achieved the track is updated
        //$data['reply_markup'] = Keyboard::remove(['selective' => true]);
        switch ($state) {
            //name
            case 0:
                if ($text === '') {
                    $notes['state'] = 0;
                    $this->conversation->update();

                    $data['text']         = '👤 نام خود را ارسال کنید';
                    $data['reply_markup'] = Keyboard::remove(['selective' => true]);

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['name'] = $text;
                $text          = '';
            //surname
            // no break
            case 1:
                if ($text === '') {
                    $notes['state'] = 1;
                    $this->conversation->update();

                    $data['text'] = '👤 نام خانوادگی خود را ارسال کنید';

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['surname'] = $text;
                $text             = '';
            //phone number    
            // no break
            case 2:
                if ($text === '' || !is_numeric($text)) {
                    $notes['state'] = 2;
                    $this->conversation->update();

                    $data['text'] = '☎️ تلفن ثابت';
                    if ($text !== '') {
                        $data['text'] = '☎️ تلفن ثابت به همراه پیش شماره مشابه نمونه 02532925342';
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['phone_number'] = $text;
                $text         = '';
            //mobile number    
            // no break
            case 3:
                if ($text === '' || !is_numeric($text)) {
                    $notes['state'] = 3;
                    $this->conversation->update();

                    $data['text'] = '☎️ تلفن ثابت';
                    if ($text !== '') {
                        $data['text'] = '☎️ تلفن ثابت به همراه پیش شماره مشابه نمونه 09194527844';
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['phone_number'] = $text;
                $text  
            // address
            // no break
            case 4:
                if ($text === '') {
                    $notes['state'] = 4;
                    $this->conversation->update();

                    $data['text']         = '👤 لطفا ادرس خود را کامل وارد کنید';
                    
                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['address'] = $text;
                $text             = '';
            //surname
            // no break

            case 5:
                if ($text === '' || !in_array($text, ['تایید', 'اصلاح'], true)) {
                    $notes['state'] = 5;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard(['تایید', 'اصلاح']))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    //$data['text'] =$notes['state'] .' '.$notes['state']. 'مورد تایید می باشد';
                    if ($text !== '') {
                        $data['text'] = 'در صورت تایید اطلاعات ثبت نام خود بر روی دکمه تایید در غیر این صورت اصلال حر ابزنید با تشکر';
                    }

                    $result = Request::sendMessage($data);
                    break;
                }
                if ($text !== 'اصلاح') {
                    $notes['state'] = 0;
                    $this->conversation->update();

                    $data['text']         = '👤 نام خود را ارسال کنید';
                    $data['reply_markup'] = Keyboard::remove(['selective' => true]);

                    $result = Request::sendMessage($data);
                    break;
                }
                $notes['acssept'] = $text;
            // no break
            case 6:
                $this->conversation->update();
                $out_text = '/Survey result:' . PHP_EOL;
                unset($notes['state']);
                foreach ($notes as $k => $v) {
                    $out_text .= PHP_EOL . ucfirst($k) . ': ' . $v;
                }
         
                $data['reply_markup'] = Keyboard::remove(['selective' => true]);
                $data['caption']      = $out_text;
                //$this->conversation->stop();
                DB::insertUserDetail($user_id, $full_name,$mobile_number, $phone_number, $address, $reagent);
                $result = Request::sendPhoto($data);
                break;
        }
       // DB::insertUserDetail($user_id, $full_name,$mobile_number, $phone_number, $address, $reagent);
//insertUserDetail($user_id, $full_name,$mobile_number, $phone_number, $address, $reagent)
        return $result;
    }*/
    public function execute()
    {
        $message = $this->getMessage();

        $chat    = $message->getChat();
        $user    = $message->getFrom();
        $text    = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        //Preparing Response
        $data = [
            'chat_id' => $chat_id,
        ];

        if ($chat->isGroupChat() || $chat->isSuperGroup()) {
            //reply to message id is applied by default
            //Force reply is applied by default so it can work with privacy on
            $data['reply_markup'] = Keyboard::forceReply(['selective' => true]);
        }

        //Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        //cache data from the tracking session if any
        $state = 0;
        if (isset($notes['state'])) {
            $state = $notes['state'];
        }

        $result = Request::emptyResponse();

        //State machine
        //Entrypoint of the machine state if given by the track
        //Every time a step is achieved the track is updated
        switch ($state) {
            case 0:
                if ($text === '') {
                    $notes['state'] = 0;
                    $this->conversation->update();

                    $data['text']         = '👤 نام خود را ارسال کنید';
                    $data['reply_markup'] = Keyboard::remove(['selective' => true]);

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['name'] = $text;
                $text          = '';

            // no break
              case 1:
                if ($text === '') {
                    $notes['state'] = 1;
                    $this->conversation->update();

                    $data['text'] = '👤 نام خانوادگی خود را ارسال کنید';

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['lastname'] = $text;
                $text             = '';

            // no break
            case 2:
                if ($text === '' || !is_numeric($text)) {
                    $notes['state'] = 2;
                    $this->conversation->update();

                    $data['text'] = 'شماره موبایل خود را وارد کنید';
                    if ($text !== '') {
                        $data['text'] = 'شماره موبایل خود را به شکل صحیح وارد نمایید';
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['mobile'] = $text;
                $text         = '';

            // no break
            case 3:
                if ($text === '' || !is_numeric($text)) {
                    $notes['state'] = 3;
                    $this->conversation->update();

                    $data['text'] = 'شماره تلفن خود را وارد کنید';
                    if ($text !== '') {
                        $data['text'] = 'شماره تلفن خود را به شکل صحیح وارد نمایید';
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['phone'] = $text;
                $text         = '';

            // no break
              case 4:
                if ($text === '') {
                    $notes['state'] = 4;
                    $this->conversation->update();

                    $data['text'] = '👤 ادرس دقیق خود را وارد کنید';

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['address'] = $text;
                $text             = '';
              ///////////////////////
                case 5:
                if ($text === '' || !in_array($text, ['دوستان', 'تبلیغات اینترنتی' ,'تبلیغات پیامکب'], true)) {
                    $notes['state'] = 5;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard(['دوستان', 'تبلیغات اینترنتی' ,'تبلیغات پیامکب']))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = 'معرف خود را انتخاب کنید';
                    if ($text !== '') {
                        $data['text'] = 'از موارد زیر یکی را به عنوان معرف انتخاب کنید';
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['reagent'] = $text;
                ////////////////////
            
            // no break
            case 6:
                if ($text === '' || !in_array($text, ['تایید', 'اصلاح'], true)) {
                    $notes['state'] = 6;
                    $this->conversation->update();
                    unset($notes['state']);
                    $out_text = "اطلاعات وارد شده با با دقت برسی و در صورت امکان تایید کنید." . PHP_EOL;
                    $out_text = "کد کاربر: $user_id" . PHP_EOL;
                    foreach ($notes as $k => $v) {
                       switch ($k) {
                           case 'name':
                               $key = 'نام';
                               break;
                           case 'lastname':
                               $key = 'نام خانوادگی';
                               break;
                           case 'mobile':
                               $key = 'تلفن همراه';
                               break;
                           case 'phone':
                               $key = 'شماره ثابت';
                               break;
                           case 'address':
                               $key = 'آدرس';
                               break;
                           case 'reagent':
                               $key = 'معرف';
                               break;
                           default:
                               # code...
                               break;
                       }
                        
                        $out_text .= PHP_EOL . $key . ': ' . $v;;//ucfirst($k) . ': ' . $v;
                    }
                    $data['reply_markup'] = (new Keyboard(['تایید', 'اصلاح']))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = $out_text.PHP_EOL;
                    if ($text !== '') {
                        $data['text'] = $out_text.PHP_EOL;
                    }

                    $result = Request::sendMessage($data);
                    break;
                }
                if ($text === 'اصلاح') {
                    $notes['state'] = 0;
                    $this->conversation->update();

                    $data['text']         = '👤 نام خود را ارسال کنید';
                    $data['reply_markup'] = Keyboard::remove(['selective' => true]);

                    $result = Request::sendMessage($data);
                    break;
                 }
                    $notes['state'] = 7;
                    $this->conversation->update();
                    
                //$notes['gender'] = $text;
                $text             = '';

            // no break
           
            case 7:
        
                unset($notes['state']);
                $out_text = "اطلاعات وارد شده با با دقت برسی و در صورت امکان تایید کنید." . PHP_EOL;
                $out_text = "کد کاربر: $user_id" . PHP_EOL;
                foreach ($notes as $k => $v) {
                    $out_text .= PHP_EOL . ucfirst($k) . ': ' . $v;
                }
                $data['text']         = $out_text;
                $data['reply_markup'] = Keyboard::remove(['selective' => true]);

                $result = Request::sendMessage($data);
                //print_r($notes);
                $full_name = $notes['name'] .' '.$notes['lastname'];
                $mobile_number=$notes['mobile'];
                $phone_number = $notes['phone'] ;
                $address = $notes['address'] ;
                $reagent = $notes['reagent'] ;
                
                //echo "$user_id, $full_name,$mobile_number, $phone_number, $address, $reagent" ;
                DB::insertUserDetail($user_id, $full_name,$mobile_number, $phone_number, $address, $reagent);
                DB::denyAccessCommand($user_id,'reginbot');
                $this->conversation->update();
                $this->conversation->stop();
                $this->telegram->executeCommand('keyboard');
                break;
        }
        
        return $result;
    }
    private function isphonenumber(int $data)
    {
        if(preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $data)) {
          // $phone is valid
        }
    }
    private function ismobilenumber(int $data)
    {
        if(preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $data)) {
          // $phone is valid
        }
    }
    private function isaddress(string $data)
    {
        if(preg_match("/^[a-zA-Z]$/", $data)) {
          // $phone is valid
        }
    }
}
