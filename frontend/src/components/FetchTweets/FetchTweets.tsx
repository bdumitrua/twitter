import { Tweet } from "@/types/tweet/tweet";
import ReplyComponent from "../Tweet/ReplyComponent";
import TweetComponent from "../Tweet/TweetComponent";

interface FetchTweetsProps {
	tweets: Tweet[];
}

const FetchTweets: React.FC<FetchTweetsProps> = ({ tweets }) => {
	return (
		<div>
			{tweets ? (
				tweets.map((tweetData: Tweet) => {
					if (tweetData.type === "reply") {
						return (
							<ReplyComponent
								key={`reply-${tweetData.id}`}
								tweetData={tweetData}
							/>
						);
					} else {
						return (
							<TweetComponent
								key={`any-${tweetData.id}`}
								tweetData={tweetData}
							/>
						);
					}
				})
			) : (
				<div>Loading...</div>
			)}
		</div>
	);
};

export default FetchTweets;
