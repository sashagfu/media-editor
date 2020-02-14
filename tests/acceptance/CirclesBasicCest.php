<?php
use App\Models\Circle;

class CirclesBasicCest
{
    CONST CIRCLE_TITLE = 'Circle title';
    CONST CIRCLE_DESCRIPTION = 'Circle description';
    const CIRCLE_CREATE_FORM = 'form.create-circle__form';
    const CIRCLE_CREATE_TITLE_INPUT = 'input.crate-circle__title';
    const CIRCLE_CREATE_DESCRIPTION = 'textarea.create-circle__description';
    const CIRCLE_CREATE_COVER_INPUT = 'input#cover';
    const CIRCLE_CREATE_SUBMIT = '.create-circle__submit';
    const CIRCLE_SETTINGS_FORM = 'form.circle-settings__form';
    const CIRCLE_SETTINGS_TITLE_INPUT = 'input.circle-settings__title';
    const CIRCLE_SETTINGS_DESCRIPTION = 'textarea.circle-settings__description';
    const CIRCLE_SETTINGS_COVER_INPUT = 'input#cover';
    const CIRCLE_SETTINGS_SUBMIT = '.circle-settings__submit';
    const PRIVACY_LABEL_SETTINGS = 'a#privacy-label';
    const CIRCLES_PAGE_DESCRIPTION_SELECTOR = 'p.circle-container__description';
    const CIRCLES_PAGE_URL_SELECTOR = 'a.circle-container__single-url';
    const CIRCLE_MENU_LINK_SELECTOR = 'a.circle-menu__settings';

    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testIfUserCanCreateCircle(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->amOnPage('/circles/create');
        $I->seeElement(self::CIRCLE_CREATE_FORM);

        // Check validator
        $I->click(self::CIRCLE_CREATE_SUBMIT);
        $I->wait(3);
        $I->seeCurrentUrlEquals('/circles/create');
        $I->seeNumberOfElements('.is-invalid-input', 2);

        //Fill necessary stuff
        $I->fillField(self::CIRCLE_CREATE_TITLE_INPUT, self::CIRCLE_TITLE);
        $I->fillField(self::CIRCLE_CREATE_DESCRIPTION, self::CIRCLE_DESCRIPTION);
        $I->attachFile(self::CIRCLE_CREATE_COVER_INPUT, 'default-cover.png');
        $I->click(self::CIRCLE_CREATE_SUBMIT);
        $I->wait(3);

        $I->seeCurrentUrlMatches('/circles/');
        $I->see(self::CIRCLE_TITLE);
        $I->see(self::CIRCLE_DESCRIPTION);
    }

    public function testIfCircleWillBeSeenInCirclesPage(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->amOnPage('/circles');

        $I->see(self::CIRCLE_TITLE, 'h3');
        $I->see(self::CIRCLE_DESCRIPTION, self::CIRCLES_PAGE_DESCRIPTION_SELECTOR);
    }

    public function testIfOnlyCreatorCanAccessSettings(AcceptanceTester $I)
    {
        //Declare stuff
        $admin_id = 1;
        $another_user_id = 5;
        $admin_password = 'password!';
        $title = 'Some tilte for circle';
        $description = 'Bacon ipsum dolor sit amet nulla ham qui sint exercitation eiusmod';
        $I->createCircle($admin_id, $admin_password, $title, $description);

        // Check circle
        $I->see($title);
        $I->see($description);

        // Check if another user cannot update settings
        $another = $I->haveFriend('another');
        $another->does(function (AcceptanceTester $I) use ($another_user_id) {
            $I->loginAs($another_user_id);

            // Now got to created circle
            $I->amOnPage('/circles');
            $link = $I->grabAttributeFrom(self::CIRCLES_PAGE_URL_SELECTOR.':first-of-type', 'href');
            $I->click(self::CIRCLES_PAGE_URL_SELECTOR.':first-of-type');
            $url = $I->makeCodeceptLink($link);

            // Another user cannot see settings and access them
            $I->dontseeElement(self::CIRCLE_MENU_LINK_SELECTOR);
            $I->amOnPage($url.'/settings');
            $I->wait(2);
            $I->seeCurrentUrlEquals($url);
        });

        // Go to circle settings
        $I->seeElement(self::CIRCLE_MENU_LINK_SELECTOR);
        $I->click(self::CIRCLE_MENU_LINK_SELECTOR);
        $I->seeElement(self::CIRCLE_SETTINGS_FORM);
        $I->fillField(self::CIRCLE_SETTINGS_TITLE_INPUT, $title.' updated');
        $I->scrollTo(self::CIRCLE_SETTINGS_SUBMIT);
        $I->click(self::CIRCLE_SETTINGS_SUBMIT);
        $I->wait(2);
        $I->see($title.' updated');
    }

    public function testIfSettingsCanBeUpdated(AcceptanceTester $I)
    {
        // Some stuff
        $updated_title = 'Updated title';
        $updated_description = 'Updated description';

        // Admin login and go to settings
        $I->adminLogin();
        $I->amOnPage('/circles');
        $link = $I->grabAttributeFrom(self::CIRCLES_PAGE_URL_SELECTOR.':first-of-type', 'href');
        $I->click(self::CIRCLES_PAGE_URL_SELECTOR.':first-of-type');
        $url = $I->makeCodeceptLink($link);
        $I->seeElement(self::CIRCLE_MENU_LINK_SELECTOR);
        $I->click(self::CIRCLE_MENU_LINK_SELECTOR);
        $I->seeCurrentUrlEquals($url.'/settings');

        $I->fillField(self::CIRCLE_SETTINGS_TITLE_INPUT, $updated_title);
        $I->fillField(self::CIRCLE_SETTINGS_DESCRIPTION, $updated_description);
        $I->click('input#circle_type-secret');
        $I->click(self::PRIVACY_LABEL_SETTINGS);
        $I->click('input#circle-settings__post-members');
        $I->scrollTo(self::CIRCLE_SETTINGS_SUBMIT);
        $I->click(self::CIRCLE_SETTINGS_SUBMIT);

        $I->see($updated_title);
        $I->see($updated_description);
        $I->seeElement('input#circle_type-secret:checked');
        $I->click(self::PRIVACY_LABEL_SETTINGS);
        $I->seeElement('input#circle-settings__post-members:checked');
    }
}
