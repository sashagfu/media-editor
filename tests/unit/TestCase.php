<?php

use App\Models\Comment;
use App\Models\Flag;
use App\Models\Like;
use App\Models\Post;
use App\Models\Star;
use App\Models\User;
use App\Models\Video;
use App\Models\FlagReason;
use App\Models\UserFollowing;
use App\Models\SearchResult;
use App\Models\Circle;
use App\Helpers\DBHelper;

//use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

/**
 * @codingStandardsIgnoreStart
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * @var Faker\Generator
     */
    protected $faker;

    /**
     * @var RemoteWebDriver
     */
    protected $webDriver;

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Set Up a test environment
     */
    public function setUp()
    {
        parent::setUp();
        $this->prepareForTests();
    }

    public function tearDown()
    {
        //$this->webDriver->close();
    }

    /**
     * @param array $attributes
     *
     * @return User
     */
    protected function createUser(array $attributes = [])
    {
        return factory(User::class)->create($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return User
     */
    protected function makeUser(array $attributes = [])
    {
        return factory(User::class)->make($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return User
     */
    protected function login_as_admin(array $attributes = [])
    {
        $user = $this->createUser($attributes);
        $this->be($user);

        return $user;
    }

    /**
     * @param array $attributes
     *
     * @return Video
     */
    protected function createVideo(array $attributes = [])
    {
        $this->createUser();
        return factory(Video::class)->create($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return Video
     */
    protected function makeVideo(array $attributes = [])
    {
        return factory(Video::class)->make($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return Post
     */
    protected function createPost(array $attributes = [])
    {
        $this->createVideo();
        return factory(Post::class)->create($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return Post
     */
    protected function makePost(array $attributes = [])
    {
        return factory(Post::class)->make($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return Comment
     */
    public function createComment(array $attributes = [])
    {
        $this->createPost();
        $this->createUser();

        return factory(Comment::class)->create($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return Comment
     */
    public function makeComment(array $attributes = [])
    {
        return factory(Comment::class)->make($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return Like
     */
    public function createLike(array $attributes = [])
    {
        $this->createUser();
        $this->createPost();
        $this->createComment();

        return factory(Like::class)->create($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return Star
     */
    public function createStar(array $attributes = [])
    {
        $this->createUser();
        $media = $this->createVideo([
            'is_performance' => true
        ]);
        $post = $this->createPost();
        $video_type = DBHelper::getMapByModel(Video::class);
        $post->videos()->attach($media, ['media_type' => $video_type]);
        $post->save();

        return factory(Star::class)->create($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return Flag
     */
    public function createFlag(array $attributes = [])
    {
        $this->createPost();
        $this->createUser();

        return factory(Flag::class)->create($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return FlagReason
     */
    public function createFlagReason(array $attributes = [])
    {
        return factory(FlagReason::class)->create($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return UserFollowing
     */
    public function createUserFollowing(array $attributes = [])
    {
        $this->createUser();
        $this->createUser();
        return factory(UserFollowing::class)->create($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return SearchResult
     */
    public function createSearchResult(array $attributes = [])
    {
        return factory(SearchResult::class)->create($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return Circle
     */
    public function createCircle(array $attributes = [])
    {
        $this->createUser();
        return factory(Circle::class)->create($attributes);
    }

    /**
     * @param array $attributes
     *
     * @return Flag
     */
    public function makeFlag(array $attributes = [])
    {
        return factory(Flag::class)->make($attributes);
    }

    private function prepareForTests()
    {
        $this->faker = \Faker\Factory::create();
        //$capabilities = array(DesiredCapabilities::firefox());
        //$this->webDriver = RemoteWebDriver::create('http://testhost:4444/wd/hub/', $capabilities);
        Artisan::call('migrate:refresh');
    }
}

// @codingStandardsIgnoreEnd