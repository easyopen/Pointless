<?php
/**
 * Pointless Edit Command
 * 
 * @package     Pointless
 * @author      ScarWu
 * @copyright   Copyright (c) 2012-2014, ScarWu (http://scar.simcz.tw/)
 * @link        http://github.com/scarwu/Pointless
 */

namespace Pointless;

use NanoCLI\Command;
use NanoCLI\IO;
use Resource;

class EditCommand extends Command {
    public function __construct() {
        parent::__construct();
    }

    public function help() {
        IO::writeln('    edit       - Edit article');
        IO::writeln('    edit -s    - Edit Static Page');
    }
    
    public function run() {
        if(!checkDefaultBlog())
            return;
        
        initBlog();
        
        $editor = Resource::get('config')['editor'];
        
        $data = [];
        $handle = opendir(MARKDOWN);
        while($filename = readdir($handle)) {
            if('.' == $filename || '..' == $filename || !preg_match('/.md$/', $filename))
                continue;

            preg_match(REGEX_RULE, file_get_contents(MARKDOWN . "/$filename"), $match);
            $temp = json_decode($match[1], TRUE);

            if($this->hasOptions('s')) {
                if('static' != $temp['type'])
                    continue;

                $data[$temp['title']]['publish'] = $temp['publish'];
                $data[$temp['title']]['title'] = $temp['title'];
                $data[$temp['title']]['path'] = MARKDOWN . "/$filename";
            }
            else {
                if('article' != $temp['type'])
                    continue;

                $index = $temp['date'] . $temp['time'];

                $data[$index]['publish'] = $temp['publish'];
                $data[$index]['title'] = $temp['title'];
                $data[$index]['date'] = $temp['date'];
                $data[$index]['path'] = MARKDOWN . "/$filename";
            }
        }
        closedir($handle);

        if(count($data) == 0) {
            IO::writeln('No post(s).', 'red');
            return;
        }

        uksort($data, 'strnatcasecmp');

        $count = 0;
        foreach($data as $key => $article) {
            if($this->hasOptions('s')) {
                $msg = $article['title'];
            }
            else {
                $msg = "{$article['date']} {$article['title']}";
            }

            if($article['publish']) {
                IO::writeln(sprintf("[ %3d] ", $count) . $msg);
            }
            else {
                IO::writeln(sprintf("[*%3d] ", $count) . $msg);
            }
            
            $data[$count++] = $article;
            unset($data[$key]);
        }
        
        $number = IO::question("\nEnter Number:\n-> ", NULL, function($answer) use($data) {
            return is_numeric($answer) && $answer >= 0 && $answer < count($data);
        });

        system("$editor {$data[$number]['path']} < `tty` > `tty`");
    }
}
