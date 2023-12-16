import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import ReactDOM from "react-dom/client";
import { Provider } from "react-redux";
import { RouterProvider } from "react-router-dom";
import "./assets/styles/global.scss";
import "./assets/styles/reset.css";
import store from "./redux/store";
import router from "./routes/router";

const queryClient = new QueryClient();

const rootElement = document.getElementById("root");
if (!rootElement) throw new Error("Failed to find the root element");
const root = ReactDOM.createRoot(rootElement);
root.render(
	<QueryClientProvider client={queryClient}>
		<Provider store={store}>
			<RouterProvider router={router} />
		</Provider>
	</QueryClientProvider>
);
