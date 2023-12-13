import comment from "@/assets/images/Tweet/comment.svg";
import makeRepost from "@/assets/images/Tweet/makeRepost.svg";
import paintedLike from "@/assets/images/Tweet/paintedLike.svg";
import paintedRepost from "@/assets/images/Tweet/paintedRetweet.svg";
import repost from "@/assets/images/Tweet/retweet.svg";
import unpaintedLike from "@/assets/images/Tweet/unpaintedLike.svg";
import styles from "@/assets/styles/components/Tweet/Tweet.module.scss";
import { TweetActions, TweetCounters } from "@/types/tweet/tweet";
import axiosInstance from "@/utils/axios/instance";
import { useState } from "react";

interface ActionButtonsProps {
	counters: TweetCounters;
	actions: TweetActions;
	setShowModal: (value: boolean) => void;
	isReposted: boolean;
}

const ActionButtons: React.FC<ActionButtonsProps> = ({
	counters,
	actions,
	setShowModal,
	isReposted,
}) => {
	//TODO: Actions

	const [isLiked, setIsLiked] = useState(false);

	const handleLike = async () => {
		setIsLiked(!isLiked);
		const action = isLiked ? actions.DislikeTweet : actions.LikeTweet;
		try {
			await axiosInstance({ method: action.method, url: action.url });
		} catch (error) {
			setIsLiked(!isLiked);
			console.error(error);
		}
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
				className={styles["tweet__counter"]}
				onClick={() => setShowModal(true)}
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
