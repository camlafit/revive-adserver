<?php

/*
+---------------------------------------------------------------------------+
| Revive Adserver                                                           |
| http://www.revive-adserver.com                                            |
|                                                                           |
| Copyright: See the COPYRIGHT.txt file.                                    |
| License: GPLv2 or later, see the LICENSE.txt file.                        |
+---------------------------------------------------------------------------+
*/

require_once MAX_PATH . '/lib/OA/Admin/Settings.php';

class Test_OA_Admin_Settings extends UnitTestCase
{
    public $basePath;

    public function __construct()
    {
        $this->basePath = MAX_PATH . '/var/cache';
    }

    public function setUp()
    {
        // Tests in this class need to use the "real" configuration
        // file writing method, not the one reserved for the test
        // environment...
        $GLOBALS['override_TEST_ENVIRONMENT_RUNNING'] = true;
        $this->serverSave = $_SERVER['HTTP_HOST'];
    }

    public function tearDown()
    {
        // Resume normal service with regards to the configuration file writer...
        unset($GLOBALS['override_TEST_ENVIRONMENT_RUNNING']);
        $_SERVER['HTTP_HOST'] = $this->serverSave;
    }

    public function testIsConfigWritable()
    {
        $oConf = new OA_Admin_Settings(true);

        // 1) Test we can write to an existing file.
        $path = $this->basePath;
        $filename = 'oa_test_' . rand() . '.conf.php';
        $fp = fopen($path . '/' . $filename, 'w');
        fwrite($fp, 'foo');
        fclose($fp);
        $this->assertTrue($oConf->isConfigWritable($path . '/' . $filename));
        unlink($path . '/' . $filename);

        // 2) A non-existing file in an unwriteable location.
        $this->assertFalse($oConf->isConfigWritable($this->basePath . '/non_existent_dir/non_existent_file'));

        // The following tests fail when running as root, exit early
        if (function_exists('posix_getuid') && 0 === posix_getuid()) {
            return;
        }

        // 3) An existing file we don't have write permission for.
        $path = $this->basePath;
        $filename = 'oa_test_' . rand() . '.conf.php';
        $fp = fopen($path . '/' . $filename, 'w');
        fwrite($fp, 'foo');
        fclose($fp);
        chmod($path . '/' . $filename, 0400);
        $this->assertFalse($oConf->isConfigWritable($path . '/' . $filename));
        chmod($path . '/' . $filename, 0700);
        unlink($path . '/' . $filename);

        // 4) An empty directory we can write to.
        $this->assertTrue($oConf->isConfigWritable($this->basePath . '/non_existent_file'));

        // The following test fails when running on Windows, exit early
        if (defined('PHP_OS_FAMILY') && 'Windows' === PHP_OS_FAMILY) {
            return;
        }

        // 5) An empty directory we cannot write to.
        $path = $this->basePath;
        $dirname = 'oa_test_' . rand();
        mkdir($path . '/' . $dirname);
        chmod($path . '/' . $dirname, 0000);
        $this->assertFalse($oConf->isConfigWritable($path . '/' . $dirname . '/non_existent_file'));
        chmod($path . '/' . $dirname, 0700);
        rmdir($path . '/' . $dirname);
    }

    public function testBulkSettingChange()
    {
        $oConf = new OA_Admin_Settings(true);
        $oConf->bulkSettingChange('foo', ['one' => 'bar', 'two' => 'baz']);
        $expected = ['foo' => ['one' => 'bar', 'two' => 'baz']];
        $this->assertEqual($expected, $oConf->aConf);
    }

    public function testSettingChange()
    {
        $oConf = new OA_Admin_Settings(true);
        $oConf->settingChange('group', 'item', 'value');
        $expected = ['group' => ['item' => 'value']];
        $this->assertEqual($expected, $oConf->aConf);
    }

    /**
     * 1. Tests a config file is written out correctly.
     * 2. Tests correct use of "dummy" and "real" config files.
     */
    public function testWriteConfigChange()
    {
        // Test 1.
        $oConf = new OA_Admin_Settings(true);

        // Build the local conf array manually.
        $oConf->aConf['foo'] = ['one' => 'bar', 'two' => 'baz'];
        $oConf->aConf['webpath']['admin'] = 'localhost';
        $oConf->aConf['webpath']['delivery'] = 'localhost';
        $oConf->aConf['webpath']['deliverySSL'] = 'localhost';
        
        // Setup an array in the global $conf
        $GLOBALS['_MAX']['CONF']['foo'] = ['one' => 'bar', 'two' => 'baz'];
        $GLOBALS['_MAX']['CONF']['webpath'] = [];
        
        $filename = 'oa_test_' . rand();
        $this->assertTrue($oConf->writeConfigChange($this->basePath, $filename), 'Error writing config file');

        // The new config file will have been reparsed so global conf should have correct values.
        $oNewConf = new OA_Admin_Settings();
        $this->assertEqual($oConf->aConf, $oNewConf->aConf);

        // Clean up
        unlink($this->basePath . '/localhost.' . $filename . '.conf.php');
        unset($oNewConf);

        // Test 2.0
        // Write out a new "single host" config file
        $oConf = new OA_Admin_Settings(true);
        
        // Build the local conf array manually.
        $oConf->aConf['webpath']['admin'] = 'dummy';
        $oConf->aConf['webpath']['delivery'] = 'dummy';
        $oConf->aConf['webpath']['deliverySSL'] = 'dummy';
        $_SERVER['HTTP_HOST'] = $oConf->aConf['webpath']['delivery'];
        
        $this->assertTrue($oConf->writeConfigChange($this->basePath), 'Error writing config file');
        $this->assertTrue(file_exists($this->basePath . '/dummy.conf.php'), 'Config file does not exist');

        // Modify delivery settings to a different host
        $oConf->aConf['webpath']['delivery'] = 'delivery';
        $oConf->aConf['webpath']['deliverySSL'] = 'delivery';
        $_SERVER['HTTP_HOST'] = $oConf->aConf['webpath']['delivery'];

        $this->assertTrue($oConf->writeConfigChange($this->basePath), 'Error writing config file');
        $this->assertTrue(file_exists($this->basePath . '/dummy.conf.php'), 'Dummy config file does not exist');
        $this->assertTrue(file_exists($this->basePath . '/delivery.conf.php'), 'Real config file does not exist');

        // Test both config files are correct
        $aRealConfig = parse_ini_file($this->basePath . '/delivery.conf.php', true);
        $aDummyConfig = parse_ini_file($this->basePath . '/dummy.conf.php', true);
        $this->assertEqual($oConf->aConf, $aRealConfig, 'Real config has incorrect values');
        // Note - The behaviour has changed to persist any 'overridden' values in the wrapper config files
        $aExpected = ['realConfig' => 'delivery'];
        $this->assertEqual($aExpected['realConfig'], $aDummyConfig['realConfig'], 'Dummy config has incorrect values');

        // Modify the delivery to use three different hosts
        $GLOBALS['_MAX']['CONF']['webpath']['delivery'] = 'delivery';
        $oConf->aConf['webpath']['admin'] = 'admin';
        $oConf->aConf['webpath']['delivery'] = 'newhost';
        $oConf->aConf['webpath']['deliverySSL'] = 'newSSLhost';
        $_SERVER['HTTP_HOST'] = $oConf->aConf['webpath']['delivery'];

        $this->assertTrue($oConf->writeConfigChange($this->basePath), 'Error writing config file');

        // Test the files have been correctly created/deleted
        $this->assertTrue(file_exists($this->basePath . '/admin.conf.php'), 'Dummy admin config file does not exist');
        $this->assertTrue(file_exists($this->basePath . '/newhost.conf.php'), 'Real config file does not exist');
        $this->assertTrue(file_exists($this->basePath . '/newSSLhost.conf.php'), 'Dummy SSL delivery file does not exist');
        $this->assertFalse(file_exists($this->basePath . '/delivery.conf.php'), 'Old real config file was not removed');

        // Test config files are correct
        $aRealConfig = parse_ini_file($this->basePath . '/newhost.conf.php', true);
        $aDummyAdminConfig = parse_ini_file($this->basePath . '/admin.conf.php', true);
        $aDummySSLConfig = parse_ini_file($this->basePath . '/newSSLhost.conf.php', true);
        $this->assertEqual($oConf->aConf, $aRealConfig, 'Real config has incorrect values');
        $aExpected = ['realConfig' => 'newhost'];
        $this->assertEqual($aExpected['realConfig'], $aDummyAdminConfig['realConfig'], 'Dummy admin config has incorrect values');
        $this->assertEqual($aExpected['realConfig'], $aDummySSLConfig['realConfig'], 'Dummy SSL config has incorrect values');

        // File should have been cleaned up by test.
        $this->assertFalse(file_exists(($this->basePath . '/delivery.conf.php')));

        // Clean up
        unlink($this->basePath . '/admin.conf.php');
        unlink($this->basePath . '/default.' . $filename . '.conf.php');
        unlink($this->basePath . '/newhost.conf.php');
        unlink($this->basePath . '/newSSLhost.conf.php');
    }

    /**
     * Check that the mechanism to detect unrecognised config files works as expected
     *
     */
    public function test_findOtherConfigFiles()
    {
        // Test 1.
        $oConf = new OA_Admin_Settings(true);

        // Build the local conf array manually.
        $oConf->aConf['foo'] = ['one' => 'bar', 'two' => 'baz'];
        $oConf->aConf['webpath']['admin'] = 'localhost2';
        $oConf->aConf['webpath']['delivery'] = 'localhost';
        $oConf->aConf['webpath']['deliverySSL'] = 'localhost3';
        $filename = 'oa_test_' . rand();
        $folder = $this->basePath . '/oa_test_' . rand();
        mkdir($folder);
        $this->assertEqual([], $oConf->findOtherConfigFiles($folder, $filename), 'Unexpected un-recognised config files detected');

        //Check that if there is an admin config file, it it recognised
        touch($folder . '/' . $oConf->aConf['webpath']['admin'] . $filename . '.conf.php');
        $this->assertEqual([], $oConf->findOtherConfigFiles($folder, $filename), 'Unexpected un-recognised config files detected');

        // Same for a deliverySSL config file:
        touch($folder . '/' . $oConf->aConf['webpath']['deliverySSL'] . $filename . '.conf.php');
        $this->assertEqual([], $oConf->findOtherConfigFiles($folder, $filename), 'Unexpected un-recognised config files detected');

        $unrecognisedFilename = $folder . '/localhost4.' . $filename . '.conf.php';
        touch($unrecognisedFilename);
        $this->assertNotEqual([], $oConf->findOtherConfigFiles($folder, $filename), 'Expected un-recognised config files NOT detected');

        // Cleanup
        unlink($folder . '/' . $oConf->aConf['webpath']['admin'] . $filename . '.conf.php');
        unlink($folder . '/' . $oConf->aConf['webpath']['deliverySSL'] . $filename . '.conf.php');
        unlink($unrecognisedFilename);

        rmdir($folder);
    }

    public function testMergeConfigChanges()
    {
        // Build a test dist.conf.php
        $oDistConf = new OA_Admin_Settings(true);

        $oDistConf->aConf['foo'] = ['one' => 'bar', 'two' => 'baz', 'new' => 'additional_value'];
        $oDistConf->aConf['webpath']['admin'] = 'disthost';
        $oDistConf->aConf['webpath']['delivery'] = 'disthost';
        $oDistConf->aConf['webpath']['deliverySSL'] = 'disthost';
        $oDistConf->aConf['new'] = ['new_key' => 'new_value'];

        $distFilename = 'oa_test_dist' . rand();
        $this->assertTrue($oDistConf->writeConfigChange($this->basePath, $distFilename, false), 'Error writing config file');

        // Build a test user conf
        $oUserConf = new OA_Admin_Settings(true);

        $oUserConf->aConf['foo'] = ['one' => 'bar', 'two' => 'baz', 'old' => 'old_value'];
        $oUserConf->aConf['deprecated'] = ['old_key' => 'old_value'];
        $oUserConf->aConf['webpath']['admin'] = 'localhost';
        $oUserConf->aConf['webpath']['delivery'] = 'localhost';
        $oUserConf->aConf['webpath']['deliverySSL'] = 'localhost';

        $userFilename = 'oa_test_user' . rand();
        $this->assertTrue($oUserConf->writeConfigChange($this->basePath, $userFilename), 'Error writing config file');

        $expected = ['foo' => ['one' => 'bar',
                                         'two' => 'baz',
                                         'new' => 'additional_value'],
                          'webpath' => ['admin' => 'localhost',
                                             'delivery' => 'localhost',
                                             'deliverySSL' => 'localhost'],
                          'new' => ['new_key' => 'new_value']];

        $this->assertEqual(
            $expected,
            $oUserConf->mergeConfigChanges($this->basePath . '/disthost.' . $distFilename . '.conf.php'),
            'Config files don\'t match'
        );

        // Clean up
        unlink($this->basePath . '/disthost.' . $distFilename . '.conf.php');
        unlink($this->basePath . '/localhost.' . $userFilename . '.conf.php');
        unlink($this->basePath . '/default.' . $distFilename . '.conf.php');
    }

    /**
     * Tests the config file is backed up.
     *
     */
    public function testBackupConfig()
    {
        $oConfig = new OA_Admin_Settings(true);

        $originalFilename = 'oa_test_' . rand() . '.conf.php';
        $directory = $this->basePath;
        touch($directory . '/' . $originalFilename);
        $now = date("Ymd");
        $expected = $now . '_old.' . $originalFilename;
        $this->assertTrue($oConfig->backupConfig($directory . '/' . $originalFilename));
        $this->assertTrue(file_exists($directory . '/' . $expected));

        $this->assertTrue($oConfig->backupConfig($directory . '/' . $originalFilename));
        $expected0 = $now . '_0_old.' . $originalFilename;
        $this->assertTrue(file_exists($directory . '/' . $expected0));

        $this->assertTrue($oConfig->backupConfig($directory . '/' . $originalFilename));
        $expected1 = $now . '_1_old.' . $originalFilename;
        $this->assertTrue(file_exists($directory . '/' . $expected1));

        // Clean up
        unlink($this->basePath . '/' . $originalFilename);
        unlink($this->basePath . '/' . $expected);
        unlink($this->basePath . '/' . $expected0);
        unlink($this->basePath . '/' . $expected1);

        // Test a .ini file
        $originalFilename = 'oa_test_' . rand() . '.conf.ini';
        $directory = $this->basePath;
        touch($directory . '/' . $originalFilename);
        $now = date("Ymd");
        $expected = $now . '_old.' . $originalFilename . '.php';
        $this->assertTrue($oConfig->backupConfig($directory . '/' . $originalFilename));
        $this->assertTrue(file_exists($directory . '/' . $expected));
        $this->assertEqual(';<' . '?php exit; ?>' . "\r\n", file_get_contents($directory . '/' . $expected));

        // Clean up
        unlink($this->basePath . '/' . $originalFilename);
        unlink($this->basePath . '/' . $expected);
    }

    /**
     * Tests the correct backup filename is generated.
     *
     */
    public function test_getBackupFilename()
    {
        // Test when backup filename doesn't already exist.
        $originalFilename = 'oa_test_' . rand() . '.conf.php';
        $directory = $this->basePath;
        $now = date("Ymd");
        touch($directory . '/' . $originalFilename);
        $expected = $now . '_old.' . $originalFilename;
        $this->assertEqual(
            $expected,
            OA_Admin_Settings::_getBackupFilename($directory . '/' . $originalFilename),
            'Filenames don\'t match'
        );

        // Test when backup filename already exists.
        $existingBackupFile = $expected;
        touch($directory . '/' . $existingBackupFile);
        //$expected = $existingBackupFile . '_0';
        $expected0 = $now . '_0_old.' . $originalFilename;
        $this->assertEqual(
            $expected0,
            OA_Admin_Settings::_getBackupFilename($directory . '/' . $originalFilename),
            'Filenames don\'t match'
        );

        // Clean up
        unlink($directory . '/' . $originalFilename);
        unlink($directory . '/' . $existingBackupFile);


        // Test when .ini backup filename doesn't already exist.
        $originalFilename = 'oa_test_' . rand() . '.conf.ini';
        $directory = $this->basePath;
        $now = date("Ymd");
        touch($directory . '/' . $originalFilename);
        $expected = $now . '_old.' . $originalFilename . '.php';
        $this->assertEqual(
            $expected,
            OA_Admin_Settings::_getBackupFilename($directory . '/' . $originalFilename),
            'Filenames don\'t match'
        );

        // Clean up
        unlink($directory . '/' . $originalFilename);
    }
}
