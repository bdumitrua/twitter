import styles from "../../assets/styles/pages/TweetPage/RetweetModal.module.scss";

import { TweetActions } from "@/types/tweet/tweet";
import { handleAction } from "@/utils/functions/handleAction";
import axios from "axios";
import rectangle from "../../assets/images/Tweet/rectangle.svg";
import retweet from "../../assets/images/Tweet/retweet.svg";
import retweetWithComment from "../../assets/images/Tweet/retweetWithComment.svg";

interface RetweetModalProps {
	onClose: () => void;
	showRepostModal: boolean;
	actions: TweetActions;
	isReposted: boolean;
	setIsReposted: (value: boolean) => void;
}

const RetweetModal: React.FC<RetweetModalProps> = ({
	onClose,
	showRepostModal,
	actions,
	isReposted,
	setIsReposted,
}) => {
	// TODO: Разобраться с логикой репостов

	const handleRepost = async () => {
		handleAction(isReposted, setIsReposted, {
			checkAction: actions.RepostTweet,
			uncheckAction: actions.UnrepostTweet,
		});
		onClose;
	};

	const handleQuote = async () => {
		try {
			await axios({
				method: actions.QuoteTweet.method,
				url: actions.QuoteTweet.url,
			});
			onClose;
		} catch (error) {
			console.error(error);
		}
	};

	return (
		<>
			{showRepostModal && (
				<div className={styles["overlay"]} onClick={onClose}></div>
			)}
			<div
				className={`${styles["modal"]} ${
					showRepostModal && styles["modal__open"]
				}`}
			>
				<div className={styles["modal__wrapper"]}>
					<img
						className={styles["modal__rectangle"]}
						src={rectangle}
						alt=""
					/>
					<button
						className={styles["modal__button"]}
						onClick={handleRepost}
					>
						<img
							className={styles["modal__retweet-icon"]}
							src={retweet}
							alt=""
						/>
						{isReposted ? (
							<span>Cancel retweet</span>
						) : (
							<span>Retweet</span>
						)}
					</button>
					<button
						className={styles["modal__button"]}
						onClick={handleQuote}
					>
						<img
							className={styles["modal__pen-icon"]}
							src={retweetWithComment}
							alt=""
						/>
						<span>Retweet with comment</span>
					</button>
					<button
						className={styles["modal__cancel"]}
						onClick={onClose}
					>
						Cancel
					</button>
				</div>
			</div>
		</>
	);
};

export default RetweetModal;
