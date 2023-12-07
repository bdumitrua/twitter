import axiosInstance from "@/utils/axios/instance";

const RegisterService = {
	registerStart: async (
		name: string,
		email: string,
		birth_date: string
	): Promise<number> => {
		const response = await axiosInstance.post("auth/start", {
			name,
			email,
			birth_date,
		});
		return response.data.registration_id; // предполагая, что сервер возвращает число
	},
	registerCode: async (code: string, registerId: number) => {
		const response = await axiosInstance.post(
			`auth/confirm/${registerId}`,
			{
				code,
			}
		);
		return response.status;
	},
	register: async (password: string, registerId: number) => {
		const response = await axiosInstance.post(
			`auth/register/${registerId}`,
			{
				password,
			}
		);
		return response.status;
	},
};

export default RegisterService;
