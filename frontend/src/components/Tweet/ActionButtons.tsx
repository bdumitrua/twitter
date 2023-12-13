import comment from "@/assets/images/Tweet/comment.svg";
import makeRepost from "@/assets/images/Tweet/makeRepost.svg";
import paintedLike from "@/assets/images/Tweet/paintedLike.svg";
import paintedRepost from "@/assets/images/Tweet/paintedRetweet.svg";
import repost from "@/assets/images/Tweet/retweet.svg";
import unpaintedLike from "@/assets/images/Tweet/unpaintedLike.svg";
import styles from "@/assets/styles/components/Tweet/Tweet.module.scss";
import { TweetActions, TweetCounters } from "@/types/tweet/tweet";
import { handleAction } from "@/utils/functions/handleAction";
import { useState } from "react";

interface ActionButtonsProps {
	counters: TweetCounters;
	actions: TweetActions;
	setShowRepostModal: (value: boolean) => void;
	isReposted: boolean;
}

const ActionButtons: React.FC<ActionButtonsProps> = ({
	counters,
	actions,
	setShowRepostModal,
	isReposted,
}) => {
	//TODO: Actions
	const [isLiked, setIsLiked] = useState(false);
	const [isBookmarked, setIsBookmarked] = useState(false);

	const handleLike = async () => {
		handleAction(isLiked, setIsLiked, {
			checkAction: actions.LikeTweet,
			uncheckAction: actions.DislikeTweet,
		});
	};

	const handleBookmark = async () => {
		handleAction(isBookmarked, setIsBookmarked, {
			checkAction: actions.BookmarkTweet,
			uncheckAction: actions.UnbookmarkTweet,
		});
	};

	return (
		<div className={styles["tweet__counters"]}>
			<button className={styles["tweet__counter"]}>
				<img
					className={styles["tweet__counter-logo"]}
					src={comment}
					alt=""
				/>
				{counters.replies.count}
			</button>
			<button
				className={
					isReposted
						? styles["tweet__counter-active"]
						: styles["tweet__counter"]
				}
				onClick={() => setShowRepostModal(true)}
			>
				<img
					className={styles["tweet__counter-logo"]}
					src={isReposted ? paintedRepost : repost}
					alt=""
				/>
				{counters.reposts.count +
					counters.quotes.count +
					(isReposted ? 1 : 0)}
			</button>
			<button className={styles["tweet__counter"]} onClick={handleLike}>
				<img
					className={styles["tweet__counter-logo"]}
					src={isLiked ? paintedLike : unpaintedLike}
					alt=""
				/>
				{counters.likes.count + (isLiked ? 1 : 0)}
			</button>
			<button className={styles["tweet__counter"]}>
				<img
					className={styles["tweet__conter-logo"]}
					src={makeRepost}
					alt=""
				/>
			</button>
		</div>
	);
};

export default ActionButtons;
