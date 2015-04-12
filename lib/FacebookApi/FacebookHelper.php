<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of FacebookHelper
 *
 * @author Julian Margara
 */
class FacebookHelper {

    static $session;

    public static function init($access_token) {
        Facebook\FacebookSession::setDefaultApplication(
                '1567775303492642', '6dd7567d4887134f154554a0dd4bab46'
        );
        self::$session = new Facebook\FacebookSession($access_token);
    }

    public static function getPages() {
        $request = new Facebook\FacebookRequest(self::$session, 'GET', '/me/accounts', array('fields' => 'id,name,access_token'));
        return $request->execute()->getGraphObject()->asArray()['data'];
    }

    public static function publishPostInPage($post, $page) {
        $request = (new Facebook\FacebookRequest(self::$session, 'POST', '/' . $page['id'] . '/feed', array('access_token' => $page['access_token'], 'link' => $post['link'], 'message' => $post['message'])));
        return (isset($request->execute()->getGraphObject()->asArray()['id']));
    }

    public static function getGroups() {
        $request = new Facebook\FacebookRequest(self::$session, 'GET', '/me/groups', array('fields' => 'id,name'));
        return $request->execute()->getGraphObject()->asArray()['data'];
    }

    public static function publishPostInGroups($post, $group) {
        $request = (new Facebook\FacebookRequest(self::$session, 'POST', '/' . $group['id'] . '/feed', array('link' => $post['link'], 'message' => $post['message'])));
        return (isset($request->execute()->getGraphObject()->asArray()['id']));
    }

    public static function getEvents() {
        $request = new Facebook\FacebookRequest(self::$session, 'GET', '/me/events', array('fields' => 'id,name'));
        return $request->execute()->getGraphObject()->asArray()['data'];
    }

    public static function publishPostInEvents($post, $event) {
        $request = (new Facebook\FacebookRequest(self::$session, 'POST', '/' . $event['id'] . '/feed', array('link' => $post['link'], 'message' => $post['message'])));
        return (isset($request->execute()->getGraphObject()->asArray()['id']));
    }

}
