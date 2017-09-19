

<!-- toc -->

- [CakePHP Sample App on OpenShift](#cakephp-sample-app-on-openshift)
  * [OpenShift Considerations](#openshift-considerations)
    + [Security](#security)
    + [Installation:](#installation)
    + [Source repository layout](#source-repository-layout)
    + [Compatibility](#compatibility)
    + [License](#license)

<!-- tocstop -->

CakePHP on OpenShift
===============================

This is a quickstart CakePHP application for OpenShift v3 that you ''can'' use as a starting point to develop your own application and deploy it on an [OpenShift](https://github.com/openshift/origin) cluster.

If you'd like to install it, follow [these directions](https://github.com/openshift/cakephp-ex/blob/master/README.md#installation).  

### Source repository layout

You do not need to change anything in your existing PHP project's repository.
However, if these files exist they will affect the behavior of the build process:

* **composer.json**

  List of dependencies to be installed with `composer`. The format is documented
  [here](https://getcomposer.org/doc/04-schema.md).

### Compatibility

This repository is compatible with PHP 5.6 and higher, excluding any alpha or beta versions.

### License
This code is dedicated to the public domain to the maximum extent permitted by applicable law, pursuant to [CC0](http://creativecommons.org/publicdomain/zero/1.0/).
