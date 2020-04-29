<?php

namespace OCA\group_lookup\AppInfo;
use \OCP\AppFramework\App;

class Application extends App
{
    public function __construct(array $urlParams=array()){
       parent::__construct('group_lookup', $urlParams);
    }
};


function getFunctionalAccount($user)
{
    $connection = \OC::$server->getDatabaseConnection();
    $result = $connection->executeQuery('SELECT `quota` FROM `*PREFIX*accounts` WHERE `user_id` = ?',
                                        [$user]);
    $udata = $result->fetch();
    if($udata['quota'] == "0 B")
    {
        $sql = 'SELECT *PREFIX*group_admin.uid AS uid ' .
               'FROM *PREFIX*group_user, *PREFIX*group_admin ' .
               'WHERE *PREFIX*group_user.gid = *PREFIX*group_admin.gid AND *PREFIX*group_user.uid = ?';
        $result = $connection->executeQuery($sql, [$user]);
        $admin = $result->fetch();
        if($admin)
        {
            return $admin['uid'];
        }
    }
    return $user;
}

function getFileTrashbinFileId($dir, $user)
{
    $connection = \OC::$server->getDatabaseConnection();
    $fuser = getFunctionalAccount($user);
    $result = $connection->executeQuery('SELECT `numeric_id` FROM `*PREFIX*storages` WHERE `id` = ?',
                                        ["home::$fuser"]);
    $storage_id = -1;
    $storage = $result->fetch();
    if($storage)
    {
        $storage_id = $storage['numeric_id'];
    }
    $dir = \trim(\OC_Util::normalizeUnicode($dir), '/');
    list($dummy, $dir) = explode('/', $dir, 2);
    $result = $connection->executeQuery('SELECT `fileid` '.
                                        'FROM `*PREFIX*filecache` '.
                                        'WHERE `storage` = ? AND `path_hash` = ?',
                                        [$storage_id, \md5($dir)]);
    $file_id = -1;
    $file = $result->fetch();
    if($file)
    {
        return $file['fileid'];
    }
    return -1;
};

