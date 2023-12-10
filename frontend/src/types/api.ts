export interface ApiResponse<T> {
	data: T;
}

export interface ApiError {
	message: string;
}

export interface ErrorMessages {
	[key: number]: string;
}
