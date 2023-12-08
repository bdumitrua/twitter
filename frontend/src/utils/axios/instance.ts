import { ApiError } from "@/types/api";
import axios, { AxiosError } from "axios";
import Cookies from "js-cookie";

const ApiUrl: string = "/api";

const axiosInstance = axios.create({
	baseURL: ApiUrl,
});

axiosInstance.defaults.headers.common["Authorization"] =
	"Bearer " + Cookies.get("access_token");

// Создаем перехватчик ответов
axiosInstance.interceptors.response.use(
	null,
	async (error: AxiosError<ApiError>) => {
		if (error.config?.url?.includes("/auth/login")) {
			return Promise.reject(error);
		}

		if (error.config && error.response && error.response.status === 401) {
			// Сохраняем оригинальный запрос
			const originalRequest = error.config;
			const token = Cookies.get("access_token");

			if (token) {
				try {
					// Получаем новый токен
					const response = await axiosInstance.get(
						`/auth/refresh?token=${token}`
					);

					// Обновляем токен в хранилище
					Cookies.set("access_token", response.data.access_token, {
						expires: 14,
					});

					// Обновляем токен в заголовке авторизации
					axiosInstance.defaults.headers.common["Authorization"] =
						"Bearer " + Cookies.get("access_token");

					// Повторяем оригинальный запрос с новым токеном
					return axiosInstance(originalRequest);
				} catch (err) {
					console.error(err);
				}
			}
		}

		return Promise.reject(error);
	}
);

export default axiosInstance;
