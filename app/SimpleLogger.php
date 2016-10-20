<?php
/**
 * Created by PhpStorm.
 * User: aleksey
 * Date: 20/10/16
 * Time: 21:20
 */

namespace App;


class SimpleLogger {

    public function __construct($fileName) {
        $this->fileName = $fileName;

        $this->dirName = realpath(dirname(__FILE__)."/../storage/logs");
    }

    public function Log($string, $addLineBreak = true) {
        try {
            $fp = @fopen($this->dirName.'/'.$this->fileName, 'a');
            if($fp) {
                $now = new \DateTime('now');
                $logString = "[{$now->format('Y-m-d H:i:s')}] {$string}".($addLineBreak ? "\r\n" : '');
                fwrite($fp, $logString);
                fclose($fp);
            }
        } catch (\Exception $e) {

        }
    }


    private $dirName;

    private $fileName;
}