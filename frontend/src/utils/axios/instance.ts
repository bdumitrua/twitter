import { ApiError } from "@/types/api";
import axios, { AxiosError } from "axios";
import Cookies from "js-cookie";

const ApiUrl: string = "";

const axiosInstance = axios.create({
	baseURL: ApiUrl,
});

axiosInstance.defaults.headers.common["Authorization"] =
	"Bearer " + Cookies.get("accessToken");

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
			const token: string | undefined = Cookies.get("accessToken");

			if (token) {
				try {
					// Получаем новый токен
					const response = await axiosInstance.get(
						`/auth/refresh?token=${token}`
					);

					// Обновляем токен в хранилище
					Cookies.set("accessToken", response.data.accessToken, {
						expires: 14,
					});

					// Обновляем токен в заголовке авторизации
					axiosInstance.defaults.headers.common["Authorization"] =
						"Bearer " + Cookies.get("accessToken");

					// Повторяем оригинальный запрос с новым токеном
					return axiosInstance(originalRequest);
				} catch (err) {
					console.error(err);
				}
			}
		}

		if (error.config && error.response && error.response.status === 403) {
			Cookies.remove("accessToken");
		}

		return Promise.reject(error);
	}
);

export default axiosInstance;
