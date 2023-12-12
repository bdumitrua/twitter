import FetchTweets from "@/components/FetchTweets/FetchTweets";
import { fetchData } from "@/utils/functions/fetchData";
import { useQuery } from "@tanstack/react-query";

const TweetsTab: React.FC = () => {
	const { data } = useQuery({
		queryKey: ["tweets-tab"],
		queryFn: () => fetchData("/tweets/feed"),
		refetchOnWindowFocus: false,
		//enabled: loadMore, // * Запрос активируется, когда loadMore становится true
	});

	return (
		<>
			<FetchTweets tweets={data} />
		</>
	);
};

export default TweetsTab;
