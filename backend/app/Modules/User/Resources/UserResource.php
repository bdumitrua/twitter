<?php

namespace App\Modules\User\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isAuthorizedUser = $this->whenLoaded('deviceTokens', function () {
            return true;
        }, false);


        $lists = $this->whenLoaded('lists', function () {
            return $this->lists;
        }, []);

        $listsSubscribtions = $this->whenLoaded('listsSubscribtions', function () {
            return $this->listsSubscribtions;
        }, []);

        $deviceTokens = $this->whenLoaded('deviceTokens', function () {
            return $this->deviceTokens;
        }, []);

        $actions = $this->prepareActions($isAuthorizedUser);
        $availableSections = $this->prepareAvailableSections($isAuthorizedUser);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'link' => $this->link,
            'email' => $this->email,
            'about' => $this->about,
            'bg_image' => $this->bg_image,
            'avatar' => $this->avatar,
            'status_text' => $this->status_text,
            'site_url' => $this->site_url,
            'address' => $this->address,
            'birth_date' => $this->birth_date,
            'created_at' => $this->created_at,
            "subscribtions_count" => $this->subscribtions_count,
            "subscribers_count" => $this->subscribers_count,
            'available_sections' => $availableSections,
            "lists" => $lists,
            "listsSubscribtions" => $listsSubscribtions,
            "device_tokens" => $deviceTokens,
            "actions" => $actions
        ];
    }

    private function prepareActions(bool $isAuthorizedUser): object
    {
        $actions = [
            [
                "GetUserTweets",
                "get_user_tweets",
                ["user" => $this->id]
            ],
            [
                "GetUserReplies",
                "get_user_replies",
                ["user" => $this->id]
            ],
            [
                "GetUserLikedTweets",
                "get_user_likes",
                ["user" => $this->id]
            ],
            [
                "GetUserTweetsWithMedia",
                "get_user_tweets_with_media",
                ["user" => $this->id]
            ],
            [
                "GetUserSubscribtions",
                "get_user_subscribtions",
                ["user" => $this->id]
            ],
            [
                "GetUserSubscribers",
                "get_user_subscribers",
                ["user" => $this->id]
            ],
        ];

        if ($isAuthorizedUser) {
            $actions[] = [
                "GetUserBookmarks",
                "get_authorized_user_bookmarks",
            ];

            $actions[] = [
                "UpdateProfileData",
                "update_user_data",
            ];
        } else {
            $actions[] = [
                "SubscribeOnUser",
                "subscribe_on_user",
                ["user" => $this->id]
            ];

            $actions[] = [
                "UnsubscribeFromUser",
                "unsubscribe_from_user",
                ["user" => $this->id]
            ];
        }

        return ActionsResource::collection($actions);
    }

    private function prepareAvailableSections(bool $isAuthorizedUser): array
    {
        $sections = ['Tweets', 'Tweets & replies', 'Media', 'Likes'];

        if ($isAuthorizedUser) {
            $sections[] = 'Bookmarks';
        }

        return $sections;
    }
}
