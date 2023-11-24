import { createBrowserRouter, Navigate } from "react-router-dom";
import DefaultLayout from "../pages/DefaultLayout";
import Feed from "../pages/Feed/Feed";
import Profile from "../pages/Profile/Profile";
import TweetPage from "../pages/TweetPage/TweetPage";

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
			},
		],
	},
]);

export default router;
