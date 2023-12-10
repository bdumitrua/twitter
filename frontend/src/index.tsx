import ReactDOM from "react-dom/client";
import { Provider } from "react-redux";
import { RouterProvider } from "react-router-dom";
import "./assets/styles/global.scss";
import "./assets/styles/reset.css";
import store from "./redux/store";
import router from "./routes/router";

const rootElement = document.getElementById("root");
if (!rootElement) throw new Error("Failed to find the root element");
const root = ReactDOM.createRoot(rootElement);
root.render(
	<Provider store={store}>
		<RouterProvider router={router} />
	</Provider>
);
