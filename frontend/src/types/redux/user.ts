export interface User {
	id: number;
	name: string;
	link: string;
	email: string;
	about: string | null;
	bgImage: string | null;
	avatar: string | null;
	statusText: string | null;
	siteUrl: string | null;
	address: string | null;
	birthDate: string;
	created_at: string;
	subscribtionsCount: number;
	subscribersCount: number;
	lists: UserList[];
	listsSubscribtions: ListSubscription[];
	deviceTokens: string[];
}

interface UserList {
	id: number;
	name: string;
	userId: number;
	description: string;
	bgImage: string;
	isPrivate: boolean; // предполагается, что это булево значение
	created_at: string;
	updated_at: string;
	deleted_at: string | null;
}

interface ListSubscription {
	id: number;
	userId: number;
	users_list_id: number;
	created_at: string;
	updated_at: string;
	deleted_at: string | null;
	listsData: ListsData;
}

interface ListsData {
	id: number;
	name: string;
	userId: number;
	description: string;
	bgImage: string;
	isPrivate: boolean; // предполагается, что это булево значение
	created_at: string;
	updated_at: string;
	deleted_at: string | null;
}

export interface UserState {
	authorizedUser: User | null;
	isSuccessfull: boolean | null;
	loading: boolean;
	error: string | null;
}
