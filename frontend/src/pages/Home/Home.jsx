import React from "react";
import styles from "../../assets/styles/pages/Home.module.scss";
import Tweet from "../../components/Tweet/Tweet";

const Home = (props) => {
	const haveThread = true;

	return (
		<div className={styles["home__wrapper"]}>
			<Tweet haveThread={haveThread} />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
		</div>
	);
};

export default Home;
