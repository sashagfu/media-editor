<?php

use Illuminate\Database\Seeder;
use App\Models\Circle;
use App\Models\User;
use App\Helpers\DBHelper;

class CirclesSeeder extends Seeder
{
    protected $faker;

    public function __construct(Faker\Generator $faker)
    {
        $this->faker = $faker;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $max_circles = 30;
        $max_existing_members = 30;
        $max_posts = 30;

        $circle_count = rand(10, $max_circles);

        factory(Circle::class, $circle_count)
            ->create()
            ->each(function ($circle) use ($max_existing_members, $max_posts) {
                $existing_member_count = $this->faker->numberBetween(2, $max_existing_members);
                User::inRandomOrder()->limit($existing_member_count)->get()->each(function ($member) use ($circle) {
                    $this->attachMember($member, $circle);
                });
                $posts_count = rand(2, $max_posts);
                Post::inRandomOrder()->limit($posts_count)->get()->each(function ($post) use ($circle) {
                    $this->attachToFeed($post, $circle);
                });
            });
    }

    private function attachMember($member, $circle)
    {
        $status = [Circle::STATUS_ADMIN, Circle::STATUS_PENDING, Circle::STATUS_MEMBER];

        $circle->members()->attach($member, [
            'status'     => $this->faker->randomElement($status),
            'created_at' => $this->faker->dateTimeThisMonth(),
            'updated_at' => $this->faker->dateTimeThisMonth(),
        ]);
    }

    private function attachToFeed($post, $circle)
    {
        $type = DBHelper::getMapByModel(Circle::class);

        $circle->feed()->attach($post, [
            'feed_type' => $type
        ]);
    }
}
