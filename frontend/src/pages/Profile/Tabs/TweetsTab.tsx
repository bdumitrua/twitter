import FetchTweets from "@/components/FetchTweets/FetchTweets";

const TweetsTab: React.FC = () => {
	return (
		<>
			<FetchTweets
				queryKey={["tweets-and-replies"]}
				path="/tweets/feed"
				// path={`/tweets/user/${currentUser}`}
			/>
		</>
	);
};

export default TweetsTab;
