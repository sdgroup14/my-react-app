<?php
require __DIR__ . '/DB.php';

class TagsModel
{
    static protected $table = 'tags';
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

    public static function getAllTags ()
    {
        $db = new DB;
        $db->setClassName(get_called_class());
        $sql = 'SELECT * FROM websummit.tags';
        $res = $db->query($sql);
        if (empty($res)) {
            return false;
        } else {
            return $res;
        }
    }

    public static function getTag ($word)
    {
        $db = new DB;
        $db->setClassName(get_called_class());
        $sql = 'SELECT * FROM websummit.tags WHERE tag_name LIKE "%' . $word . '%"';
        $res = $db->query($sql);
        if (empty($res)) {
            return false;
        } else {
            return $res;
        }
    }

    public static function insertTag ($tag_name)
    {
        $sql = 'INSERT INTO ' . static::$table . ' (tag_name) VALUES (:tag_name)';
        $db = new DB;
        return $db->execute($sql, [':tag_name' => $tag_name]);
    }
}