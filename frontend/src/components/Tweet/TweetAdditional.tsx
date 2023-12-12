import shadedLike from "../../assets/images/Tweet/shadedLike.svg";
import styles from "../../assets/styles/components/Tweet/TweetAdditional.module.scss";

interface TweetAdditionalProps {
	type: string;
	name: string;
}

const TweetAdditional: React.FC<TweetAdditionalProps> = ({ type, name }) => {
	return (
		<div className={styles["tweet__additional"]}>
			<div className={styles["tweet__type"]}>
				<img
					className={styles["tweet__shaded-like"]}
					src={shadedLike}
					alt=""
				/>
			</div>
			<span>{name} retweeted</span>
		</div>
	);
};

export default TweetAdditional;
