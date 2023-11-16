import { createBrowserRouter } from "react-router-dom";
import App from "../App";
import TweetPage from "../pages/TweetPage/TweetPage";

const router = createBrowserRouter([
  {
    path: "/",
    element: <App />,
  },
  {
    path: "/tweet",
    element: <TweetPage />,
  },
]);

export default router;
