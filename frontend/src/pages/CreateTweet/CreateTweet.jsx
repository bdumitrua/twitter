import React from "react";

import styles from "../../assets/styles/pages/CreateTweet/CreateTweet.module.scss";
import CreateTweetBody from "./CreateTweetBody";

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
		</div>
	);
};

export default CreateTweet;
