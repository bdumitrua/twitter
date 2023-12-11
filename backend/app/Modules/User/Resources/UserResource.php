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
            'bgImage' => $this->bg_image,
            'avatar' => $this->avatar,
            'statusText' => $this->status_text,
            'siteUrl' => $this->site_url,
            'address' => $this->address,
            'birthDate' => $this->birth_date,
            'created_at' => $this->created_at,
            "subscribtionsCount" => $this->subscribtions_count,
            "subscribersCount" => $this->subscribers_count,
            'availableSections' => $availableSections,
            "lists" => $lists,
            "listsSubscribtions" => $listsSubscribtions,
            "deviceTokens" => $deviceTokens,
            "actions" => $actions
        ];
    }

    private function prepareActions(bool $isAuthorizedUser): object
    {
        $actions = [
            [
                "GetUserTweets",
                "getUserTweets",
                ["user" => $this->id]
            ],
            [
                "GetUserReplies",
                "getUserReplies",
                ["user" => $this->id]
            ],
            [
                "GetUserLikedTweets",
                "getUserLikes",
                ["user" => $this->id]
            ],
            [
                "GetUserTweetsWithMedia",
                "getUserTweetsWithMedia",
                ["user" => $this->id]
            ],
            [
                "GetUserSubscribtions",
                "getUserSubscribtions",
                ["user" => $this->id]
            ],
            [
                "GetUserSubscribers",
                "getUserSubscribers",
                ["user" => $this->id]
            ],
        ];

        if ($isAuthorizedUser) {
            $actions[] = [
                "GetUserBookmarks",
                "getAuthorizedUserBookmarks",
            ];

            $actions[] = [
                "UpdateProfileData",
                "updateUserData",
            ];
        } else {
            $actions[] = [
                "SubscribeOnUser",
                "subscribeOnUser",
                ["user" => $this->id]
            ];

            $actions[] = [
                "UnsubscribeFromUser",
                "unsubscribeFromUser",
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
