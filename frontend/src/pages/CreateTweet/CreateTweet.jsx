import React, { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";

import {
	addTweetBody,
	selectTweetBodies,
	selectNextId,
} from "../../redux/slices/createTweet.slice";

import styles from "../../assets/styles/pages/CreateTweet/CreateTweet.module.scss";
import CreateTweetBody from "./CreateTweetBody";
import TickIcon from "./TickIcon";
import AddButton from "./AddButton";

import groupIcon from "../../assets/images/Pages/CreateTweet/groupIcon.svg";
import imageIcon from "../../assets/images/Pages/CreateTweet/imageIcon.svg";
import gifIcon from "../../assets/images/Pages/CreateTweet/gifIcon.svg";
import statsIcon from "../../assets/images/Pages/CreateTweet/statsIcon.svg";
import locationIcon from "../../assets/images/Pages/CreateTweet/locationIcon.svg";

const CreateTweet = () => {
	const dispatch = useDispatch();
	const [addButtonActive, setAddButtonActive] = React.useState(false);
	const maxCharCount = 255;
	const tweetBodies = useSelector(selectTweetBodies);
	const currentId = useSelector((state) => state.createTweet.currentId);
	const charCount = useSelector((state) => {
		const tweetBody = state.createTweet.tweetBodies.find(
			(body) => body.id === currentId
		);
		return tweetBody ? tweetBody.charCount : 0;
	});

	useEffect(() => {
		setAddButtonActive(
			tweetBodies.every(
				(body) => body.charCount > 0 && body.charCount <= 255
			)
		);
	}, [tweetBodies]);

	const firstCreateProps = {
		placeholder: "What's happening?",
		showCloseButton: false,
	};

	const additionalCreateProps = {
		placeholder: "Add another post",
		showCloseButton: true,
	};

	useEffect(() => {
		dispatch(addTweetBody({ ...firstCreateProps }));
	}, []);

	const addBody = () => {
		if (addButtonActive) {
			dispatch(addTweetBody({ ...additionalCreateProps }));
		}
	};

	return (
		<div className={styles["create"]}>
			<div className={styles["create__bodies"]}>
				{tweetBodies.map((props, index) => (
					<CreateTweetBody key={index} id={props.id} {...props} />
				))}
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
						<div className={styles["create__tick"]}>
							<TickIcon
								charCount={charCount}
								maxCharCount={maxCharCount}
							/>
						</div>
						<div className={styles["create__separator"]} />
						<button
							className={styles["create__add-button"]}
							onClick={addBody}
						>
							<AddButton addButtonActive={addButtonActive} />
						</button>
					</div>
				</div>
			</div>
		</div>
	);
};

export default CreateTweet;
