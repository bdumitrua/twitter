import RetweetModal from "@/pages/TweetPage/RetweetModal";
import { Tweet } from "@/types/tweet/tweet";
import { useState } from "react";
import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";
import { parseHashtags } from "../../utils/functions/parseHashtags";
import ActionButtons from "./ActionButtons";
import Author from "./Author";
import TweetAuthorAvatar from "./TweetAuthorAvatar";
import TweetComponent from "./TweetComponent";

interface TweetComponentProps {
	tweetData: Tweet;
}

const ReplyComponent: React.FC<TweetComponentProps> = ({ tweetData }) => {
	const [showRepostModal, setShowRepostModal] = useState<boolean>(false);
	const [isReposted, setIsReposted] = useState<boolean>(false);

	return (
		<>
			<div className={styles["wrapper"]}>
				<div className={styles["tweet"]}>
					<div className={styles["tweet__wrapper"]}>
						<div className={styles["tweet__image"]}>
							<TweetAuthorAvatar
								authorId={tweetData.related.author.id}
								authorName={tweetData.related.author.name}
								authorAvatar={tweetData.related.author.avatar}
							/>
							{tweetData.type === "reply" && (
								<div className={styles["tweet__line"]}></div>
							)}
						</div>
						<div className={styles["tweet__content"]}>
							<Author
								author={tweetData.related.author}
								created_at={
									tweetData.related.content.created_at
								}
							/>

							<div className={styles["tweet__tweet-body"]}>
								<span className={styles["tweet__text"]}>
									{parseHashtags(
										tweetData.content.text,
										"feed"
									)}
								</span>
							</div>
							<ActionButtons
								counters={tweetData.related.counters}
								actions={tweetData.related.actions}
								setShowRepostModal={setShowRepostModal}
								isReposted={isReposted}
							/>
						</div>
					</div>
					{tweetData.type === "reply" && (
						<TweetComponent tweetData={tweetData} />
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
		</>
	);
};

export default ReplyComponent;
