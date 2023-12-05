import React from "react";
import styles from "../../assets/styles/pages/CreateTweet/AddButton.module.scss";

const AddButton = ({ addButtonActive }) => {
	return (
		<svg
			width="20"
			height="20"
			viewBox="0 0 20 20"
			fill="none"
			xmlns="http://www.w3.org/2000/svg"
		>
			<circle
				cx="10"
				cy="10"
				r="10"
				fill={addButtonActive ? "#4c9eeb" : "#b9dcf7"}
				className={styles["circle"]}
			/>
			<path
				d="M10.75 6.25C10.75 5.83579 10.4142 5.5 10 5.5C9.58579 5.5 9.25 5.83579 9.25 6.25V9.25H6.25C5.83579 9.25 5.5 9.58579 5.5 10C5.5 10.4142 5.83579 10.75 6.25 10.75H9.25V13.75C9.25 14.1642 9.58579 14.5 10 14.5C10.4142 14.5 10.75 14.1642 10.75 13.75V10.75H13.75C14.1642 10.75 14.5 10.4142 14.5 10C14.5 9.58579 14.1642 9.25 13.75 9.25H10.75V6.25Z"
				fill="white"
			/>
		</svg>
	);
};

export default AddButton;
