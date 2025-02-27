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

require_once MAX_PATH . '/lib/OA/Dll/Agency.php';
require_once MAX_PATH . '/lib/OA/Dll/AgencyInfo.php';
require_once MAX_PATH . '/lib/OA/Dll/tests/util/DllUnitTestCase.php';

/**
 * A class for testing DLL Agency methods
 *
 * @package    OpenXDll
 * @subpackage TestSuite
 */


class OA_Dll_AgencyTest extends DllUnitTestCase
{
    /**
     * Errors
     *
     */
    public $unknownIdError = 'Unknown agencyId Error';
    public $duplicateAgencyNameError = 'Agency name must be unique';
    public $invalidLanguageError = 'Invalid language';
    public $invalidStatusError = 'Invalid status';

    /**
     * The constructor method.
     */
    public function __construct()
    {
        parent::__construct();
        Mock::generatePartial(
            'OA_Dll_Agency',
            'PartialMockOA_Dll_Agency_AgencyTest',
            ['checkPermissions']
        );
    }

    public function tearDown()
    {
        DataGenerator::cleanUp();
    }

    /**
     * A method to test Add, Modify and Delete.
     */
    public function testAddModifyDelete()
    {
        $dllAgencyPartialMock = new PartialMockOA_Dll_Agency_AgencyTest($this);

        $dllAgencyPartialMock->setReturnValue('checkPermissions', true);
        $dllAgencyPartialMock->expectCallCount('checkPermissions', 12);

        $oAgencyInfo = new OA_Dll_AgencyInfo();

        $oAgencyInfo->agencyName = 'testAgency';
        $oAgencyInfo->password = 'password';
        $oAgencyInfo->contactName = 'Mike';

        // Add
        $this->assertTrue(
            $dllAgencyPartialMock->modify($oAgencyInfo),
            $dllAgencyPartialMock->getLastError()
        );

        $this->assertTrue($oAgencyInfo->accountId);
        $this->assertEqual($oAgencyInfo->status, OA_ENTITY_STATUS_RUNNING);

        // Try adding a duplicate agency name.
        $oDupeAgencyInfo = new OA_Dll_AgencyInfo();
        $oDupeAgencyInfo->agencyName = $oAgencyInfo->agencyName;
        $oDupeAgencyInfo->password = $oAgencyInfo->password;
        $oDupeAgencyInfo->contactName = $oAgencyInfo->contactName;

        $this->assertTrue(
            ($dllAgencyPartialMock->modify($oDupeAgencyInfo) &&
            $dllAgencyPartialMock->getLastError() != $this->duplicateAgencyNameError),
            !$this->_getMethodShouldReturnError($this->duplicateAgencyNameError)
        );


        // Modify
        $oAgencyInfo->agencyName = 'modified Agency';

        $this->assertTrue(
            $dllAgencyPartialMock->modify($oAgencyInfo),
            $dllAgencyPartialMock->getLastError()
        );

        // Try to modify to a duplicate agency name.
        $this->assertTrue(
            $dllAgencyPartialMock->modify($oDupeAgencyInfo),
            $dllAgencyPartialMock->getLastError()
        );
        $oDupeAgencyInfo->agencyName = 'modified Agency';
        $this->assertTrue(
            ($dllAgencyPartialMock->modify($oDupeAgencyInfo) &&
            $dllAgencyPartialMock->getLastError() != $this->duplicateAgencyNameError),
            !$this->_getMethodShouldReturnError($this->duplicateAgencyNameError)
        );

        // Suspend
        $oAgencyInfo->status = OA_ENTITY_STATUS_PAUSED;
        $this->assertTrue(
            $dllAgencyPartialMock->modify($oAgencyInfo),
            $dllAgencyPartialMock->getLastError()
        );

        // Inactive
        $oAgencyInfo->status = OA_ENTITY_STATUS_INACTIVE;
        $this->assertTrue(
            $dllAgencyPartialMock->modify($oAgencyInfo),
            $dllAgencyPartialMock->getLastError()
        );

        // Unexpected status
        $oAgencyInfo->status = 99;
        $this->assertTrue(
            ($dllAgencyPartialMock->modify($oDupeAgencyInfo) &&
            $dllAgencyPartialMock->getLastError() != $this->invalidStatusError),
            !$this->_getMethodShouldReturnError($this->invalidStatusError)
        );

        // Delete (both of the agencies)
        $this->assertTrue(
            $dllAgencyPartialMock->delete($oAgencyInfo->agencyId),
            $dllAgencyPartialMock->getLastError()
        );
        $this->assertTrue(
            $dllAgencyPartialMock->delete($oDupeAgencyInfo->agencyId),
            $dllAgencyPartialMock->getLastError()
        );

        // Modify not existing id
        $this->assertTrue(
            (!$dllAgencyPartialMock->modify($oAgencyInfo) &&
                          $dllAgencyPartialMock->getLastError() == $this->unknownIdError),
            $this->_getMethodShouldReturnError($this->unknownIdError)
        );

        // Delete not existing id
        $this->assertTrue(
            (!$dllAgencyPartialMock->delete($oAgencyInfo->agencyId) &&
                           $dllAgencyPartialMock->getLastError() == $this->unknownIdError),
            $this->_getMethodShouldReturnError($this->unknownIdError)
        );

        $dllAgencyPartialMock->tally();
    }

    public function testAddAgencyWithoutUser()
    {
        $dllAgencyPartialMock = new PartialMockOA_Dll_Agency_AgencyTest($this);

        $dllAgencyPartialMock->setReturnValue('checkPermissions', true);
        $dllAgencyPartialMock->expectCallCount('checkPermissions', 2);

        $oAgencyInfo = new OA_Dll_AgencyInfo();

        $oAgencyInfo->agencyName = 'testAgency';
        //$oAgencyInfo->contactName = 'Bob';
        //$oAgencyInfo->userEmail = 'bob@example.com';
        $this->assertTrue(
            $dllAgencyPartialMock->modify($oAgencyInfo),
            $dllAgencyPartialMock->getLastError()
        );

        $this->assertTrue($dllAgencyPartialMock->delete($oAgencyInfo->agencyId));

        $dllAgencyPartialMock->tally();

        DataGenerator::cleanUp(['agency', 'users']);
    }

    public function testAddAgencyWithUser()
    {
        $dllAgencyPartialMock = new PartialMockOA_Dll_Agency_AgencyTest($this);

        $dllAgencyPartialMock->setReturnValue('checkPermissions', true);
        $dllAgencyPartialMock->expectCallCount('checkPermissions', 3);

        $oAgencyInfo = new OA_Dll_AgencyInfo();

        $oAgencyInfo->agencyName = 'testAgency';
        $oAgencyInfo->contactName = 'Bob';
        $oAgencyInfo->username = 'user';
        $oAgencyInfo->userEmail = 'bob@example.com';
        $oAgencyInfo->password = '';
        $oAgencyInfo->language = 'de';

        // Add user without password
        $this->assertFalse(
            $dllAgencyPartialMock->modify($oAgencyInfo),
            $dllAgencyPartialMock->getLastError()
        );

        // Add user with password
        $oAgencyInfo->password = 'pass';

        $this->assertTrue(
            $dllAgencyPartialMock->modify($oAgencyInfo),
            $dllAgencyPartialMock->getLastError()
        );

        $this->assertTrue($oAgencyInfo->accountId);
        $doUsers = OA_Dal::factoryDO('users');
        $doUsers->default_account_id = $oAgencyInfo->accountId;
        $doUsers->find(true);
        $this->assertEqual(1, $doUsers->count(), 'Should be one user found.');
        $this->assertEqual($oAgencyInfo->username, $doUsers->username, 'Username does not match.');
        $this->assertEqual($oAgencyInfo->userEmail, $doUsers->email_address, 'User email does not match.');
        $this->assertEqual($oAgencyInfo->language, $doUsers->language, 'Language does not match.');

        // Because the password gets unset.
        $this->assertTrue(\RV\Manager\PasswordManager::verifyPassword('pass', $doUsers->password), 'Password does not match.');

        // Test a dodgy language
        $oBadLanguageInfo = clone $oAgencyInfo;

        $oBadLanguageInfo->language = 'BAD_LANGUAGE';
        $this->assertTrue(
            (!$dllAgencyPartialMock->modify($oBadLanguageInfo) &&
            $dllAgencyPartialMock->getLastError() == $this->invalidLanguageError),
            $this->_getMethodShouldReturnError($this->invalidLanguageError)
        );

        $dllAgencyPartialMock->tally();

        DataGenerator::cleanUp(['agency', 'users']);
    }

    /**
     * A method to test get and getList method.
     */
    public function testGetAndGetList()
    {
        $dllAgencyPartialMock = new PartialMockOA_Dll_Agency_AgencyTest($this);

        $dllAgencyPartialMock->setReturnValue('checkPermissions', true);
        $dllAgencyPartialMock->expectCallCount('checkPermissions', 6);

        $oAgencyInfo1 = new OA_Dll_AgencyInfo();
        $oAgencyInfo1->agencyName = 'test name 1';
        $oAgencyInfo1->contactName = 'contact';
        $oAgencyInfo1->password = 'password';
        $oAgencyInfo1->emailAddress = 'name@domain.com';

        $oAgencyInfo2 = new OA_Dll_AgencyInfo();
        $oAgencyInfo2->agencyName = 'test name 2';
        $oAgencyInfo2->password = 'password';
        // Add
        $this->assertTrue(
            $dllAgencyPartialMock->modify($oAgencyInfo1),
            $dllAgencyPartialMock->getLastError()
        );

        $this->assertTrue(
            $dllAgencyPartialMock->modify($oAgencyInfo2),
            $dllAgencyPartialMock->getLastError()
        );

        $oAgencyInfo1Get = null;
        $oAgencyInfo2Get = null;
        // Get
        $this->assertTrue(
            $dllAgencyPartialMock->getAgency($oAgencyInfo1->agencyId, $oAgencyInfo1Get),
            $dllAgencyPartialMock->getLastError()
        );
        $this->assertTrue(
            $dllAgencyPartialMock->getAgency($oAgencyInfo2->agencyId, $oAgencyInfo2Get),
            $dllAgencyPartialMock->getLastError()
        );

        // Check field value
        $this->assertFieldEqual($oAgencyInfo1, $oAgencyInfo1Get, 'agencyName');
        $this->assertFieldEqual($oAgencyInfo1, $oAgencyInfo1Get, 'contactName');
        $this->assertFieldEqual($oAgencyInfo1, $oAgencyInfo1Get, 'emailAddress');
        $this->assertNull(
            $oAgencyInfo1Get->password,
            'Field \'password\' must be null'
        );
        $this->assertFieldEqual($oAgencyInfo2, $oAgencyInfo2Get, 'agencyName');
        $this->assertTrue($oAgencyInfo1Get->accountId);
        $this->assertTrue($oAgencyInfo2Get->accountId);

        // Get List
        $aAgencyList = [];
        $this->assertTrue(
            $dllAgencyPartialMock->getAgencyList($aAgencyList),
            $dllAgencyPartialMock->getLastError()
        );
        $this->assertEqual(
            count($aAgencyList) == 2,
            '2 records should be returned'
        );
        $oAgencyInfo1Get = $aAgencyList[0];
        $oAgencyInfo2Get = $aAgencyList[1];
        if ($oAgencyInfo1->agencyId == $oAgencyInfo2Get->agencyId) {
            $oAgencyInfo1Get = $aAgencyList[1];
            $oAgencyInfo2Get = $aAgencyList[0];
        }
        // Check field value from list
        $this->assertFieldEqual($oAgencyInfo1, $oAgencyInfo1Get, 'agencyName');
        $this->assertFieldEqual($oAgencyInfo2, $oAgencyInfo2Get, 'agencyName');
        $this->assertTrue($oAgencyInfo1Get->accountId);
        $this->assertTrue($oAgencyInfo2Get->accountId);


        // Delete
        $this->assertTrue(
            $dllAgencyPartialMock->delete($oAgencyInfo1->agencyId),
            $dllAgencyPartialMock->getLastError()
        );

        // Get not existing id
        $this->assertTrue(
            (!$dllAgencyPartialMock->getAgency($oAgencyInfo1->agencyId, $oAgencyInfo1Get) &&
                          $dllAgencyPartialMock->getLastError() == $this->unknownIdError),
            $this->_getMethodShouldReturnError($this->unknownIdError)
        );

        $dllAgencyPartialMock->tally();
    }

    /**
     * Method to run all tests for agency statistics
     *
     * @access private
     *
     * @param string $methodName  Method name in Dll
     */
    public function _testStatistics($methodName)
    {
        $dllAgencyPartialMock = new PartialMockOA_Dll_Agency_AgencyTest($this);

        $dllAgencyPartialMock->setReturnValue('checkPermissions', true);
        $dllAgencyPartialMock->expectCallCount('checkPermissions', 5);

        $oAgencyInfo = new OA_Dll_AgencyInfo();

        $oAgencyInfo->agencyName = 'testAgency';
        $oAgencyInfo->password = 'password';

        // Add
        $this->assertTrue(
            $dllAgencyPartialMock->modify($oAgencyInfo),
            $dllAgencyPartialMock->getLastError()
        );

        // Get no data
        $rsAgencyStatistics = null;
        $this->assertTrue($dllAgencyPartialMock->$methodName(
            $oAgencyInfo->agencyId,
            new Date('2001-12-01'),
            new Date('2007-09-19'),
            false,
            $rsAgencyStatistics
        ), $dllAgencyPartialMock->getLastError());

        $this->assertTrue(isset($rsAgencyStatistics));
        if (is_array($rsAgencyStatistics)) {
            $this->assertEqual(count($rsAgencyStatistics), 0, 'No records should be returned');
        } else {
            $this->assertEqual($rsAgencyStatistics->getRowCount(), 0, 'No records should be returned');
        }

        // Test for wrong date order
        $rsAgencyStatistics = null;
        $this->assertTrue(
            (!$dllAgencyPartialMock->$methodName(
                $oAgencyInfo->agencyId,
                new Date('2007-09-19'),
                new Date('2001-12-01'),
                false,
                $rsAgencyStatistics
            ) &&
            $dllAgencyPartialMock->getLastError() == $this->wrongDateError),
            $this->_getMethodShouldReturnError($this->wrongDateError)
        );

        // Delete
        $this->assertTrue(
            $dllAgencyPartialMock->delete($oAgencyInfo->agencyId),
            $dllAgencyPartialMock->getLastError()
        );

        // Test statistics for not existing id
        $rsAgencyStatistics = null;
        $this->assertTrue(
            (!$dllAgencyPartialMock->$methodName(
                $oAgencyInfo->agencyId,
                new Date('2001-12-01'),
                new Date('2007-09-19'),
                false,
                $rsAgencyStatistics
            ) &&
            $dllAgencyPartialMock->getLastError() == $this->unknownIdError),
            $this->_getMethodShouldReturnError($this->unknownIdError)
        );

        $dllAgencyPartialMock->tally();
    }

    /**
     * A method to test getAgencyDailyStatistics.
     */
    public function testDailyStatistics()
    {
        $this->_testStatistics('getAgencyDailyStatistics');
    }

    /**
     * A method to test getAgencyHourlyStatistics.
     */
    public function testHourlyStatistics()
    {
        $this->_testStatistics('getAgencyHourlyStatistics');
    }

    /**
     * A method to test getAgencyAdvertiserStatistics.
     */
    public function testAdvertiserStatistics()
    {
        $this->_testStatistics('getAgencyAdvertiserStatistics');
    }

    /**
     * A method to test getAgencyCampaignStatistics.
     */
    public function testCampaignStatistics()
    {
        $this->_testStatistics('getAgencyCampaignStatistics');
    }

    /**
     * A method to test getAgencyBannerStatistics.
     */
    public function testBannerStatistics()
    {
        $this->_testStatistics('getAgencyBannerStatistics');
    }

    /**
     * A method to test getAgencyPublisherStatistics.
     */
    public function testPublisherStatistics()
    {
        $this->_testStatistics('getAgencyPublisherStatistics');
    }

    /**
     * A method to test getAgencyZoneStatistics.
     */
    public function testZoneStatistics()
    {
        $this->_testStatistics('getAgencyZoneStatistics');
    }
}
