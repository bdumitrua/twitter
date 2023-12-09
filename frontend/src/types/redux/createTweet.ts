export interface CreateTweetState {
	tweetBodies: TweetBody[];
	groupsList: string[];
	group: string | null;
	currentId: number;
}

interface TweetBody {
	id: number;
	charCount: number;
}

export interface RemoveTweetBodyPayload {
	id: number;
}

export interface UpdateTweetBodyLengthPayload {
	id: number;
	charCount: number;
}

export interface AddTweetBodyPayload {}
