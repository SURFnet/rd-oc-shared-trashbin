# Shared trashbin POC

## clone impersonate app

```
git submodule update --init owncloud_apps/impersonate
```

## Build, start and initialize

Build

```
docker-compose build
```

Start

```
docker-compose up
```

Initialize

```
docker exec shared_trash-owncloud-1 /app/init.sh
```

The initialization script creates 3 groups with different users (password = username)

| Group          | User             | Role          |
|:---------------|:-----------------|:--------------|
| bioinformatics | f_bioinformatics | admin         |
|                | jennifer         | 0 B Quota     |
|                | katharine        | 0 B Quota     |
|                | dawne            | Default Quota |
|                | noella           | Default Quota |
| astrophysics   | f_astrophysics   | admin         |
|                | jolynn           | 0 B Quota     |
|                | deanne           | 0 B Quota     |
|                | willia           | Default Quota |
|                | craig            | Default Quota |
| biochemistry   | f_biochemistry   | admin         |
|                | lucretia         | 0 B Quota     |
|                | sherrie          | 0 B Quota     |
|                | babara           | Default Quota |
|                | stevie           | Default Quota |
|                |                  |               |


## Test

1. login as a "0 B Quota" user
2. upload some files to shared folder
3. remove file
4. check trashbin
5. logout and login as different "0 B Quota" user in the same group
6. check trashbin

## Todo

Implement trashbin operations such as restore / delete etc.
