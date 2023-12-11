import { TweetTypes } from "@/types/tweet/tweet";
import { fetchData } from "@/utils/functions/fetchData";
import { useQuery } from "@tanstack/react-query";
import TweetTest from "../Tweet/TweetTest";

interface FetchTweetsProps {
	queryKey: string[];
	path: string;
}

const FetchTweets: React.FC<FetchTweetsProps> = ({ queryKey, path }) => {
	console.log(path);
	const { data } = useQuery({
		queryKey: [queryKey],
		queryFn: () => fetchData(path),
		//enabled: loadMore, // * Запрос активируется, когда loadMore становится true
	});

	return (
		<div>
			{data ? (
				data.map((tweet: TweetTypes) => {
					return <TweetTest key={tweet.id} tweetData={tweet} />;
				})
			) : (
				<div>Loading...</div>
			)}
		</div>
	);
};

export default FetchTweets;
