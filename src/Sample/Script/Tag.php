<?php
/**
 * Tag Data Generator Script for Theme
 * 
 * @package     Pointless
 * @author      ScarWu
 * @copyright   Copyright (c) 2012-2014, ScarWu (http://scar.simcz.tw/)
 * @link        http://github.com/scarwu/Pointless
 */

use NanoCLI\IO;

class Tag {

    /**
     * @var array
     */
    private $list;
    
    public function __construct() {
        $this->list = [];

        foreach(Resource::get('article') as $value) {
            foreach($value['tag'] as $tag) {
                if(!isset($this->list[$tag]))
                    $this->list[$tag] = [];

                $this->list[$tag][] = $value;
            }
        }

        uasort($this->list, function($a, $b) {
            if (count($a) == count($b))
                return 0;

            return count($a) > count($b) ? -1 : 1;
        });
    }
    
    /**
     * Get List
     *
     * @return array
     */
    public function getList() {
        return $this->list;
    }
    
    /**
     * Generate Data
     *
     * @param string
     */
    public function gen() {
        $first = NULL;
        $count = 0;
        $total = count($this->list);
        $key = array_keys($this->list);

        $blog = Resource::get('config')['blog'];
        
        foreach((array)$this->list as $index => $post_list) {
            IO::writeln("Building tag/$index");
            if(NULL == $first) {
                $first = $index;
            }
            
            $post = [];
            $post['title'] = "Tag: $index";
            $post['url'] = "tag/$index";
            $post['list'] = $this->createDateList($post_list);
            $post['bar']['index'] = $count + 1;
            $post['bar']['total'] = $total;

            if(isset($key[$count - 1])) {
                $post['bar']['p_title'] = $key[$count - 1];
                $post['bar']['p_path'] = "{$blog['base']}tag/" . $key[$count - 1];
            }
            
            if(isset($key[$count + 1])) {
                $post['bar']['n_title'] = $key[$count + 1];
                $post['bar']['n_path'] = "{$blog['base']}tag/" . $key[$count + 1];
            }
            
            $count++;

            $container = bindData($post, THEME . '/Template/Container/Tag.php');

            $block = Resource::get('block');
            $block['container'] = $container;

            $ext = [];
            $ext['name'] = "{$post['title']} | {$blog['name']}";
            $ext['url'] = $blog['dn'] . $blog['base'];

            $data = [];
            $data['blog'] = $blog;
            $data['block'] = $block;
            $data['ext'] = $ext;
            
            // Write HTML to Disk
            $result = bindData($data, THEME . '/Template/index.php');
            writeTo($result, TEMP . "/{$post['url']}");

            // Sitemap
            Resource::append('sitemap', $post['url']);
        }

        if(file_exists(TEMP . "/tag/$first/index.html")) {
            copy(TEMP . "/tag/$first/index.html", TEMP . '/tag/index.html');
            Resource::append('sitemap', 'tag');
        }
    }

    private function createDateList($list) {
        $result = [];

        foreach((array)$list as $article) {
            if(!isset($result[$article['year']])) {
                $result[$article['year']] = [];
            }

            if(!isset($result[$article['year']][$article['month']])) {
                $result[$article['year']][$article['month']] = [];
            }

            $result[$article['year']][$article['month']][] = $article;
        }

        return $result;
    }
}
