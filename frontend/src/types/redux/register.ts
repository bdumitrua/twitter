export interface RegisterError {
	message: string;
	status: number;
}

export interface RegisterState {
	registrationId: number | null;
	loading: boolean;
	error: RegisterError | null;
}

export interface RegisterStartPayload {
	name: string;
	email: string;
	birth_date: string;
}

export interface RegisterCodePayload {
	code: string;
	registrationId: number;
}

export interface RegisterEndPayload {
	password: string;
	repeatPassword?: string;
	registrationId: number;
}
