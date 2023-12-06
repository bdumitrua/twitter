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
		} catch (error) {
			console.log(error);
			throw error;
		}
	},
};

export default AuthService;
