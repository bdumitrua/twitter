import ReactDOM from "react-dom/client";
import { Provider } from "react-redux";
import { RouterProvider } from "react-router-dom";
import App from "./App.tsx";
import "./assets/styles/global.scss";
import "./assets/styles/reset.css";
import store from "./redux/store.ts";
import router from "./routes/router.tsx";

const root = ReactDOM.createRoot(document.getElementById("root"));
root.render(
	<Provider store={store}>
		<RouterProvider router={router}>
			<App />
		</RouterProvider>
	</Provider>
);
