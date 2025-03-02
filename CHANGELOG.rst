Changelog
---------

5.0.0
~~~~~

1) [FEATURE] Add support for three new types of loaders: ``file_phar``, ``package`` and ``get``.
2) [DEPRECATED] Deprecate special autoload. It is not needed anymore in newer versions of deployer.

4.0.1
~~~~~

1) [TASK] Code cleanup.

4.0.0
~~~~~

1) [TASK] Allow to install package in different folder than `vendor`, fe. in local folder.
2) [TASK] Extend the condition with filenames indicating root dir.

3.0.0
~~~~~

1) [TASK] Simplify autoload.php with require instead of include.
2) [TASK][BREAKING] Remove not needed public method requireFile.
3) [TASK][BREAKING] Make loader to load files alphabetically.
4) [TASK][BREAKING] Rename public method requireFilesFromDirectoryRecursively because of typo.

2.0.1
~~~~~

1) [BUGFIX] Fix wrong exception with multiple repositories with same namespace prefix (sourcebroker/deployer-loader/pull/1)
2) [TASK] Refactor autoload.php and FileUtility / Load classes.
3) [TASK] Refactor docs.

2.0.0
~~~~~

1) Remove dependency to deployer.

1.0.3
~~~~~

1) Check if namespace is not empty.

1.0.2
~~~~~

1) Improving documentation.


1.0.1
~~~~~

1) Improving documentation.


1.0.0
~~~~~

1) Initial version
