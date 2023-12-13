import RetweetModal from "@/pages/TweetPage/RetweetModal";
import { Tweet } from "@/types/tweet/tweet";
import { useState } from "react";
import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";
import { parseHashtags } from "../../utils/functions/parseHashtags";
import ActionButtons from "./ActionButtons";
import Author from "./Author";
import TweetAdditional from "./TweetAdditional";
import TweetAuthorAvatar from "./TweetAuthorAvatar";
import TweetReply from "./TweetReply";
import TweetThread from "./TweetThread";

interface TweetComponentProps {
	tweetData: Tweet;
}

const TweetComponent: React.FC<TweetComponentProps> = ({ tweetData }) => {
	const [showRepostModal, setShowRepostModal] = useState<boolean>(false);
	const [isReposted, setIsReposted] = useState<boolean>(false);

	return (
		<div className={styles["wrapper"]}>
			<div className={styles["tweet"]}>
				{tweetData.type === "repost" && (
					<TweetAdditional
						type={tweetData.type}
						name={tweetData.author.name}
					/>
				)}
				<div className={styles["tweet__wrapper"]}>
					<div className={styles["tweet__image"]}>
						<TweetAuthorAvatar
							authorId={tweetData.author.id}
							authorName={tweetData.author.name}
							authorAvatar={tweetData.author.avatar}
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
								userId={tweetData.related.author.id}
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
							setShowRepostModal={setShowRepostModal}
							isReposted={isReposted}
						/>
					</div>
				</div>
				{/* {tweetData.type === "reply" && (
					<TweetMapReplies replies={tweetData.replies} />
				)} */}
				{tweetData.type === "thread" && (
					<TweetThread
						authorAvatar={tweetData.author.avatar}
						authorName={tweetData.author.name}
						authorId={tweetData.author.id}
					/>
				)}
			</div>
			<RetweetModal
				onClose={() => setShowRepostModal(false)}
				showRepostModal={showRepostModal}
				actions={tweetData.actions}
				isReposted={isReposted}
				setIsReposted={setIsReposted}
			/>
		</div>
	);
};

export default TweetComponent;
