import axiosInstance from "@/utils/axios/instance";

const RegisterService = {
	registerStart: async (name, email, birth_date) => {
		const response = await axiosInstance.post("auth/start", {
			name,
			email,
			birth_date,
		});
		return response.data.registration_id;
	},
	registerCode: async (code, registerId) => {
		const response = await axiosInstance.post(
			`auth/confirm/${registerId}`,
			{
				code,
			}
		);
		return response.status;
	},
	register: async (password, registerId) => {
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
