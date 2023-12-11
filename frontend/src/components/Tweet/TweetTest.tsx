import { TweetTypes } from "@/types/tweet/tweet";
import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";
import { parseHashtags } from "../../utils/functions/parseHashtags";
import ActionButtons from "./ActionButtons";
import Author from "./Author";
import TweetAuthorAvatar from "./TweetAuthorAvatar";
import TweetThread from "./TweetThread";

const tweetText =
	"UXR/UX: You can only bring one item to a remote island to assist your research of native use of tools and usability. What do you bring? #TellMeAboutYou";

interface TweetTestProps {
	tweetData: TweetTypes;
}

const TweetTest: React.FC<TweetTestProps> = ({ tweetData }) => {
	return (
		<div className={styles["wrapper"]}>
			<div className={styles["tweet"]}>
				{/* <TweetAdditional />  !ТОЛЬКО С tweet_type: repost*/}
				<div className={styles["tweet__wrapper"]}>
					<TweetAuthorAvatar
						authorAvatar={tweetData.author.avatar}
						authorName={tweetData.author.name}
						haveThread={tweetData.haveThread}
					/>
					<div className={styles["tweet__content"]}>
						<Author
							author={tweetData.author}
							createdAt={tweetData.content.created_at}
						/>
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
				<TweetThread />
			</div>
		</div>
	);
};

export default TweetTest;
