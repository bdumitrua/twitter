import { Tweet } from "@/types/tweet/tweet";
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
					return <TweetComponent key={reply.id} tweetData={reply} />;
				})
			) : (
				<p>Loading...</p>
			)}
			{/* <div className={styles["tweet__reply-branch-dots"]}>
				<img src={dots} alt="" />
			</div>
			<Link to="/tweet" className={styles["tweet__show-thread"]}>
				len more reply
			</Link> */}
		</div>
	);
};

export default TweetMapReplies;
