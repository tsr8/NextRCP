<?php
// db/authormapper.php

namespace OCA\NextRCP\Db;

use OCP\IDBConnection;

use OCA\NextRCP\AppFramework\Db\CompatibleMapper;

class UserToClientMapper extends CompatibleMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'nextrcp_user_to_client');
    }


    public function find($id) {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_user_to_client` ' .
            'WHERE `id` = ?';
            
            try {
                $e = $this->findEntity($sql, [$id]);
                return $e;
            } catch (\OCP\AppFramework\Db\DoesNotExistException $e){
                return null;
            }
        
    }

    public function findAllForUser($uid) {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_user_to_client` ' .
            'WHERE `user_uid` = ?';
            
            try {
                $e = $this->findEntities($sql, [$uid]);
                return $e;
            } catch (\OCP\AppFramework\Db\DoesNotExistException $e){
                return null;
            }
        
    }
    
    public function findForUserAndClient($uid, $client) {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_user_to_client` ' .
            'WHERE `user_uid` = ? and client_id = ?';
            
            try {
                $e = $this->findEntity($sql, [$uid, $client->id]);
                return $e;
            } catch (\OCP\AppFramework\Db\DoesNotExistException $e){
                return null;
            }
        
    }

    
   
}
