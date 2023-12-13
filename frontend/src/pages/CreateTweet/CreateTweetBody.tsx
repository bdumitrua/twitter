import { useRef, useState } from "react";
import { useDispatch } from "react-redux";

import {
	removeTweetBody,
	updateTweetBodyLength,
} from "../../redux/slices/createTweet.slice";

import UserAvatarPlug from "@/components/UserAvatar/UserAvatarPlug";
import { AppDispatch } from "@/redux/store";
import cancelTweetButton from "../../assets/images/Pages/CreateTweet/cancelTweetButton.svg";
import styles from "../../assets/styles/pages/CreateTweet/CreateTweetBody.module.scss";

interface CreateTweetBodyProps {
	placeholder?: string;
	showCloseButton?: boolean;
	id: number;
}

const CreateTweetBody: React.FC<CreateTweetBodyProps> = ({
	placeholder,
	showCloseButton,
	id,
}) => {
	const dispatch = useDispatch<AppDispatch>();
	const [showLine, setShowLine] = useState<boolean>(false);
	const textareaRef = useRef<HTMLTextAreaElement>(null);
	const minHeight: number = 60;

	const handleChange: () => void = () => {
		if (textareaRef.current) {
			const textarea: HTMLTextAreaElement = textareaRef.current;
			textarea.style.height = "auto";
			textarea.style.height = `${textarea.scrollHeight}px`;

			setShowLine(textarea.scrollHeight > minHeight);
			dispatch(
				updateTweetBodyLength({ id, charCount: textarea.value.length })
			);
		}
	};

	return (
		<div className={styles["body"]}>
			<div className={styles["body__input-container"]}>
				<div className={styles["body__avatar-group"]}>
					<div className={styles["body__avatar"]}>
						<UserAvatarPlug authorId={1} userName="Duma" />
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
