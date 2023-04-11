<?php
// db/authormapper.php

namespace OCA\NextRCP\Db;

use OCP\IDBConnection;

use OCA\NextRCP\AppFramework\Db\CompatibleMapper;

class UserToProjectMapper extends CompatibleMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'nextrcp_user_to_project');
    }


    public function find($id) {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_user_to_project` ' .
            'WHERE `id` = ?';
            
            try {
                $e = $this->findEntity($sql, [$id]);
                return $e;
            } catch (\OCP\AppFramework\Db\DoesNotExistException $e){
                return null;
            }
        
    }

    public function findAllForUser($uid) {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_user_to_project` ' .
            'WHERE `user_uid` = ?';
            
            try {
                $e = $this->findEntities($sql, [$uid]);
                return $e;
            } catch (\OCP\AppFramework\Db\DoesNotExistException $e){
                return null;
            }
        
    }
    public function findAllForProject($pid) {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_user_to_project` ' .
            'WHERE `project_id` = ?';
            
            try {
                $e = $this->findEntities($sql, [$pid]);
                return $e;
            } catch (\OCP\AppFramework\Db\DoesNotExistException $e){
                return null;
            }
        
    }
    public function findForUserAndProject($uid, $project) {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_user_to_project` ' .
            'WHERE `user_uid` = ? and project_id = ?';
            
            try {
                $e = $this->findEntity($sql, [$uid, $project->id]);
                return $e;
            } catch (\OCP\AppFramework\Db\DoesNotExistException $e){
                return null;
            }
        
    }
    public function deleteAllForProject($project_id) {
        $sql = 'delete FROM `*PREFIX*nextrcp_user_to_project` ' .
            ' where project_id = ?';
            
            try {
                $this->execute($sql, [$project_id]);
                return;
            } catch (\OCP\AppFramework\Db\DoesNotExistException $e){
                return;
            }
        
    }

    
   
}
