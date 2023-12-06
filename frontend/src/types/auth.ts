export interface AuthState {
	accessToken: string | null;
	loading: boolean;
	error: string | null;
	loggedIn: boolean;
}

export interface LoginPayload {
	email: string;
	password: string;
}
