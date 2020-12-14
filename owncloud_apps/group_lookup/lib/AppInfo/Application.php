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
//        $sql = 'SELECT *PREFIX*group_admin.uid AS uid ' .
//               'FROM *PREFIX*group_user, *PREFIX*group_admin ' .
//               'WHERE *PREFIX*group_user.gid = *PREFIX*group_admin.gid AND *PREFIX*group_user.uid = ?';
        $sql = "SELECT *PREFIX*share.uid_owner AS uid " .
               "FROM *PREFIX*share " .
               "WHERE *PREFIX*share.uid_owner like 'f_%' AND *PREFIX*share.uid_owner = *PREFIX*share.uid_initiator AND *PREFIX*share.share_with = ?";

        $result = $connection->executeQuery($sql, [$user]);
        $project = $result->fetch();


        // If we have multiple results, then we have to choose for which projectfolder we wants view the trashbin
        $rows = $result->fetchAll();
        foreach ($rows as $row) {
            \OC::$server->getLogger()->debug(
               'We will use Project UID TEST "' .  print_r($row, true) . '" for trashbin listing.' ,
                ['app' => 'group_lookup']
              );
        }

        if($project)
        {
            \OC::$server->getLogger()->debug(
               'We will use Project UID "' . $project['uid'] . '" for trashbin listing.' ,
                ['app' => 'group_lookup']
              );
            return $project['uid'];
        }
    }

    \OC::$server->getLogger()->debug(
        'No project found, we will use User UID "' . $user . '" for trashbin listing.',
        ['app' => 'group_lookup']
    );

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

    \OC::$server->getLogger()->debug(
        'Dir of "' . $user . '" is "'.$dir.'".',
        ['app' => 'group_lookup']
    );

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

