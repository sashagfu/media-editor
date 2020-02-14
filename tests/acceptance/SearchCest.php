<?php

use App\Models\Post;
/**
 * Class SearchCest
 *
 * @SuppressWarnings(PHPMD)
 * @codingStandardsIgnoreStart
 */
class SearchCest
{
    const SEARCH_INPUT_SELECTOR = '.top-bar-search__input';
    const AUTOCOMPLETE_MENU_SELECTOR = '#ui-id-1';
    const AC_LI_SELECTOR = '.top-bar-search__li-autocomplete';
    const AC_ICON_SELECTOR = '.autocomplete-result__icon';
    const AC_QUOTE_SELECTOR = '.autocomplete-result__quote';
    const AC_RESULT_SELECTOR = '.autocomplete-result';
    const AC_POST_HEADER = '.autocomplete-result__post-header';
    const SEARCH_POSTS_RESULTS_SELECTOR = '.search-results__posts-results';
    const SEARCH_USERS_RESULTS_SELECTOR = '.search-results__users-results';
    const TRENDING_POSTS_RESULTS_SELECTOR = '.trending-results__posts-results';
    const TRENDING_POST_WRAPPER_SELECTOR = '.trending-results__post-wrapper';
    const TRENDING_USERS_RESULTS_SELECTOR = '.trending-results__users-results';
    const TRENDING_USER_WRAPPER_SELECTOR = '.trending-results__user-wrapper';
    const TRENDING_HASTAGS_RESULTS_SELECTOR = '.trending-results__hashtag-results';
    const TRENDING_HASTAG_RESULT_SELECTOR = '.trending-results__hashtag';

    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testIfUserCanSeeSearchForm(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->seeElement(self::SEARCH_INPUT_SELECTOR);
    }

    public function testIfUserCanSeeAutocompleteMenu(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->seeElement(self::SEARCH_INPUT_SELECTOR);
        $I->fillField(self::SEARCH_INPUT_SELECTOR, 'error ');
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR, 5);
    }

    public function testIfUserCanSearchInUsers(AcceptanceTester $I)
    {
        $I->wait(1);
        $I->adminLogin();
        $I->seeElement(self::SEARCH_INPUT_SELECTOR);
        $I->fillField(self::SEARCH_INPUT_SELECTOR, '@admin');
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR, 5);
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR.'>'.self::AC_LI_SELECTOR, 5);
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR.' '.self::AC_ICON_SELECTOR, 5);
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR.' '.self::AC_QUOTE_SELECTOR, 5);
        $I->waitForText('Yuustar Admin', 5);
    }

    public function testIfUserCanGoToUserFromSearch(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->seeElement(self::SEARCH_INPUT_SELECTOR);
        $I->fillField(self::SEARCH_INPUT_SELECTOR, '@admin');
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR, 5);
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR.'>'.self::AC_LI_SELECTOR, 5);
        $I->waitForText('Yuustar Admin', 5);
        $I->click(self::AUTOCOMPLETE_MENU_SELECTOR.' '.self::AC_RESULT_SELECTOR);

        $I->seeCurrentUrlMatches('/profile/');
        $I->see('Yuustar Admin');
        $I->see('@yuustar.admin');
    }

    public function testIfUserCanSearchInPosts(AcceptanceTester $I)
    {
        $I->createPost('Some mega unique content with #myhashtag', 15);
        $I->fillField(self::SEARCH_INPUT_SELECTOR, '#myhashtag');
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR, 5);
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR.'>'.self::AC_LI_SELECTOR, 5);
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR.' '.self::AC_POST_HEADER, 5);
    }

    public function testIfUserCanGoToPostFromSearch(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->seeElement(self::SEARCH_INPUT_SELECTOR);
        $I->fillField(self::SEARCH_INPUT_SELECTOR, '#myhashtag');
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR, 5);
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR.'>'.self::AC_LI_SELECTOR, 5);
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR.' '.self::AC_POST_HEADER, 5);
        $I->click(self::AUTOCOMPLETE_MENU_SELECTOR.' '.self::AC_RESULT_SELECTOR);

        $I->seeCurrentUrlMatches('/post/');
    }

    public function testIfUserWillSeeNoResultsInfo(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->seeElement(self::SEARCH_INPUT_SELECTOR);
        $I->fillField(self::SEARCH_INPUT_SELECTOR, 'sdfdsafadsfasdfasdfasdf');
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR, 5);
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR.'>'.self::AC_LI_SELECTOR, 5);
        $I->click(self::AUTOCOMPLETE_MENU_SELECTOR.' '.self::AC_RESULT_SELECTOR);

        $I->seeCurrentUrlEquals('/following');
    }

    public function testIfUserWillSeeGeneralResultsSearchPage(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->seeElement(self::SEARCH_INPUT_SELECTOR);
        $I->fillField(self::SEARCH_INPUT_SELECTOR, 'quam');
        $I->pressKey(self::SEARCH_INPUT_SELECTOR, WebDriverKeys::ENTER);

        $I->seeCurrentUrlEquals('/search/?q=quam');
        $I->see(trans('posts.page_title'), self::SEARCH_POSTS_RESULTS_SELECTOR);
        $I->see(trans('users.title'), self::SEARCH_USERS_RESULTS_SELECTOR);
        $I->dontSee(trans('search.no_res'));
    }

    public function testIfUserWillSeeErrorOnGeneralResultsSearchPage(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->seeElement(self::SEARCH_INPUT_SELECTOR);
        $I->fillField(self::SEARCH_INPUT_SELECTOR, 'erdsfsadfsdfsafsafsafdror');
        $I->pressKey(self::SEARCH_INPUT_SELECTOR, WebDriverKeys::ENTER);

        $I->seeCurrentUrlEquals('/search/?q=erdsfsadfsdfsafsafsafdror');
        $I->dontsee(trans('posts.page_title'), self::SEARCH_POSTS_RESULTS_SELECTOR);
        $I->dontsee(trans('users.title'), self::SEARCH_USERS_RESULTS_SELECTOR);
        $I->see(trans('search.no_res'));
    }

    public function testIfUserWillSeeTrendingResultsOnSearchPage(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->seeElement(self::SEARCH_INPUT_SELECTOR);
        $I->fillField(self::SEARCH_INPUT_SELECTOR, 'error');
        $I->pressKey(self::SEARCH_INPUT_SELECTOR, WebDriverKeys::ENTER);

        $I->seeCurrentUrlEquals('/search/?q=error');
        $I->see(trans('posts.page_title'), self::SEARCH_POSTS_RESULTS_SELECTOR);
        $I->see(trans('users.title'), self::SEARCH_USERS_RESULTS_SELECTOR);
        $I->see(trans('search.trending_posts'), self::TRENDING_POSTS_RESULTS_SELECTOR);
        $I->seeNumberOfElements(self::TRENDING_POST_WRAPPER_SELECTOR, 5);
        $I->see(trans('search.trending_users'), self::TRENDING_USERS_RESULTS_SELECTOR);
        $I->seeNumberOfElements(self::TRENDING_USER_WRAPPER_SELECTOR, 5);
        $I->dontSee(trans('search.no_res'));
    }

    public function testIfUserWillSeeTrendingResultsOnErrorSearchPage(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->seeElement(self::SEARCH_INPUT_SELECTOR);
        $I->fillField(self::SEARCH_INPUT_SELECTOR, 'erdsfsadfsdfsafsafsafdror');
        $I->pressKey(self::SEARCH_INPUT_SELECTOR, WebDriverKeys::ENTER);

        $I->seeCurrentUrlEquals('/search/?q=erdsfsadfsdfsafsafsafdror');
        $I->dontsee(trans('posts.page_title'), self::SEARCH_POSTS_RESULTS_SELECTOR);
        $I->dontsee(trans('users.title'), self::SEARCH_USERS_RESULTS_SELECTOR);
        $I->see(trans('search.no_res'));
        $I->see(trans('search.trending_posts'), self::TRENDING_POSTS_RESULTS_SELECTOR);
        $I->seeNumberOfElements(self::TRENDING_POST_WRAPPER_SELECTOR, 5);
        $I->see(trans('search.trending_users'), self::TRENDING_USERS_RESULTS_SELECTOR);
        $I->seeNumberOfElements(self::TRENDING_USER_WRAPPER_SELECTOR, 5);
    }

    public function testIfUserWillSeeTrendingHashtagsOnSearchPage(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->seeElement(self::SEARCH_INPUT_SELECTOR);
        $I->fillField(self::SEARCH_INPUT_SELECTOR, '#quam');
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR, 5);
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR.'>'.self::AC_LI_SELECTOR, 5);
        $I->click(self::AUTOCOMPLETE_MENU_SELECTOR.' '.self::AC_RESULT_SELECTOR);
        $I->seeCurrentUrlMatches('/post/');

        $I->fillField(self::SEARCH_INPUT_SELECTOR, 'error');
        $I->pressKey(self::SEARCH_INPUT_SELECTOR, WebDriverKeys::ENTER);
        $I->seeCurrentUrlEquals('/search/?q=error');

        $I->see(trans('posts.page_title'), self::SEARCH_POSTS_RESULTS_SELECTOR);
        $I->see(trans('users.title'), self::SEARCH_USERS_RESULTS_SELECTOR);
        $I->see(trans('search.trending_hashtags'), self::TRENDING_HASTAGS_RESULTS_SELECTOR);
        $I->seeElementInDOM(self::TRENDING_HASTAG_RESULT_SELECTOR);

        $I->dontSee(trans('search.no_res'));
    }

    public function testIfUserWillSeeTrendingHashtagsOnErrorSearchPage(AcceptanceTester $I)
    {
        $I->adminLogin();
        $I->seeElement(self::SEARCH_INPUT_SELECTOR);
        $I->fillField(self::SEARCH_INPUT_SELECTOR, '#error');
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR, 5);
        $I->waitForElementVisible(self::AUTOCOMPLETE_MENU_SELECTOR.'>'.self::AC_LI_SELECTOR, 5);
        $I->click(self::AUTOCOMPLETE_MENU_SELECTOR.' '.self::AC_RESULT_SELECTOR);
        $I->seeCurrentUrlMatches('/post/');

        $I->fillField(self::SEARCH_INPUT_SELECTOR, 'erdsfsadfsdfsafsafsafdror');
        $I->pressKey(self::SEARCH_INPUT_SELECTOR, WebDriverKeys::ENTER);
        $I->seeCurrentUrlEquals('/search/?q=erdsfsadfsdfsafsafsafdror');

        $I->dontSee(trans('posts.page_title'), self::SEARCH_POSTS_RESULTS_SELECTOR);
        $I->dontSee(trans('users.title'), self::SEARCH_USERS_RESULTS_SELECTOR);

        $I->see(trans('search.trending_hashtags'), self::TRENDING_HASTAGS_RESULTS_SELECTOR);
        $I->seeElementInDOM(self::TRENDING_HASTAG_RESULT_SELECTOR);
        $I->see(trans('search.no_res'));
    }
}
