import { createBrowserRouter } from "react-router-dom";
import App from "../App";
import TweetPage from "../pages/TweetPage/TweetPage";
import Profile from "../pages/Profile/Profile";

const router = createBrowserRouter([
  {
    path: "/",
    element: <App />,
  },
  {
    path: "/tweet",
    element: <TweetPage />,
  },
  {
    path: "/profile",
    element: <Profile />,
  }
]);

export default router;
