

<!-- toc -->

- CakePHP for [PHP-CMS Pohse](https://sourceforge.net/projects/pohse/) on OpenShift
    * [Source repository layout](#source-repository-layout)
    * [Compatibility](#compatibility)
    * [License](#license)

<!-- tocstop -->

CakePHP for [PHP-CMS Pohse](https://sourceforge.net/projects/pohse/) on OpenShift
===============================

This is a quickstart CakePHP application for OpenShift v3 that you ''can'' use as a starting point to develop your own application and deploy it on an [OpenShift](https://github.com/openshift/origin) cluster.

If you'd like to install it, follow [these directions](https://github.com/openshift/cakephp-ex/blob/master/README.md#installation).  

It includes a link to [PHP-CMS Pohse](https://sourceforge.net/projects/pohse/) and its [GIT cake-php release](https://bitbucket.org/b23prodtm/php-cms/branch/cake-php). The latter PHP CMS is featuring well-known functionalities as cool as posting some web contents with pictures stored in a database. More features do come thank to the powerful [Cake PHP framework](http://www.cakephp.org).

### <a name="source-repository-layout">Source repository layout</a>

You do not need to change anything in your existing PHP project's repository.
However, if these files exist they will affect the behavior of the build process:

**composer.json**

List of dependencies to be installed with `composer`. The format is documented
[here](https://getcomposer.org/doc/04-schema.md).

### <a name="compatibility">Compatibility</a>

This repository is compatible with PHP 5.6 and higher, excluding any alpha or beta versions.

### <a name="license">License</a>
Copyright 2016 b23production GNU

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

[LICENSE 2.0](http://www.apache.org/licenses/LICENSE-2.0)

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
