import { Tweet } from "@/types/tweet/tweet";
import TweetComponent from "../Tweet/TweetComponent";

interface FetchTweetsProps {
	tweets: Tweet[];
}

const FetchTweets: React.FC<FetchTweetsProps> = ({ tweets }) => {
	return (
		<div>
			{tweets ? (
				tweets.map((tweet: Tweet) => {
					return <TweetComponent key={tweet.id} tweetData={tweet} />;
				})
			) : (
				<div>Loading...</div>
			)}
		</div>
	);
};

export default FetchTweets;
