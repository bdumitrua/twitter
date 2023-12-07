import styles from "../../assets/styles/pages/TweetPage/RetweetModal.module.scss";

import rectangle from "../../assets/images/Tweet/rectangle.svg";
import retweet from "../../assets/images/Tweet/retweet.svg";
import retweetWithComment from "../../assets/images/Tweet/retweetWithComment.svg";

interface RetweetModalProps {
	onClose: () => void;
	showModal: boolean;
}

const RetweetModal: React.FC<RetweetModalProps> = ({ onClose, showModal }) => (
	<>
		{showModal && (
			<div className={styles["overlay"]} onClick={onClose}></div>
		)}
		<div
			className={`${styles["modal"]} ${
				showModal && styles["modal__open"]
			}`}
		>
			<div className={styles["modal__wrapper"]}>
				<img
					className={styles["modal__rectangle"]}
					src={rectangle}
					alt=""
				/>
				<button className={styles["modal__button"]} onClick={onClose}>
					<img
						className={styles["modal__retweet-icon"]}
						src={retweet}
						alt=""
					/>
					<span>Retweet</span>
				</button>
				<button className={styles["modal__button"]} onClick={onClose}>
					<img
						className={styles["modal__pen-icon"]}
						src={retweetWithComment}
						alt=""
					/>
					<span>Retweet with comment</span>
				</button>
				<button className={styles["modal__cancel"]} onClick={onClose}>
					Cancel
				</button>
			</div>
		</div>
	</>
);

export default RetweetModal;
