<?php

namespace App\Http\Controllers\Newsfeed;

use App\NewsFeedGroup;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NewsFeedGroupController extends Controller
{
    /**
     * Add a new group in DB
     *
     * @param Request $request
     * @return int
     */
    public function addGroup(Request $request):int
    {
        $model = new NewsFeedGroup();
        $model->group_name = $request->name;
        $model->group_tags = json_encode($request->hotels);
        $model->save();
        return $model->id;
    }

    /**
     * Edit news feed group
     *
     * @param Request $request
     * @return void
     */
    public function editGroup(Request $request):void
    {
        $model =NewsFeedGroup::find($request->id);
        $model->group_name = $request->name;
        $model->group_tags = json_encode($request->hotels);
        $model->save();

    }

    /**
     * Delete group
     *
     * @param Request $request
     * @return void
     */
    public function deleteGroup(Request $request):void
    {
        $model =NewsFeedGroup::find($request->id);
        $model->delete();
    }

}
