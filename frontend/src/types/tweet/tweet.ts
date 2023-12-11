export interface Author {
	id: number;
	name: string;
	link: string;
	avatar: string | null;
}

interface TweetContent {
	text: string;
	notices: any[]; // Замените any на более конкретный тип, если у вас есть информация о типе 'notices'.
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

export interface TweetTypes {
	id: number;
	key?: number;
	type: string;
	author: Author;
	content: TweetContent;
	counters: TweetCounters;
	actions: TweetActions;
	related: TweetTypes;
	replies: TweetTypes[];
	thread: TweetTypes;
}

export interface ActionButtonsData {
	counters: TweetCounters;
	actions: TweetActions;
}
