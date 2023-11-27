import React from "react";

import styles from "../../assets/styles/pages/CreateTweet/CreateTweet.module.scss";
import UserAvatar from "../../components/UserAvatar/UserAvatar";
import userPhoto from "../../assets/images/Tweet/pictureExample.jpg";

const CreateTweet = () => {
	const textareaRef = React.useRef(null);

	const handleChange = () => {
		const textarea = textareaRef.current;
		textarea.style.height = "auto";
		textarea.style.height = `${textarea.scrollHeight}px`;
	};

	return (
		<div className={styles["create"]}>
			<div className={styles["create__body"]}>
				<div className={styles["create__input-container"]}>
					<div className={styles["create__avatar"]}>
						<UserAvatar userPhoto={userPhoto} link="/profile" />
					</div>
					<textarea
						ref={textareaRef}
						onChange={handleChange}
						className={styles["create__input"]}
						type="text"
						placeholder="What's happening?"
					></textarea>
				</div>
			</div>
		</div>
	);
};

export default CreateTweet;
