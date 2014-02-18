<?php
/**
 * Category Data Generator Script for Theme
 * 
 * @package     Pointless
 * @author      ScarWu
 * @copyright   Copyright (c) 2012-2014, ScarWu (http://scar.simcz.tw/)
 * @link        http://github.com/scarwu/Pointless
 */

use NanoCLI\IO;

class Category {

    /**
     * @var array
     */
    private $list;
    
    public function __construct() {
        $this->list = [];
        
        foreach(Resource::get('article') as $value) {
            if(!isset($this->list[$value['category']]))
                $this->list[$value['category']] = [];

            $this->list[$value['category']][] = $value;
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
        $keys = array_keys($this->list);

        $blog = Resource::get('config')['blog'];
        
        foreach((array)$this->list as $index => $post_list) {
            IO::writeln("Building category/$index");
            if(NULL == $first) {
                $first = $index;
            }

            $post = [];
            $post['title'] ="Category: $index";
            $post['url'] = "category/$index";
            $post['list'] = $this->createDateList($post_list);
            $post['bar']['index'] = $count + 1;
            $post['bar']['total'] = $total;
            
            if(isset($keys[$count - 1])) {
                $category = $keys[$count - 1];

                $post['bar']['p_title'] = $category;
                $post['bar']['p_url'] = "{$blog['base']}category/$category";
            }

            if(isset($keys[$count + 1])) {
                $category = $keys[$count + 1];

                $post['bar']['n_title'] = $category;
                $post['bar']['n_url'] = "{$blog['base']}category/$category";
            }
            
            $count++;

            $data = [];
            $data['blog'] = $blog;
            $data['post'] = $post;

            $container = bindData($data, THEME . '/Template/Container/Category.php');

            $block = Resource::get('block');
            $block['container'] = $container;

            $ext = [];
            $ext['title'] = "{$post['title']} | {$blog['name']}";
            $ext['url'] = $blog['dn'] . $blog['base'];

            $data = [];
            $data['blog'] = array_merge($blog, $ext);
            $data['block'] = $block;
            
            // Write HTML to Disk
            $result = bindData($data, THEME . '/Template/index.php');
            writeTo($result, TEMP . "/{$post['url']}");

            // Sitemap
            Resource::append('sitemap', $post['url']);
        }

        if(file_exists(TEMP . "/category/$first/index.html")) {
            copy(TEMP . "/category/$first/index.html", TEMP . "/category/index.html");
            Resource::append('sitemap', 'category');
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