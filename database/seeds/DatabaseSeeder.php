<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(TransactionsTableSeeder::class);
        $this->call(ProjectTableSeeder::class);
        $this->call(CommentsTableSeeder::class);
        $this->call(AssetsTableSeeder::class);
//        $this->call(VideosTableSeeder::class);
//        $this->call(ImagesSeeder::class);
//        $this->call(PostsTableSeeder::class);
//        $this->call(CirclesSeeder::class);
//        $this->call(LikesTableSeeder::class);
//        $this->call(StarsTableSeeder::class);
//        $this->call(FlagTableSeeder::class);
//        $this->call(UserFollowingsTableSeeder::class);
//        $this->call(FlagReasonsSeeder::class);
//        $this->call(NotificationsSeeder::class);
//        $this->call(InvitesTableSeeder::class);
//        $this->call(ThreadsTableSeeder::class);
//        $this->call(MessagesTableSeeder::class);
    }
}
