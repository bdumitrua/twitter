<?php

namespace App\Modules\User\Resources;

use App\Http\Resources\ActionsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isAuthorizedUser = Auth::id() === $this->id;

        $lists = !empty((array)$this->lists)
            ? UsersListResource::collection($this->lists)->resolve()
            : [];

        $deviceTokens = !empty((array)$this->deviceTokens)
            ? $this->deviceTokens->toArray()
            : [];

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
            'created_at' => $this->created_at->toW3cString(),
            "subscribtionsCount" => $this->subscribtions_count ?? 0,
            "subscribersCount" => $this->subscribers_count ?? 0,
            "imSubscribed" => $this->imSubscribed ?? false,
            "imSubscribedOnNotifications" => $this->imSubscribedOnNotifications ?? false,
            "availableSections" => $availableSections,
            "lists" => $lists,
            "deviceTokens" => $deviceTokens,
            "actions" => $actions
        ];
    }

    private function prepareActions(bool $isAuthorizedUser): array
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

            $actions[] = [
                "SubscribeToUserNewTweets",
                "subscribeOnUserNotification",
                ["user" => $this->id]
            ];

            $actions[] = [
                "UnsubscribeToUserNewTweets",
                "unsubscribeFromUserNotification",
                ["user" => $this->id]
            ];
        }

        return (array) ActionsResource::collection($actions);
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
