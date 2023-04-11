<?php
// db/authormapper.php

namespace OCA\NextRCP\Db;

use OCP\IDBConnection;

use OCA\NextRCP\AppFramework\Db\CompatibleMapper;

class TimelineMapper extends CompatibleMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'nextrcp_timeline');
    }


    /**
     * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one result
     */
    public function find($id) {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_timeline` ' .
            'WHERE `id` = ?';
        return $this->findEntity($sql, [$id]);
    }



    public function findAll($uid) {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_timeline` ' .
            'WHERE `user_uid` = ?';
            
            try {
                $e = $this->findEntities($sql, [$uid]);
                return $e;
            } catch (\OCP\AppFramework\Db\DoesNotExistException $e){
                return null;
            }
        
    }

    public function findLatest() {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_timeline` ' .
            'order by created_at desc limit 100';
            
            try {
                $e = $this->findEntities($sql, []);
                return $e;
            } catch (\OCP\AppFramework\Db\DoesNotExistException $e){
                return null;
            }
        
    }

    public function findByStatus($status) {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_timeline` ' .
            'WHERE `status` = ?';
            
            try {
                $e = $this->findEntities($sql, [$status]);
                return $e;
            } catch (\OCP\AppFramework\Db\DoesNotExistException $e){
                return null;
            }
        
    }
}
