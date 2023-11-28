import React from "react";

import styles from "../../assets/styles/pages/CreateTweet/CreateTweet.module.scss";
import CreateTweetBody from "./CreateTweetBody";

import groupIcon from "../../assets/images/CreateTweet/groupIcon.svg";
import imageIcon from "../../assets/images/CreateTweet/imageIcon.svg";
import gifIcon from "../../assets/images/CreateTweet/gifIcon.svg";
import statsIcon from "../../assets/images/CreateTweet/statsIcon.svg";
import locationIcon from "../../assets/images/CreateTweet/locationIcon.svg";

const CreateTweet = () => {
	return (
		<div className={styles["create"]}>
			<div className={styles["create__bodies"]}>
				<CreateTweetBody
					placeholder={"What's happening?"}
					showCloseButton={false}
				/>
				<CreateTweetBody
					placeholder={"What's happening?"}
					showCloseButton={true}
				/>
			</div>
			<div className={styles["create__bars"]}>
				<div className={styles["create__group-bar"]}>
					<div className={styles["create__group"]}>
						<img src={groupIcon} alt="Group icon" />
						<span>Everyone can see</span>
					</div>
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
					<div
						className={styles["create__tick-and-add-buttons"]}
					></div>
				</div>
			</div>
		</div>
	);
};

export default CreateTweet;
