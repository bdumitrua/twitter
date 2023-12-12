import { Tweet } from "@/types/tweet/tweet";
import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";
import { parseHashtags } from "../../utils/functions/parseHashtags";
import ActionButtons from "./ActionButtons";
import Author from "./Author";
import TweetAdditional from "./TweetAdditional";
import TweetAuthorAvatar from "./TweetAuthorAvatar";
import TweetReply from "./TweetReply";
import TweetReplyBranch from "./TweetReplyBranch";
import TweetThread from "./TweetThread";

interface TweetComponentProps {
	tweetData: Tweet;
}

const TweetComponent: React.FC<TweetComponentProps> = ({ tweetData }) => {
	return (
		<div className={styles["wrapper"]}>
			<div className={styles["tweet"]}>
				{tweetData.type === "repost" && (
					<TweetAdditional type={"repost"} name={"Dima Boo"} />
				)}
				<div className={styles["tweet__wrapper"]}>
					<div className={styles["tweet__image"]}>
						<TweetAuthorAvatar
							authorAvatar={tweetData.author.avatar}
							authorName={tweetData.author.name}
						/>
						{(tweetData.type === "thread" ||
							tweetData.type === "reply") && (
							<div className={styles["tweet__line"]}></div>
						)}
					</div>
					<div className={styles["tweet__content"]}>
						<Author
							author={tweetData.author}
							created_at={tweetData.content.created_at}
						/>
						{tweetData.type === "reply" && (
							<TweetReply
								replyTo={tweetData.related.author.link}
							/>
						)}
						<div className={styles["tweet__tweet-body"]}>
							<span className={styles["tweet__text"]}>
								{parseHashtags(tweetData.content.text, "feed")}
							</span>
						</div>
						<ActionButtons
							counters={tweetData.counters}
							actions={tweetData.actions}
						/>
					</div>
				</div>
				{tweetData.type === "reply" && <TweetReplyBranch />}
				{tweetData.type === "thread" && (
					<TweetThread
						authorAvatar={tweetData.author.avatar}
						authorName={tweetData.author.name}
					/>
				)}
			</div>
		</div>
	);
};

export default TweetComponent;
