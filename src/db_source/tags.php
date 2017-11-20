<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require __DIR__ . '/classes/TagsModel.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET['get'] == 'all') {
        $tags = TagsModel::getAllTags();
        $data = [];
        $i = 0;
        foreach ($tags as $k) {
            $data[$i]['id'] = $k->data['idtags'];
            $data[$i]['name'] = $k->data['tag_name'];
            $i++;
        }
        exit(json_encode($data));
    } elseif ($_GET['get'] == 'one') {
        $tags = TagsModel::getTag($_GET['word']);
        if ($tags) {
            $data = [];
            $i = 0;
            foreach ($tags as $k) {
                $data[$i]['id'] = $k->data['idtags'];
                $data[$i]['name'] = $k->data['tag_name'];
                $i++;
            }
            exit(json_encode($data));
        } else {
            echo 0;
        }

    }

} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data['post'] = file_get_contents('php://input');
    strip_tags($data['post']);
    if(!empty($data['post'])) {
        $data = json_decode($data['post']);
        $data->tag_name;
        $tags = TagsModel::insertTag($data->tag_name);
        $new = [];
        $new['id'] = $tags;
        $new['name'] = $data->tag_name;
        exit(json_encode($new));
    } else {
        exit('data must be not null');
    }

}

