interface Author {
	id: number;
	name: string;
	link: string;
	avatar: string | null;
}

interface Content {
	text: string;
	notices: any[]; // Замените any на более конкретный тип, если у вас есть информация о типе 'notices'.
	created_at: string;
}

interface Counters {
	likes: {
		count: number;
	};
	replies: {
		count: number;
	};
	reposts: {
		count: number;
	};
	quotes: {
		count: number;
	};
	favorites: {
		count: number;
	};
}

export interface TweetTypes {
	id: number;
	key?: number;
	type: string;
	author: Author;
	content: Content;
	counters: Counters;
	haveThread: boolean;
	related: any[]; // Замените any на более конкретный тип, если у вас есть информация о типе 'related'.
	replies: TweetTypes[]; // Здесь 'replies' - массив постов, вы можете использовать рекурсивный тип, если это необходимо.
}
