export interface User {
	id: number;
	name: string;
	link: string;
	email: string;
	about: string | null;
	bg_image: string | null;
	avatar: string | null;
	status_text: string | null;
	site_url: string | null;
	address: string | null;
	birth_date: string;
	created_at: string;
	subscribtions_count: number;
	subscribers_count: number;
	lists: UserList[];
	listsSubscribtions: ListSubscription[];
	device_tokens: string[];
}

interface UserList {
	id: number;
	name: string;
	user_id: number;
	description: string;
	bg_image: string;
	is_private: boolean; // предполагается, что это булево значение
	created_at: string;
	updated_at: string;
	deleted_at: string | null;
}

interface ListSubscription {
	id: number;
	user_id: number;
	users_list_id: number;
	created_at: string;
	updated_at: string;
	deleted_at: string | null;
	listsData: ListsData;
}

interface ListsData {
	id: number;
	name: string;
	user_id: number;
	description: string;
	bg_image: string;
	is_private: boolean; // предполагается, что это булево значение
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
