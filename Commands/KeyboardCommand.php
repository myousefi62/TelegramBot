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
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\DB;

/**
 * User "/keyboard" command
 *
 * Display a keyboard with a few buttons.
 */
class KeyboardCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'keyboard';

    /**
     * @var string
     */
    protected $description = 'لیست دستورات در دسترس کاربر بر اساس سطوح دسترسی';

    /**
     * @var string
     */
    protected $usage = '/keyboard';

    /**
     * @var string
     */
    protected $version = '0.2.0';
/**
     * @var bool
     */
    protected $need_mysql = true;
    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        //Keyboard examples
        /** @var Keyboard[] $keyboards */
        $user_id = $this->getMessage()->getFrom()->getId();
        $result = DB::getAllCommand($user_id); 
      // $media[][]=null;
    if(count($result)>0){   
        $custemkeyboards = [];
        $i = 0 ;
       foreach ($result as $item) {
           $custemkeyboards[$i]=$item['text_fa'];
           $i++;
           //print_r($item);
           //echo $item['text_fa'];
         //  $media[][]= $item['text_en'];
           //$keyboards[] =new Keyboard($item['text_en']);
           //$keyboard9[] =new Keyboard($item['text_en']);

       }

       $i = 0 ;
       $j = 0 ;
       foreach ($result as $item) {
           if ($i >= 4) {
               $i = 0 ;
               $j ++ ;
           }
           $custemkeyboards1[$j][$i]=$item['text_fa'];
           $i++;
           

       }
//print_r(json_encode($custemkeyboards1));
       //print_r($media);
//print_r($media);
  }
        //print_r($result);
/*$keyboard110 = array(
        'keyboard' => array(
                array("سرور مجازی", "سرور اختصاصی"),
                array("درباره ما", "تماس با ما","تبلیغات"),
        ),'one_time_keyboard'=>true,'resize_keyboard'=>true);
$keyboard110 = json_encode($keyboard);*/
  // echo "$keyboard110";

        $keyboards = [];
//$keyboards = new Keyboard($keyboard110);
        foreach ($custemkeyboards1 as $item) {
            $keyboards[0] = new Keyboard($item);
           // print_r($item);
            //echo PHP_EOL;
        }
        $keyboards[1] = new Keyboard($custemkeyboards);
       // $keyboards[10] = new Keyboard($custemkeyboards1);
        //$keyboards[] = new Keyboard($custemkeyboards1);
       // $keyboard1[] = new Keyboard($custemkeyboards);
//   from
        //echo $this->getMessage()->getFrom()->getId();
        
        /*print_r($this->getMessage()->getFrom->getId);
        echo PHP_EOL."user_id : $user_id ". PHP_EOL ;*/
        //Example 0
        //Example 0
        $keyboards[2] = new Keyboard(
            ['7', '8', '9','1'],
            ['4', '5']
        );
/*
        //Example 1
        $keyboards[] = new Keyboard(
            ['7', '8', '9', '+'],
            ['4', '5', '6', '-'],
            ['1', '2', '3', '*'],
            [' ', '0', ' ', '/']
        );*/

        //Example 2

        /*$testkey = [];
        $testkey[0]="ali";
        $testkey[1]="hosein";*/
       // $keyboards[] = new Keyboard('A','d','b');
/*        $keyboard1[] = new Keyboard('A','d','b');
        $keyboard88[] = new Keyboard($testkey);
        
        $keyboards[] =$keyboard88;

*/        //$keyboard1[] =new Keyboard('b');
        //$keyboard1[] =new Keyboard('d');
        //$keyboard2[] = new Keyboard(json_encode(array('keyboard' => $media)));
/*        echo PHP_EOL.json_encode($keyboard1).PHP_EOL;
        echo PHP_EOL.json_encode($keyboard88).PHP_EOL;
*/       // echo PHP_EOL.json_encode($keyboard2).PHP_EOL;
        //Example 3
/*        $keyboards[] = new Keyboard(
            ['text' => 'A'],
            'B',
            ['C', 'D']
        );*/

        //Example 4 (bots version 2.0)
        /*$keyboards[] = new Keyboard(
                                        [
                                            ['text' => 'Send my contact', 'request_contact' => true],
                                            ['text' => 'Send my location', 'request_location' => true],
                                            
                                        ],
                                        
                                    );  */
            //Example 5
            //$keyboards[] = new Keyboard(json_encode(array('keyboard' => $media)));

//echo PHP_EOL.json_encode($keyboards[10]);        
//echo PHP_EOL.json_encode($keyboards[1]);        
echo PHP_EOL.json_encode($keyboards[2]);        
/*echo mt_rand(0, count($keyboards) - 1).PHP_EOL;
print_r($keyboards[mt_rand(0, count($keyboards) - 1)]);*/
//print_r($keyboards);
        //Return a random keyboard.
        $keyboard = $keyboards[1]//$keyboards[mt_rand(0, count($keyboards) - 1)]
            ->setResizeKeyboard(true)
            ->setOneTimeKeyboard(true)
            ->setSelective(false);
        
          /*$keyboards1[] = new Keyboard(json_encode(array('keyboard' => $media)));
print_r( $keyboards1);*/
//json_encode(array('keyboard' => $media))



/*String[][] $keys = new String[2][];
$keys[0] = new String[2];
$keys[0][0] = "Top-left";
$keys[0][1] = "Top-right";
$keys[1] = new String[3];
$keys[1][0] = "Bottom-left";
$keys[1][1] = "Bottom-center";
$keys[1][2] = "Bottom-right";


*/





        $chat_id = $this->getMessage()->getChat()->getId();
        $data    = [
            'chat_id'      => $chat_id,
            'text'         => 'Press a Button:',
            'reply_markup' =>  $keyboard,
            /*$custemkeyboards1 ,
            'one_time_keyboard'=>true,
            'resize_keyboard'=>true//*/
        ];

        return Request::sendMessage($data);
    }
}
