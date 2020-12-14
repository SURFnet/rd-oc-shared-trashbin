#!/bin/bash
set -x
# set -e

mysql_cmd="mysql -h db -u owncloud --password=owncloud  owncloud"

###########################
# create user and set quota
create_user() {
    user=$1
    group=$2
    quota=$3
    OC_PASS=$user occ user:add --password-from-env $user --group $group
    if [ ! -z "$quota" ]
    then
        echo "UPDATE oc_accounts  SET quota='0 B' WHERE user_id='$user'"  | $mysql_cmd
    fi
}

share_folder() {
    f_user=$1;
    f_pass=$2
    group=$3
    # create shared folder
    curl -u $f_user:$f_pass -X MKCOL "http://127.0.0.1:8080/remote.php/dav/files/$f_user/shared"

    # get file id of shared folder
    fileid=$( ( echo "SELECT oc_filecache.fileid FROM oc_storages, oc_filecache "
                echo "WHERE id ='home::$f_user' AND "
                echo "oc_filecache.storage=oc_storages.numeric_id AND "
                echo "oc_filecache.path = 'files/shared'" ) | \
                  $mysql_cmd --skip-column-names )
    # share file
    ( echo "INSERT INTO oc_share "
      echo "SET share_type=1, share_with='$group', uid_owner='$f_user', uid_initiator='$f_user',"
      echo "item_type='folder', item_source=$fileid, file_source=$fileid,"
      echo "file_target='/shared', permissions=31" ) | \
        $mysql_cmd
}

# enable helper app
occ app:enable group_lookup

# Create research groups and functional accounts
for group in bioinformatics astrophysics biochemistry
do
    f_user=f_$group
    f_pass=$f_user
    occ group:add $group
    OC_PASS=$f_pass occ user:add --password-from-env $f_user --group $group
    echo "INSERT INTO oc_group_admin SET gid='$group', uid='$f_user';" | $mysql_cmd
    share_folder $f_user $f_pass $group
done

####################################
#
# Create researchers in each group
#
####################################

# bioinformatics
for i in jennifer katharine
do
    create_user $i bioinformatics "0 B"
done

for i in dawne noella jennifer
do
    create_user $i bioinformatics
done

# astrophysics
for i in jolynn deanne
do
    create_user $i astrophysics "0 B"
done

for i  in willia craig
do
    create_user $i astrophysics
done

# biochemistry
for i in lucretia sherrie
do
    create_user $i biochemistry "0 B"
done

for i in babara stevie
do
    create_user $i biochemistry
done
