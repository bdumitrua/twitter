import axiosInstance from "@/utils/axios/instance";

const RegisterService = {
	register: async (name, email, password) => {
		try {
			// Отправляем данные на сервер для проверки и получения токена.
			const response = await axiosInstance.post("auth/register", {
				name,
				email,
				password,
			});

			// Возвращаем токен из функции
			console.log(response);
			return response.data.message;
		} catch (error) {
			console.error("Ошибка", error);

			throw error;
		}
	},
};

export default RegisterService;
