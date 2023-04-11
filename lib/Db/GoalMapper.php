<?php
// db/authormapper.php

namespace OCA\NextRCP\Db;

use OCP\IDBConnection;

use OCA\NextRCP\AppFramework\Db\CompatibleMapper;

class GoalMapper extends CompatibleMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'nextrcp_goal');
    }


    public function findByUserProject($userUid, $projectId) {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_goal` ' .
            'WHERE  `user_uid` = ? and `project_id` = ?';
            
            try {
                $e = $this->findEntity($sql, [$userUid, $projectId]);
                return $e;
            } catch (\OCP\AppFramework\Db\DoesNotExistException $e){
                return null;
            }
        
    }

    /**
     * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one result
     */
    public function find($id) {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_goal` ' .
            'WHERE `id` = ?';
        return $this->findEntity($sql, [$id]);
    }


    public function findAll($user){
        $sql = 'SELECT tg.*,p.name as project_name FROM `*PREFIX*nextrcp_goal` tg  join `*PREFIX*nextrcp_project` p on p.id = tg.project_id where tg.user_uid = ? order by tg.created_at desc';
        return $this->findEntities($sql, [$user]);
    }

   
}
