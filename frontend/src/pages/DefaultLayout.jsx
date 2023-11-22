import React from "react";
import { Outlet } from "react-router-dom";
import Footer from "../components/Footer/Footer";
import Header from "../components/Header/Header";
import Tweet from "../components/Tweet/Tweet";

const DefaultLayout = (props) => {
	const haveUnwatched = true;

	return (
		<div>
			<Header haveUnwatched={haveUnwatched} />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
			<Outlet />
			<Footer />
		</div>
	);
};

export default DefaultLayout;
