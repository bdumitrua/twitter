export interface Author {
	id: number;
	name: string;
	about: string | null;
	link: string;
	avatar: string | null;
}

interface Notice {
	link: string;
	userId: number;
}

interface TweetContent {
	text: string;
	notices: Notice[];
	created_at: string;
}

interface TweetCounters {
	likes: { count: number };
	replies: { count: number };
	reposts: { count: number };
	quotes: { count: number };
	favorites: { count: number };
}

interface TweetActions {
	LikeTweet: { url: string; method: string };
	DislikeTweet: { url: string; method: string };
	BookmarkTweet: { url: string; method: string };
	UnbookmarkTweet: { url: string; method: string };
	RepostTweet: { url: string; method: string };
	UnrepostTweet: { url: string; method: string };
	QuoteTweet: { url: string; method: string };
	ShowTweet: { url: string; method: string };
}

export interface Tweet {
	id: number;
	key?: number;
	type: string;
	author: Author;
	content: TweetContent;
	counters: TweetCounters;
	actions: TweetActions;
	related: Tweet;
	replies: Tweet[];
}

export interface ActionButtonsData {
	counters: TweetCounters;
	actions: TweetActions;
}
