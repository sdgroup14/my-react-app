<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require __DIR__ . '/classes/ContactsModel.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET['get'] == 'all') {
        $contacts = ContactsModel::getAllContacts();
        $data = [];
        $i = 0;
        foreach ($contacts as $k) {
            $data[$i]['id'] = $k->id;
            $data[$i]['title'] = $k->data['title'];
            $data[$i]['site'] = $k->data['site'];
            $data[$i]['email'] = $k->data['email'];
            $data[$i]['country'] = $k->data['country'];
            $data[$i]['type'] = $k->data['type'];
            $i++;
        }
        exit(json_encode($data));
    } elseif ($_GET['get'] == 'one') {
        $contact = ContactsModel::getContact($_GET['id']);
        if ($contact) {
            $data = [];
            $data['id'] = $contact->id;
            $data['title'] = $contact->data['title'];
            $data['site'] = $contact->data['site'];
            $data['email'] = $contact->data['email'];
            $data['country'] = $contact->data['country'];
            $data['type'] = $contact->data['type'];
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
        $new = new StdClass;
        $new->title = $data->title;
        $new->site = $data->site;
        $new->email = $data->email;
        $new->country = $data->country;
        $new->type = $data->desc;

        $tags = ContactsModel::getTagName($data->tags);

        $t = [];
        $i = 0;
        foreach ($tags as $k) {
            array_push($t, '{"id":' . $k->id . ',"name":"' . $k->data['tag_name'] . '"}');
        }
        $new->tags = implode(',',$t);

        $id_contact = ContactsModel::insertContact($new);
        if ($id_contact) {
            $data->tags = explode(',', $data->tags);
            foreach ($data->tags as $k => $v) {
                ContactsModel::addTagContact($v,$id_contact);
            }
            $contact = ContactsModel::getContact($id_contact);
            if ($contact) {
                $data = [];
                $data['id'] = $contact->id;
                $data['title'] = $contact->data['title'];
                $data['site'] = $contact->data['site'];
                $data['email'] = $contact->data['email'];
                $data['country'] = $contact->data['country'];
                $data['type'] = $contact->data['type'];
                $data['tags'] = $contact->data['tags'];
                exit(json_encode($data));
            } else {
                echo 0;
            }
        } else {
            echo 0;
        }
    } else {
        exit('data must be not null');
    }

}

