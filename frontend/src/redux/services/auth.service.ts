/* eslint-disable @typescript-eslint/no-explicit-any */
import axiosInstance from "@/utils/axios/instance";

const AuthService = {
	login: async (email: string, password: string): Promise<string> => {
		try {
			const response = await axiosInstance.post<{
				access_token: string;
			}>("/auth/login", {
				email,
				password,
			});
			axiosInstance.defaults.headers.common["Authorization"] =
				"Bearer " + response.data.access_token;

			return response.data.access_token;
		} catch (error: any) {
			if (error.response) {
				console.error(error.response.status, error.response.data);
			} else if (error.request) {
				console.error(error.request);
			} else {
				console.error(error.message);
			}
			throw error;
		}
	},
};

export default AuthService;