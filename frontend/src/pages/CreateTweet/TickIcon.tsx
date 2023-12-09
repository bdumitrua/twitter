import { motion } from "framer-motion";

import styles from "../../assets/styles/pages/CreateTweet/TickIcon.module.scss";

interface CircleProgressProps {
	charCount: number;
	maxCharCount: number;
}

const CircleProgress: React.FC<CircleProgressProps> = ({
	charCount,
	maxCharCount,
}) => {
	const baseRadius: number = 7.5;
	const fullRadius: number = 9.5;
	const isFull: boolean = charCount > maxCharCount;
	const radius: number = isFull ? fullRadius : baseRadius;
	const circumference: number = 2 * Math.PI * radius;

	const offset: number = isFull
		? 0
		: circumference - (charCount / maxCharCount) * circumference;
	const progressClass: string = isFull
		? `${styles["progress__full"]}`
		: `${styles["progress"]}`;

	const overage: number =
		charCount >= maxCharCount ? charCount - maxCharCount : 0;
	const overageText: string = overage > 0 ? `-${overage}` : "";

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
