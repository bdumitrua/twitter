/* eslint-disable @typescript-eslint/no-explicit-any */
import { User } from "@/types/redux/user";
import axiosInstance from "@/utils/axios/instance";

const UserService = {
	getMe: async (): Promise<User> => {
		try {
			const response = await axiosInstance.get<User>("/api/users");

			return response.data;
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

export default UserService;
