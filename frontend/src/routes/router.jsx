import { createBrowserRouter, Navigate } from "react-router-dom";
import DefaultLayout from "../pages/DefaultLayout";
import Feed from "../pages/Feed/Feed";
import Profile from "../pages/Profile/Profile";
import LikesTab from "../pages/Profile/Tabs/LikesTab";
import MediaTab from "../pages/Profile/Tabs/MediaTab";
import TweetsAndRepliesTab from "../pages/Profile/Tabs/TweetsAndRepliesTab";
import TweetsTab from "../pages/Profile/Tabs/TweetsTab";
import TweetPage from "../pages/TweetPage/TweetPage";
import CreateTweet from "../pages/CreateTweet/CreateTweet";

const router = createBrowserRouter([
	{
		path: "/",
		element: <DefaultLayout />,
		children: [
			{
				path: "/",
				element: <Navigate to="/feed" />,
			},
			{
				path: "/feed",
				element: <Feed />,
			},
			{
				path: "/tweet",
				element: <TweetPage />,
			},
			{
				path: "/profile",
				element: <Profile />,
				children: [
					{
						path: "/profile",
						element: <Navigate to="tweets" />,
					},
					{
						path: "tweets",
						element: <TweetsTab />,
					},
					{
						path: "tweets-with-replies",
						element: <TweetsAndRepliesTab />,
					},
					{
						path: "media",
						element: <MediaTab />,
					},
					{
						path: "likes",
						element: <LikesTab />,
					},
				],
			},
			{
				path: "/create",
				element: <CreateTweet />,
			},
		],
	},
]);

export default router;
