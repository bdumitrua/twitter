import dots from "@/assets/images/Tweet/dots.svg";
import { Tweet } from "@/types/tweet/tweet";
import { Link } from "react-router-dom";
import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";
import TweetComponent from "./TweetComponent";

interface Replies {
	replies: Tweet[];
}

const TweetMapReplies: React.FC<Replies> = ({ replies }) => {
	return (
		<div className={styles["tweet__reply-branch"]}>
			{replies ? (
				replies.map((reply) => {
					return <TweetComponent tweetData={reply} />;
				})
			) : (
				<p>Loading...</p>
			)}
			<div className={styles["tweet__reply-branch-dots"]}>
				<img src={dots} alt="" />
			</div>
			<Link to="/tweet" className={styles["tweet__show-thread"]}>
				len more reply
			</Link>
		</div>
	);
};

export default TweetMapReplies;
