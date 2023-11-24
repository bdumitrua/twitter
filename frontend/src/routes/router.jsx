import {
	createBrowserRouter,
	Navigate,
} from "react-router-dom";
import DefaultLayout from "../pages/DefaultLayout";
import Home from "../pages/Home/Home";
import Profile from "../pages/Profile/Profile";
import TweetPage from "../pages/TweetPage/TweetPage";

const router = createBrowserRouter([
	{
		path: "/",
		element: <DefaultLayout />,
		children: [
			{
				path: "/",
				element: <Navigate to="/home" />,
			},
			{
				path: "/home",
				element: <Home />,
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
