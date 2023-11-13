import React from "react";
import styles from "../../assets/styles/pages/Home.module.scss";
import Footer from "../../components/Footer/Footer";
import Header from "../../components/Header/Header";
import Tweet from "../../components/Tweet/Tweet";

const Home = (props) => {
	const haveThread = true;
	const haveUnwatched = true;

	return (
		<div className={styles["home__wrapper"]}>
			<Header haveUnwatched={haveUnwatched} />
			<Tweet haveThread={haveThread} />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
			<Footer />
		</div>
	);
};

export default Home;
