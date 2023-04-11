<?php
// db/authormapper.php

namespace OCA\NextRCP\Db;

use OCP\IDBConnection;

use OCA\NextRCP\AppFramework\Db\CompatibleMapper;

class ProjectMapper extends CompatibleMapper {

    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'nextrcp_project');
    }


    public function findByName($name) {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_project` ' .
            'WHERE upper(`name`) = ?';

            try {
                $e = $this->findEntity($sql, [strtoupper($name)]);
                return $e;
            } catch (\OCP\AppFramework\Db\DoesNotExistException $e){
                return null;
            }

    }

    public function searchByName($user, string $name) {
        $name = strtoupper($name);
        $sql = 'SELECT tp.* FROM `*PREFIX*nextrcp_project` tp LEFT JOIN `*PREFIX*nextrcp_user_to_project` up ON up.project_id = tp.id WHERE up.`user_uid` = ? AND upper(tp.`name`) LIKE ? ORDER BY tp.`name`';

        return $this->findEntities($sql, [$user,"%" . $name ."%"]);

    }

    /**
     * @throws \OCP\AppFramework\Db\DoesNotExistException if not found
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException if more than one result
     */
    public function find($id) {
        $sql = 'SELECT * FROM `*PREFIX*nextrcp_project` ' .
            'WHERE `id` = ?';
        return $this->findEntity($sql, [$id]);
    }


    public function findAll($user,$getArchived = 0){
        if ($getArchived) {
            $sql = 'SELECT tp.* FROM `*PREFIX*nextrcp_project` tp left join `*PREFIX*nextrcp_user_to_project` up '.
                'on up.project_id = tp.id where up.user_uid = ? order by tp.name asc';
        } else {
            $sql = 'SELECT tp.* FROM `*PREFIX*nextrcp_project` tp left join `*PREFIX*nextrcp_user_to_project` up '.
                'on up.project_id = tp.id where up.user_uid = ? and tp.archived != 1 order by tp.name asc';
        }
        return $this->findEntities($sql, [$user]);
    }
    public function findAllAdmin($getArchived = 0){
        if ($getArchived) {
            $sql = 'SELECT tp.* FROM `*PREFIX*nextrcp_project` tp order by tp.name asc';
        } else {
            $sql = 'SELECT tp.* FROM `*PREFIX*nextrcp_project` tp where tp.archived != 1 order by tp.name asc';
        }
        return $this->findEntities($sql, []);
    }

    public function delete($project_id) {
        $sql = 'delete FROM `*PREFIX*nextrcp_project` ' .
            ' where id = ?';

            try {
                $this->execute($sql, [$project_id]);
                return;
            } catch (\OCP\AppFramework\Db\DoesNotExistException $e){
                return;
            }

    }
}
