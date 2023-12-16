import React from "react";
import { Link } from "react-router-dom";
import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";

interface TweetReplyProps {
	replyTo: string | undefined;
	userId: number | undefined;
}

const TweetReply: React.FC<TweetReplyProps> = ({ replyTo, userId }) => {
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
