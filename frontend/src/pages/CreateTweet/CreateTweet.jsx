import React from "react";

import styles from "../../assets/styles/pages/CreateTweet/CreateTweet.module.scss";
import CreateTweetBody from "./CreateTweetBody";
import TickIcon from "./TickIcon";

import groupIcon from "../../assets/images/CreateTweet/groupIcon.svg";
import imageIcon from "../../assets/images/CreateTweet/imageIcon.svg";
import gifIcon from "../../assets/images/CreateTweet/gifIcon.svg";
import statsIcon from "../../assets/images/CreateTweet/statsIcon.svg";
import locationIcon from "../../assets/images/CreateTweet/locationIcon.svg";

const CreateTweet = () => {
	const [charCount, setCharCount] = React.useState(0);
	const maxCharCount = 255;

	const handleCharCountChange = (count) => {
		setCharCount(count);
	};

	const firstCreateProps = {
		placeholder: "What's happening?",
		showCloseButton: false,
	};

	const additionalCreateProps = {
		placeholder: "Add another post",
		showCloseButton: true,
	};

	const createProps = {
		onCharCountChange: handleCharCountChange,
		maxCharCount: maxCharCount,
	};

	return (
		<div className={styles["create"]}>
			<div className={styles["create__bodies"]}>
				<CreateTweetBody {...firstCreateProps} {...createProps} />
				<CreateTweetBody {...additionalCreateProps} {...createProps} />
			</div>
			<div className={styles["create__bars"]}>
				<div className={styles["create__group-bar"]}>
					<button className={styles["create__group"]}>
						<img src={groupIcon} alt="Group icon" />
						<span>Everyone can see</span>
					</button>
				</div>
				<div className={styles["create__buttons-bar"]}>
					<div className={styles["create__media-buttons"]}>
						<button className={styles["create__image-button"]}>
							<img src={imageIcon} alt="Image icon" />
						</button>
						<button className={styles["create__gif-button"]}>
							<img src={gifIcon} alt="Gif icon" />
						</button>
						<button className={styles["create__stats-button"]}>
							<img src={statsIcon} alt="Stats icon" />
						</button>
						<button className={styles["create__location-button"]}>
							<img src={locationIcon} alt="Location icon" />
						</button>
					</div>
					<div className={styles["create__tick-and-add"]}>
						<TickIcon
							charCount={charCount}
							maxCharCount={maxCharCount}
						/>
						<button
							className={styles["create__add-button"]}
						></button>
					</div>
				</div>
			</div>
		</div>
	);
};

export default CreateTweet;
