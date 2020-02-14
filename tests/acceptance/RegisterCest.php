<?php

/**
 * Class RegisterCest
 *
 * @SuppressWarnings(PHPMD)
 * @codingStandardsIgnoreStart
 */
class RegisterCest
{
    const EMAIL = 'john.doe@example.com';
    const ADMIN_EMAIL = 'admin@yuustar.com';
    const UNVERIFIED_EMAIL = 'homer@simpson.com';

    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function iOpenTheRegisterPage(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->click('Register');
        $I->see('Register a new membership');
        $I->seeLink('I already have a membership');
        $I->seeCurrentUrlEquals('/register');
    }

    public function iCheckAValidator(AcceptanceTester $I)
    {
        $I->amOnPage('/register');
        $I->click('Register');
        $I->see('The email field is required.');
        $I->see('The terms conditions must be accepted.');
        $I->seeCurrentUrlEquals('/register');
    }

    public function iRegisterAndConfirmAnAccount(AcceptanceTester $I)
    {
        $I->amOnPage('/register');
        $I->fillField('email', self::EMAIL);
        $I->click('/html/body/div[1]/div[2]/form/div[2]/div/label');
        $I->click('Register');
        $I->seeCurrentUrlEquals('/');

        $user_in_db = $I->fromDB()->from('users')->where('email', self::EMAIL)->first();

        $I->receiveAnEmailToEmail(self::EMAIL);
        $I->canSeeInEmailHtmlBody('<h3>Hello john.doe! Welcome to our website. </h3>');
        $activation_link = 'http://127.0.0.1:8000/users/' . self::EMAIL . '/verify/' . $user_in_db->verification_code;
        $I->canSeeInEmailHtmlBody($activation_link);
    }

    public function iSpecifyMyPasswordAndSkipTourAndGoToFollowingPage(AcceptanceTester $I)
    {
        $user_in_db = $I->fromDB()->from('users')->where('email', self::EMAIL)->first();
        $activation_link = 'http://127.0.0.1:8000/users/' . self::EMAIL . '/verify/' . $user_in_db->verification_code;
        $I->amOnUrl($activation_link);
        $I->waitForText('Please, enter your password and username to continue', 10);
        $I->fillField('username', '');
        $I->fillField('password', '');
        $I->click('button');
        $I->see('The username field is required.');
        $I->see('The password field is required.');

        $I->fillField('username', 'john.doe1');
        $I->fillField('password', '123');
        $I->click('button');
        $I->see('Sorry, the password needs to have at least one capital letter, one special character and one digit and should have length at least 8 and at most 15 characters.');

        $I->fillField('password', 'SuperPass!1');
        $I->click('button');
        $I->seeCurrentUrlEquals('/welcome');
        $I->waitForText('Welcome to our site!');

        $I->seeLink('Skip');
        $I->click('Skip');
        $I->seeCurrentUrlEquals('/following');

    }

    public function CheckIfNotValidatedUserCantBeLoggedIn(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->click('Login');
        $I->see('Yuustar Admin');
        $I->see('Sign in to start your session');

        $I->submitForm('form', ['email' => self::UNVERIFIED_EMAIL, 'password' => 'password!']);

        $I->see('Yuustar Admin');
        $I->see('Sign in to start your session');
        $I->see('These credentials do not match our records.');

        $I->amOnPage('/login');

        $I->submitForm('form', ['email' => self::ADMIN_EMAIL, 'password' => 'password!']);
        $I->seeCurrentUrlEquals('/following');
    }

}
