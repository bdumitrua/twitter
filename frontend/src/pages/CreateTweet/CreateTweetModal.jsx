import React from "react";
import { useSelector, useDispatch } from "react-redux";

import styles from "../../assets/styles/pages/CreateTweet/CreateTweetModal.module.scss";

import { changeGroup } from "../../redux/slices/createTweet.slice";

import rectangle from "../../assets/images/Pages/CreateTweet/rectangle.svg";
import retweet from "../../assets/images/Pages/CreateTweet/retweet.svg";
import penIcon from "../../assets/images/Pages/CreateTweet/penIcon.svg";

const CreateModal = ({ onClose, showModal }) => {
	const dispatch = useDispatch();
	const groupsList = useSelector((state) => state.createTweet.groupsList);

	const onGroupChange = (group) => {
		dispatch(changeGroup(group));
		onClose();
	};

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
						onClick={() => onGroupChange(null)}
					>
						<img
							className={styles["modal__all-users-icon"]}
							src={retweet}
							alt=""
						/>
						<span>All users</span>
					</button>
					{groupsList.map((group, index) => (
						<button
							key={index}
							className={styles["modal__button"]}
							onClick={() => onGroupChange(group)}
						>
							<img
								className={styles["modal__pen-icon"]}
								src={penIcon}
								alt=""
							/>
							<span>{group}</span>
						</button>
					))}
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
