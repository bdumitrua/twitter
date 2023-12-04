import React from "react";

import styles from "../../assets/styles/pages/CreateTweet/CreateTweetModal.module.scss";

import rectangle from "../../assets/images/Pages/CreateTweet/rectangle.svg";
import retweet from "../../assets/images/Pages/CreateTweet/retweet.svg";
import penIcon from "../../assets/images/Pages/CreateTweet/penIcon.svg";

const CreateModal = ({ onClose, showModal }) => {
	return (
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
					<button
						className={styles["modal__button"]}
						onClick={onClose}
					>
						<img
							className={styles["modal__all-users-icon"]}
							src={retweet}
							alt=""
						/>
						<span>All users</span>
					</button>
					<button
						className={styles["modal__button"]}
						onClick={onClose}
					>
						<img
							className={styles["modal__pen-icon"]}
							src={penIcon}
							alt=""
						/>
						<span>Group 1</span>
					</button>
					<button
						className={styles["modal__button"]}
						onClick={onClose}
					>
						<img
							className={styles["modal__pen-icon"]}
							src={penIcon}
							alt=""
						/>
						<span>Group 2</span>
					</button>
					<button
						className={styles["modal__button"]}
						onClick={onClose}
					>
						<img
							className={styles["modal__pen-icon"]}
							src={penIcon}
							alt=""
						/>
						<span>Group 3</span>
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

export default CreateModal;
