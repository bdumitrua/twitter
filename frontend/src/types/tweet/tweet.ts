export interface Author {
	id: number;
	name: string;
	link: string;
	avatar: string | null;
}

export interface Content {
	text: string;
	notices: any[]; // Замените any на более конкретный тип, если у вас есть информация о типе 'notices'.
	created_at: string;
}

export interface Counters {
	likes: { count: number };
	replies: { count: number };
	reposts: { count: number };
	quotes: { count: number };
	favorites: { count: number };
}

export interface Actions {
	LikeTweet: { url: string; method: "POST" };
	DislikeTweet: { url: string; method: "DELETE" };
	BookmarkTweet: { url: string; method: "POST" };
	UnbookmarkTweet: { url: string; method: "DELETE" };
	RepostTweet: { url: string; method: "POST" };
	UnrepostTweet: { url: string; method: "POST" };
	QuoteTweet: { url: string; method: "POST" };
	ShowTweet: { url: string; method: "GET" };
}

export interface TweetTypes {
	id: number;
	key?: number;
	type: string;
	author: Author;
	content: Content;
	counters: Counters;
	haveThread: boolean;
	actions: Actions;
	related: any[]; // ! ???
	replies: TweetTypes[]; // ???
}

export interface ActionButtonsData {
	counters: Counters;
	actions: Actions;
}
