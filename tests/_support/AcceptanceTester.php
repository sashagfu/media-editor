<?php

use Codeception\Scenario;
use App\Models\Circle;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor
{
    const CIRCLE_CREATE_FORM = 'form.create-circle__form';
    const CIRCLE_CREATE_TITLE_INPUT = 'input.crate-circle__title';
    const CIRCLE_CREATE_DESCRIPTION = 'textarea.create-circle__description';
    const CIRCLE_CREATE_COVER_INPUT = 'input#cover';
    const CIRCLE_CREATE_SUBMIT = '.create-circle__submit';
    const CIRCLE_SETTINGS_FORM = 'form.circle-settings__form';
    const POST_CONTENT_SELECTOR = 'div.create-post__content.contenteditable';
    const CREATE_POST_VIDEO_OPEN_BTN = 'a.create-post__video-btn';
    const VIDEOS_MODAL_SELECTOR = '#videosModal';
    const MEDIA_TAB_ITEM_SELECTOR = 'div.media-tab__item';
    const MEDIA_ITEM_BUTTON_SELECTOR = 'a.media-tab__select-btn';
    const POST_SHARE_BUTTON_SELECTOR = 'button.post-create__button';
    const PROFILE_POST_URL_SELECTOR  = 'a.post-container__single-url';
    const USER_MENTION_SELECTOR  = 'a.user-mention';
    const CLOSE_BUTTON_SELECTOR = 'button.close-button';

    use _generated\AcceptanceTesterActions;

    /** @var \Illuminate\Foundation\Application $laravelApp */
    protected $laravelApp;

    /** @var \Illuminate\Database\Query\Builder $db */
    protected $db;

    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);

        /** @var \Illuminate\Foundation\Application $app */
        $app = require 'bootstrap/app.php';
        $app->loadEnvironmentFrom('.env');
        $app->instance('request', new \Illuminate\Http\Request);
        $app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

        $this->db = $app->make('\Illuminate\Database\Query\Builder');

        $this->laravelApp = $app;
    }

    public function refreshMigration()
    {
        $this->laravelApp->make('Illuminate\Contracts\Console\Kernel')
            ->call('migrate:refresh');
    }

    public function fromDB()
    {
        return $this->db;
    }

    public function adminLogin()
    {
        $admin = User::whereEmail('admin@yuustar.com')->firstOrFail();
        $I = $this;
        $I->amOnPage('/');
        $I->click('Login');
        $I->seeElement('form');
        $I->submitForm('form', ['email' => $admin->email, 'password' => 'password!']);
        $I->seeCurrentUrlEquals('/following');
    }

    public function loginAs($user_id, $password = null, $dont_expect_following = null)
    {
        $user = User::find($user_id);
        $password = $password ? $password : 'secret';
        $I = $this;
        $I->amOnPage('/');
        $I->click('Login');
        $I->seeElement('form');
        $I->submitForm('form', ['email' => $user->email, 'password' => $password]);
        if ($dont_expect_following) {
            $I->canSeeCurrentUrlEquals('/login');
        } else {
            $I->seeCurrentUrlEquals('/following');
        }

    }

    public function createPost($content, $wait = null)
    {
        $I = $this;
        $I->adminLogin();
        $I->amOnPage('/post/create');
        $wait = $wait ? $wait : 8;

        // Fill fields
        $I->fillField(self::POST_CONTENT_SELECTOR, $content);

        // Select video and create post
        $I->click(self::CREATE_POST_VIDEO_OPEN_BTN);
        $I->waitForElementVisible(self::VIDEOS_MODAL_SELECTOR, 5);
        $I->moveMouseOver(self::MEDIA_TAB_ITEM_SELECTOR.':first-of-type');
        $I->waitForElementVisible(self::MEDIA_ITEM_BUTTON_SELECTOR.':first-of-type');
        $I->click(self::MEDIA_ITEM_BUTTON_SELECTOR.':first-of-type');
        $I->click(self::CLOSE_BUTTON_SELECTOR);
        $I->scrollTo(self::POST_SHARE_BUTTON_SELECTOR);
        $I->click(self::POST_SHARE_BUTTON_SELECTOR);

        $I->wait($wait);

        $I->seeCurrentUrlEquals('/profile');
    }

    public function createCircle($creator_id, $creator_password = null, $title, $description)
    {
        $I = $this;
        $I->loginAs($creator_id, $creator_password);
        $I->amOnPage('/circles/create');
        $I->seeElement(self::CIRCLE_CREATE_FORM);

        //Fill necessary stuff
        $I->fillField(self::CIRCLE_CREATE_TITLE_INPUT, $title);
        $I->fillField(self::CIRCLE_CREATE_DESCRIPTION, $description);
        $I->attachFile(self::CIRCLE_CREATE_COVER_INPUT, 'default-cover.png');
        $I->click(self::CIRCLE_CREATE_SUBMIT);
        $I->wait(3);

        $I->seeCurrentUrlMatches('/circles/');
        $I->see($title);
        $I->see($description);
        $link = $I->grabFromCurrentUrl();
        $slug = str_replace('/circles/', '', $link);
        return $slug;
    }

    public function makeCodeceptLink($link)
    {
        return str_replace('http://127.0.0.1:8000', '', $link);
    }

    public function updateCircleSettings($circle_slug, $settings)
    {
        $I = $this;
        $I->amOnPage('/circles/' . $circle_slug . '/settings');
        $I->seeElement(self::CIRCLE_SETTINGS_FORM);

        $circle = Circle::whereSlug($circle_slug)->first();
        $title = $circle->title;
        $description = $circle->description;
        $type = $circle->type;
        $post_adding_privacy = $circle->post_adding_privacy;

        $title = isset($settings['title']) ? $settings['title'] : $title;
        $description = isset($settings['description']) ? $settings['description'] : $description;
        $type = isset($settings['type']) ? $settings['type'] : $type;

        $I->submitForm(self::CIRCLE_SETTINGS_FORM, [
            'title'               => $title,
            'description'         => $description,
            'type'                => $type,
        ]);
    }
}
