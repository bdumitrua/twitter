import { Tweet } from "@/types/tweet/tweet";
import { fetchData } from "@/utils/functions/fetchData";
import { useQuery } from "@tanstack/react-query";
import TweetComponent from "../Tweet/TweetComponent";

interface FetchTweetsProps {
	queryKey: string[];
	path: string;
}

const FetchTweets: React.FC<FetchTweetsProps> = ({ queryKey, path }) => {
	const { data } = useQuery({
		queryKey: [queryKey],
		queryFn: () => fetchData(path),
		refetchOnWindowFocus: false,
		//enabled: loadMore, // * Запрос активируется, когда loadMore становится true
	});

	return (
		<div>
			{data ? (
				data.map((tweet: Tweet) => {
					return <TweetComponent key={tweet.id} tweetData={tweet} />;
				})
			) : (
				<div>Loading...</div>
			)}
		</div>
	);
};

export default FetchTweets;
