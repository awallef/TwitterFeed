<?php

App::uses('HttpSocket', 'Network/Http');
define('TWITTER_CACHE_DIR',CACHE . 'socials/');

class TwitterSource extends DataSource {

    protected $count = 0;
    protected $_schema = array(
        'id' => array('type' => 'integer', 'key' => 'primary'),
        'text' => array('type' => 'string'),
        'created_at' => array('type' => 'string')
    );

    public function __construct($config) {
        parent::__construct($config);
        $this->sourceUrl = str_replace('%screen_name%', $this->config['screen_name'], $this->config['statusesAPI']);
        $this->Http = new HttpSocket();
        
        if (!is_dir(TWITTER_CACHE_DIR))
        {
            mkdir(TWITTER_CACHE_DIR,0777);
        }
        
        $engine = 'File';
        if (extension_loaded('apc') && function_exists('apc_dec') && (php_sapi_name() !== 'cli' || ini_get('apc.enable_cli'))) {
                $engine = 'Apc';
        }
        
        Cache::config('_twitter_', array(
                'engine' => $engine,
                'prefix' => $this->cachePrefix,
                'path' => TWITTER_CACHE_DIR,
                'serialize' => ($engine === 'File'),
                'duration' => $this->cacheDuration,
                'groups' => array('socials','twitter')
        ));
    }

    public function listSources() {
        return null;
    }

    public function describe($Model) {
        return $this->_schema;
    }

    public function calculate(Model $model, $func, $params = array()) {
        return 'COUNT';
    }

    public function read($model, $queryData = array()) {

        //debug( $queryData );

        $query = array();

        if ($queryData['fields'] === 'COUNT') {
            return array(array(array('count' => $this->count)));
        }

        if (!empty($queryData['offset'])) {
            $query['start'] = $queryData['offset'];
        }

        if (!empty($queryData['limit'])) {
            $query['num'] = $queryData['limit'];
        }

        $this->_createQuery($query, $queryData);

        //debug($query);
        //go get tweets
        $results = $this->Http->get($this->sourceUrl, $query);
        //debug( $results );
        $results = substr($results->body, 22);
        $results = substr($results, 0, -2);
        $results = json_decode($results, true);
        //debug( $results );
        $this->count = $results['posts-total'];

        $infos = array();
        $infos['title'] = $results['tumblelog']['title'];
        $infos['name'] = $results['tumblelog']['name'];
        $infos['description'] = $results['tumblelog']['description'];
        $infos['posts-start'] = $results['posts-start'] - 1;

        $posts = array();

        foreach ($results['posts'] as $key => $post) {

            $infos['posts-start'] = $infos['posts-start'] + 1;

            $posts[$key] = array();
            $posts[$key]['Tumblr'] = array(
                'id' => $post['id'],
                'num' => $infos['posts-start'],
                'url' => $post['url'],
                'type' => $post['type'],
                'format' => $post['format'],
                'unix-timestamp' => $post['unix-timestamp'],
                'slug' => $post['slug'],
                'title' => $infos['title'],
                'name' => $infos['name'],
                'description' => $infos['description'],
            );

            $posts[$key]['Tumblr']['tags'] = (!empty($post[$key]['tags']) ) ? $post[$key]['tags'] : array();

            $this->_createAssocModel($posts, $post, $key);
        }
        unset($results);
        return $posts;
    }

}