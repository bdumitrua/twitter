import { motion } from "framer-motion";
import React from "react";

import styles from "../../assets/styles/pages/CreateTweet/TickIcon.module.scss";

interface CircleProgressProps {
	charCount: number;
	maxCharCount: number;
}

const CircleProgress: React.FC<CircleProgressProps> = ({
	charCount,
	maxCharCount,
}) => {
	const baseRadius = 7.5;
	const fullRadius = 9.5;
	const isFull = charCount > maxCharCount;
	const radius = isFull ? fullRadius : baseRadius;
	const circumference = 2 * Math.PI * radius;

	const offset = isFull
		? 0
		: circumference - (charCount / maxCharCount) * circumference;
	const progressClass = isFull
		? `${styles["progress__full"]}`
		: `${styles["progress"]}`;

	const overage = charCount >= maxCharCount ? charCount - maxCharCount : 0;
	const overageText = overage > 0 ? `-${overage}` : "";

	return (
		<svg width="26" height="26" viewBox="0 0 26 26">
			<motion.circle
				className={styles["base-circle"]}
				initial={{ r: baseRadius }}
				animate={{ r: radius }}
				transition={{ duration: 0.5 }}
				cx="13"
				cy="13"
				fill="none"
				strokeWidth="1.5"
			/>
			<motion.circle
				className={progressClass}
				initial={{ r: baseRadius }}
				animate={{ r: radius }}
				transition={{ duration: 0.5 }}
				cx="13"
				cy="13"
				fill="none"
				strokeWidth="1.5"
				strokeDasharray={circumference}
				strokeDashoffset={offset}
				transform="rotate(-90 13 13)"
			/>
			{overage > 0 && (
				<text
					x="50%"
					y="50%"
					dy=".3em"
					textAnchor="middle"
					className={styles["progress__overage-text"]}
				>
					{overageText}
				</text>
			)}
		</svg>
	);
};

export default CircleProgress;
