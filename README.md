# mmimagemap
a remake of the old MW Imagemap TYPO3 extension for newer TYPO3 versions

This extension does not offer new functionalities.
It is just a new implementation of the old MW Imagemap plugin which should now work in TYPO3 8.x and 9.x.

If someone wants to migrate data from an installation of the old MW Imagemap to the new MM Imagemap - just follow those steps:

1.) Install MM Imagemap. It is presumed that your TYPO3 database must also contain all tables (with data) from the old version.

2.) Immediately after install: open the file [extdir]/mmimagemap/Resources/Private/Php/Migratedata.php for editing.
  
3.) Comment line 15.

4.) call [domainname]/typo3conf/ext/mmimagemap/Resources/Php/Private/Migratedata.php in your browser.
  
5.) uncomment line 15 again.

6.) done :-)
