<?php
use Codeception\Exception\Incomplete;

/**
 * Class LoginCest
 *
 * @SuppressWarnings(PHPMD)
 * @codingStandardsIgnoreStart
 */
class LoginCest
{
    const UNVERIFIED_USER_ID = 2;
    public function _before(AcceptanceTester $I)
    {

    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testIfVerifiedCanLogin(AcceptanceTester $I)
    {
        $I->adminLogin();
    }

    public function testIfUnverifiedCanNotLogin(AcceptanceTester $I)
    {
        $I->loginAs(self::UNVERIFIED_USER_ID, 'password!', true);
    }
}
