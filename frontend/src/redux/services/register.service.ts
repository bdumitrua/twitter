import axiosInstance from "@/utils/axios/instance";

const RegisterService = {
	registerStart: async (
		name: string,
		email: string,
		birthDate: string
	): Promise<number> => {
		const response = await axiosInstance.post(
			"/api/auth/registration/start",
			{
				name,
				email,
				birthDate,
			}
		);
		return response.data.registrationId; // предполагая, что сервер возвращает число
	},
	registerCode: async (code: string, registerId: number) => {
		const response = await axiosInstance.post(
			`/api/auth/registration/confirm/${registerId}`,
			{
				code,
			}
		);
		return response.status;
	},
	register: async (password: string, registerId: number) => {
		const response = await axiosInstance.post(
			`/api/auth/registration/end/${registerId}`,
			{
				password,
			}
		);
		return response.status;
	},
};

export default RegisterService;
