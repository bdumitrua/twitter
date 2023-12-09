import styles from "../../assets/styles/pages/Feed.module.scss";
import Tweet from "../../components/Tweet/Tweet";

const Feed: React.FC = () => {
	const haveThread: boolean = true;

	return (
		<div className={styles["feed__wrapper"]}>
			<Tweet haveThread={haveThread} />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
		</div>
	);
};

export default Feed;
