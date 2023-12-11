import FetchTweets from "@/components/FetchTweets/FetchTweets";
import styles from "../../assets/styles/pages/Feed.module.scss";

const Feed: React.FC = () => {
	const haveThread: boolean = true;

	return (
		<div className={styles["feed__wrapper"]}>
			{/* <Tweet haveThread={haveThread} />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet /> */}

			<FetchTweets path="/tweets/feed" queryKey={["feed"]} />
		</div>
	);
};

export default Feed;
