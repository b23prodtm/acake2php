<!-- toc -->

- [Apache/PHP7 on Docker](#https://hub.docker.com/repository/docker/betothreeprod/raspberrypi3-php)
    + [Source repository layout](#source-repository-layout)
    + [Compatibility](#compatibility)
    + [License](#license)

<!-- tocstop -->

Apache/PHP7 on Docker
===============================

* **.htaccess**

  To allow Apache server to browse directly to the app/webroot folder on server-side, use mod_rewrite rules, as provided by .htaccess files.

  >/.htaccess

      <IfModule mod_rewrite.c>
        RewriteEngine on
        # Uncomment if you have a .well-known directory in the root folder, e.g. for the Let's Encrypt challenge
        # https://tools.ietf.org/html/rfc5785
        #RewriteRule ^(\.well-known/.*)$ $1 [L]
        RewriteRule ^$ app/webroot/ [L]
        RewriteRule (.*) app/webroot/$1 [L]
      </IfModule>

* **.env files**

  Set environment variables as the following arguments, for instance on MacOS X:

      ./deploy.sh amd64 --nobuild

  Use a .env file in shell to configure up with RaspberryPI3 hosts :

      ./deploy.sh arm32 --nobuild

  .env -> arm32v7.env

      ./deploy.sh arm32 --balena

### Compatibility

* PHP 7 's recommended, excluding any alpha or beta versions.
* Container builder: docker-compose 1.19 and DockerFile version 2
* Mysql 5.7 and later (or MariaDB 10.1 and later)
* Cloud Platforms:
  + Openshift 3
  + Balena Cloud
  + Kubernetes (not provided)

### License
   Copyright 2019 www.b23prodtm.info

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

   * [Apache License, Version 2.0](http://www.apache.org/licenses/LICENSE-2.0)

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
