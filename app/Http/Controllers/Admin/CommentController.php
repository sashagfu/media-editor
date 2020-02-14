<?php

namespace App\Http\Controllers\Admin;

use App\Models\Post;
use Illuminate\Http\Request;

/**
 * Class CommentController
 *
 * @package App\Http\Controllers\Admin
 */
class CommentController extends Controller
{
    use ManagesCRUD {
        index as ManagesCRUDIndex;
    }

    public function index(Request $request)
    {
        if ($request->post) {
            $request->merge(['q' => ['post_id' => $request->post]]);
        }

        return $this->ManagesCRUDIndex($request);
    }

    /**
     * @param string|array $query
     * @param string       $model
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function search($query, $model)
    {
        $post_id = isset($query['post_id']) ? $query['post_id'] : 0;

        /**
 * @var Post $post
*/
        $post = Post::findOrFail($post_id);

        return $post->comments();
    }

    protected function withIndexView(Request $request)
    {
        $post_id = isset($request->post) ? $request->post : 0;

        return [
            'post' => Post::find($post_id)
        ];
    }

    protected function withOrder($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
