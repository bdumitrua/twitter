import axiosInstance from "../../utils/axios/instance";

const AuthService = {
	login: async (email, password) => {
		try {
			// Отправляем данные на сервер для проверки и получения токена.
			const response = await axiosInstance.post("/auth/login", {
				email,
				password,
			});

			localStorage.setItem("access_token", response.data.access_token);
			axiosInstance.defaults.headers.common["Authorization"] =
				"Bearer " + response.data.access_token;

			return response.data.access_token;
		} catch (error) {
			console.error(error);
			throw error;
		}
	},
};

export default AuthService;
