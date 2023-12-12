import React from "react";
import { Link } from "react-router-dom";
import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";

interface TweetReplyProps {
	replyTo: string;
}

const TweetReply: React.FC<TweetReplyProps> = ({ replyTo }) => {
	return (
		<span className={styles["tweet__reply"]}>
			Replying to{" "}
			<Link to="/" className={styles["tweet__reply-tag"]}>
				@{replyTo}
			</Link>
		</span>
	);
};

export default TweetReply;
