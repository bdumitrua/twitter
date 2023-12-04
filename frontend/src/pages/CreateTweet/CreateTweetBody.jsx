import { useState, useRef } from "react";
import { useDispatch } from "react-redux";

import {
	updateTweetBodyLength,
	removeTweetBody,
} from "../../redux/slices/createTweet.slice";

import styles from "../../assets/styles/pages/CreateTweet/CreateTweetBody.module.scss";
import UserAvatar from "../../components/UserAvatar/UserAvatar";
import userPhoto from "../../assets/images/Tweet/pictureExample.jpg";
import cancelTweetButton from "../../assets/images/Pages/CreateTweet/cancelTweetButton.svg";

const CreateTweetBody = ({ placeholder, showCloseButton, id }) => {
	const dispatch = useDispatch();
	const [showLine, setShowLine] = useState(false);
	const textareaRef = useRef(null);
	const minHeight = 60;

	const handleChange = () => {
		const textarea = textareaRef.current;
		textarea.style.height = "auto";
		textarea.style.height = `${textarea.scrollHeight}px`;

		if (textarea.scrollHeight < minHeight) {
			setShowLine(false);
		} else {
			setShowLine(true);
		}
		dispatch(
			updateTweetBodyLength({ id, charCount: textarea.value.length })
		);
	};

	return (
		<div className={styles["body"]}>
			<div className={styles["body__input-container"]}>
				<div className={styles["body__avatar-group"]}>
					<div className={styles["body__avatar"]}>
						<UserAvatar userPhoto={userPhoto} link="/profile" />
					</div>
					{showLine && (
						<div className={styles["body__avatar-line"]} />
					)}
				</div>
				<textarea
					ref={textareaRef}
					onChange={handleChange}
					onFocus={handleChange}
					className={styles["body__input"]}
					type="text"
					placeholder={placeholder}
				></textarea>
				{showCloseButton && (
					<button
						className={styles["body__close-button"]}
						onClick={() => dispatch(removeTweetBody({ id }))}
					>
						<img src={cancelTweetButton} alt="Cancel tweet" />
					</button>
				)}
			</div>
		</div>
	);
};

export default CreateTweetBody;
