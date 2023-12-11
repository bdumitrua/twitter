interface Counters {
	likes: { count: number };
	replies: { count: number };
	reposts: { count: number };
	quotes: { count: number };
	favorites: { count: number };
}

interface Actions {
	LikeTweet: { url: string; method: "POST" };
	DislikeTweet: { url: string; method: "DELETE" };
	BookmarkTweet: { url: string; method: "POST" };
	UnbookmarkTweet: { url: string; method: "DELETE" };
	RepostTweet: { url: string; method: "POST" };
	UnrepostTweet: { url: string; method: "POST" };
	QuoteTweet: { url: string; method: "POST" };
	ShowTweet: { url: string; method: "GET" };
}

export interface ActionButtonsData {
	counters: Counters;
	actions: Actions;
}
