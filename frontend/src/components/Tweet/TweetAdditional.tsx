import shadedLike from "../../assets/images/Tweet/shadedLike.svg";
import styles from "../../assets/styles/components/Tweet/TweetAdditional.module.scss";

const TweetAdditional: React.FC = () => {
	return (
		<div className={styles["tweet__additional"]}>
			<div className={styles["tweet__type"]}>
				<img
					className={styles["tweet__shaded-like"]}
					src={shadedLike}
					alt=""
				/>
			</div>
			<span>Kieron Dotson and Zack John liked</span>
		</div>
	);
};

export default TweetAdditional;
