import FetchTweets from "@/components/FetchTweets/FetchTweets";
import { fetchData } from "@/utils/functions/fetchData";
import { getSubstring } from "@/utils/functions/getSubstring";
import { useQuery } from "@tanstack/react-query";
import { useLocation } from "react-router-dom";

const TweetsTab: React.FC = () => {
	const location = useLocation();
	const userId = getSubstring(location.pathname, "/", 2);

	console.log(userId);

	const { data } = useQuery({
		queryKey: ["tweets-tab"],
		queryFn: () => fetchData(`/api/tweets/user/${userId}`),
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
