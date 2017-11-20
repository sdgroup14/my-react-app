<?php

class DB
{
    public $settings = array();

    //DB handle
    private $dbh = null;

    //Имя класса
    private  $className = 'stdClass';

    //Соединение с БД
    public function __construct()
    {
        $this->settings = require (__DIR__ . '/dbConfig.php');
        $this->dbh = new PDO('mysql:host=' . $this->settings['host'] . '; charset=UTF8; dbname=' . $this->settings['dbname'], $this->settings['user'], $this->settings['pass']);
    }

    //Запрос к серверу
    public function query( $sql, $params = array() )
    {
        //Statement handler
        $sth = null;

        //Подготовка запроса и выполнение
        $sth = $this->dbh->prepare($sql);
        $sth->execute($params);

        //Возвращение выборки из БД
        if ( !$sth ) return false;
        return $sth->fetchAll(PDO::FETCH_CLASS, $this->className);
    }

    //Запрос к серверу
    public function execute( $sql, $params = array() )
    {
        //Statement handler
        $sth = null;

        //Подготовка запроса и выполнение
        $sth = $this->dbh->prepare($sql);
        $sth->execute($params);
        return $this->dbh->lastInsertId();
    }

    //Запрос к серверу
    public function rowCount( $sql, $params = array() )
    {
        //Statement handler
        $sth = null;

        //Подготовка запроса и выполнение
        $sth = $this->dbh->prepare($sql);
        return $sth->execute($params);
    }

    //Функция имени класса
    public function setClassName($className)
    {
        $this->className = $className;
    }
}