import axiosInstance from "@/utils/axios/instance";

const UserService = {
	getMe: async () => {
		try {
			// Отправляем данные на сервер для проверки и получения токена.
			const response = await axiosInstance.get("/user/");

			// Возвращаем токен из функции
			console.log(response.data);
			return response.data;
		} catch (error) {
			console.error("Ошибка", error);

			throw error;
		}
	},
};

export default UserService;
