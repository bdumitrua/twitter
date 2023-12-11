import FetchTweets from "@/components/FetchTweets/FetchTweets";

const TweetsAndRepliesTab: React.FC = (currentUser) => {
	return (
		<FetchTweets
			queryKey={["tweets-and-replies"]}
			path="/tweets/feed"
			// path={`/tweets/user/${currentUser}/replies`}
		/>
	);
};

export default TweetsAndRepliesTab;
