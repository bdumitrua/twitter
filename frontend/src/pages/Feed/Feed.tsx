import FetchTweets from "@/components/FetchTweets/FetchTweets";
import { fetchData } from "@/utils/functions/fetchData";
import { useQuery } from "@tanstack/react-query";
import styles from "../../assets/styles/pages/Feed.module.scss";

const Feed: React.FC = () => {
	const { data } = useQuery({
		queryKey: ["feed"],
		queryFn: () => fetchData("/api/tweets/feed"),
		refetchOnWindowFocus: false,
		//enabled: loadMore, // * Запрос активируется, когда loadMore становится true
	});

	return (
		<div className={styles["feed__wrapper"]}>
			<FetchTweets tweets={data} />
		</div>
	);
};

export default Feed;
