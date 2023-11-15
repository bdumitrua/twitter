import { createBrowserRouter } from "react-router-dom";
import Home from "../pages/Home/Home";

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
		],
	},
]);

export default router;
