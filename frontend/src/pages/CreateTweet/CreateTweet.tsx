import { useEffect, useState } from "react";
import { useDispatch, useSelector } from "react-redux";

import {
	addTweetBody,
	selectTweetBodies,
} from "../../redux/slices/createTweet.slice";

import styles from "../../assets/styles/pages/CreateTweet/CreateTweet.module.scss";
import AddButton from "./AddButton";
import CreateTweetBody from "./CreateTweetBody";
import CreateTweetModal from "./CreateTweetModal";
import TickIcon from "./TickIcon";

import { AppDispatch, RootState } from "@/redux/store";
import gifIcon from "../../assets/images/Pages/CreateTweet/gifIcon.svg";
import groupIcon from "../../assets/images/Pages/CreateTweet/groupIcon.svg";
import imageIcon from "../../assets/images/Pages/CreateTweet/imageIcon.svg";
import locationIcon from "../../assets/images/Pages/CreateTweet/locationIcon.svg";
import statsIcon from "../../assets/images/Pages/CreateTweet/statsIcon.svg";

interface FirstCreateProps {
	placeholder: string;
	showCloseButton: boolean;
}

const CreateTweet = () => {
	const dispatch = useDispatch<AppDispatch>();
	const [addButtonActive, setAddButtonActive] = useState<boolean>(false);
	const maxCharCount: number = 255;
	const tweetBodies = useSelector(selectTweetBodies);
	const currentId: number = useSelector(
		(state: RootState) => state.createTweet.currentId
	);

	const charCount: number = useSelector((state: RootState) => {
		const tweetBody = state.createTweet.tweetBodies.find(
			(body) => body.id === currentId
		);
		return tweetBody ? tweetBody.charCount : 0;
	});

	const [showModal, setShowModal] = useState<boolean>(false);
	const group: string | null = useSelector(
		(state: RootState) => state.createTweet.group
	);

	useEffect(() => {
		setAddButtonActive(
			tweetBodies.every(
				(body) => body.charCount > 0 && body.charCount <= 255
			)
		);
	}, [tweetBodies]);

	const firstCreateProps: FirstCreateProps = {
		placeholder: "What's happening?",
		showCloseButton: false,
	};

	const additionalCreateProps: FirstCreateProps = {
		placeholder: "Add another post",
		showCloseButton: true,
	};

	useEffect(() => {
		dispatch(addTweetBody({ ...firstCreateProps }));
	}, []);

	const addBody: () => void = () => {
		if (addButtonActive) {
			dispatch(addTweetBody({ ...additionalCreateProps }));
		}
	};

	return (
		<>
			<div className={styles["create"]}>
				<div className={styles["create__bodies"]}>
					{tweetBodies.map((props) => (
						<CreateTweetBody key={props.id} {...props} />
					))}
				</div>
				<div className={styles["create__bars"]}>
					<div className={styles["create__group-bar"]}>
						<button
							className={styles["create__group"]}
							onClick={() => setShowModal(true)}
						>
							<img src={groupIcon} alt="Group icon" />
							<span>{group ? group : "Everyone can see"}</span>
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
							<button
								className={styles["create__location-button"]}
							>
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
			<CreateTweetModal
				onClose={() => setShowModal(false)}
				showModal={showModal}
			/>
		</>
	);
};

export default CreateTweet;
