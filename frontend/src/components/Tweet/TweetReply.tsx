import React from "react";
import { Link } from "react-router-dom";
import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";

const TweetReply: React.FC = () => {
	return (
		<span className={styles["tweet__reply"]}>
			Replying to{" "}
			<Link to="/" className={styles["tweet__reply-tag"]}>
				@xDDD
			</Link>
		</span>
	);
};

export default TweetReply;
