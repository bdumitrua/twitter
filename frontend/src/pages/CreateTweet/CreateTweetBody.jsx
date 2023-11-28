import React from "react";

import styles from "../../assets/styles/pages/CreateTweet/CreateTweetBody.module.scss";
import UserAvatar from "../../components/UserAvatar/UserAvatar";
import userPhoto from "../../assets/images/Tweet/pictureExample.jpg";
import cancelTweetButton from "../../assets/images/CreateTweet/cancelTweetButton.svg";

const CreateTweetBody = ({ placeholder, showCloseButton }) => {
	const [showLine, setShowLine] = React.useState(false);
	const textareaRef = React.useRef(null);
	const minHeight = 61;

	const handleChange = () => {
		const textarea = textareaRef.current;
		textarea.style.height = "auto";
		textarea.style.height = `${textarea.scrollHeight}px`;

		if (textarea.scrollHeight < minHeight) {
			setShowLine(false);
		} else {
			setShowLine(true);
		}
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
					className={styles["body__input"]}
					type="text"
					placeholder={placeholder}
				></textarea>
				<div>
					{showCloseButton && (
						<img
							className={styles["body__close-button"]}
							src={cancelTweetButton}
							alt="cancel tweet"
						/>
					)}
				</div>
			</div>
		</div>
	);
};

export default CreateTweetBody;
