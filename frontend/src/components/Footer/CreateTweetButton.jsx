import React from "react";
import { Link } from "react-router-dom";

import createTweetButtonSVG from "../../assets/images/Footer/createTweetButton.svg";
import styles from "../../assets/styles/components/Footer/CreateTweetButton.module.scss";

const CreateTweetButton = () => {
	return (
		<Link to="/create" className={styles["create-tweet-button"]}>
			<img src={createTweetButtonSVG} alt="createTweetButton" />
		</Link>
	);
};

export default CreateTweetButton;
