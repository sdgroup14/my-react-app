<?php
require __DIR__ . '/DB.php';

class ContactsModel
{
    static protected $table = 'contacts';
    public $data = array();
    public $id = false;

    public function __set ($k, $v)
    {
        $this->data[$k] = $v;
    }

    public function __get ($k)
    {
        return $this->data[$k];
    }

    public static function getAllContacts ()
    {
        $db = new DB;
        $db->setClassName(get_called_class());
        $sql = 'SELECT * FROM websummit.contacts ORDER BY id DESC';
        $res = $db->query($sql);
        if (empty($res)) {
            return false;
        } else {
            return $res;
        }
    }

    public static function getContact ($id)
    {
        $db = new DB;
        $db->setClassName(get_called_class());
        $sql = 'SELECT * FROM websummit.contacts WHERE id = ' . $id;
        $res = $db->query($sql);
        if (empty($res)) {
            return false;
        } else {
            return $res[0];
        }
    }

    public static function getTagName ($id)
    {
        $db = new DB;
        $db->setClassName(get_called_class());
        $sql = 'SELECT idtags AS id, tag_name FROM websummit.tags WHERE idtags IN (' . $id . ')';
        $res = $db->query($sql);
        if (empty($res)) {
            return false;
        } else {
            return $res;
        }
    }

    public static function insertContact ($data)
    {
        $sql = 'INSERT INTO ' . static::$table . ' (title,site,email,country,type,tags) VALUES (:title,:site,:email,:country,:type,:tags)';
        $db = new DB;
        return $db->execute($sql, [':title' => $data->title,':site' => $data->site,':email' => $data->email,':country' => $data->country,':type' => $data->type,':tags' => $data->tags]);
    }

    public static function addTagContact ($id_tag, $id_contact)
    {
        $sql = 'INSERT INTO websummit.contacts_tags (id_tag,id_contact) VALUES (:id_tag,:id_contact)';
        $db = new DB;
        return $db->execute($sql, [':id_tag' => $id_tag,':id_contact' => $id_contact]);
    }
}

