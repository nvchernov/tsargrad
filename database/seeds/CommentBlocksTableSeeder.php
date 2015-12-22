<?php
use App\Models\CommentBlock;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class CommentBlocksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CommentBlock::create();
        //Comment::create(['comment_block_id' => 1, 'text' => 'test', 'user_id' => 1]);
    }
}
