 <?php

/**
 * Class FollowingPageCest
 *
 * @SuppressWarnings(PHPMD)
 * @codingStandardsIgnoreStart
 */
class FollowingPageCest
{
    const POSTS_PER_PAGE = 10;
    const POST_CONTAINER_SELECTOR = 'div.blog-post.post-container';
    const FOOTER_SELECTOR = 'footer';

    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function testFollowingPageAndAjaxLoadingIfNeeded(AcceptanceTester $I)
    {
        $admin = User::whereEmail('admin@yuustar.com')->firstOrFail();
        $posts_count = Post::followingPosts($admin)
                ->skipFlagged($admin)
                ->latest()
                ->count();
        $scroll_times = floor($posts_count/10);
        $I->adminLogin();

        if($posts_count < self::POSTS_PER_PAGE) {
            $I->canSeeNumberOfElementsInDOM(self::POST_CONTAINER_SELECTOR, $posts_count);
        } else {
            $I->canSeeNumberOfElementsInDOM(self::POST_CONTAINER_SELECTOR, self::POSTS_PER_PAGE);
            for($i = 1; $i <= $scroll_times; $i++){
                $I->scrollTo(self::FOOTER_SELECTOR);
                $I->wait(5);
                $i == $scroll_times ? $I->canSeeNumberOfElementsInDOM(self::POST_CONTAINER_SELECTOR,
                    $posts_count) : $I->canSeeNumberOfElementsInDOM(self::POST_CONTAINER_SELECTOR,
                    ($i * self::POSTS_PER_PAGE) + self::POSTS_PER_PAGE);
            }
        }
    }
}
