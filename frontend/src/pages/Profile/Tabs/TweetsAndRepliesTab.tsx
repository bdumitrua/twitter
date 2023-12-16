import FetchTweets from "@/components/FetchTweets/FetchTweets";
import { fetchData } from "@/utils/functions/fetchData";
import { useQuery } from "@tanstack/react-query";

const TweetsAndRepliesTab: React.FC = () => {
	const { data } = useQuery({
		queryKey: ["tweets-and-replies"],
		queryFn: () => fetchData("/api/tweets/feed"),
		refetchOnWindowFocus: false,
		//enabled: loadMore, // * Запрос активируется, когда loadMore становится true
	});

	return <FetchTweets tweets={data} />;
};

export default TweetsAndRepliesTab;
