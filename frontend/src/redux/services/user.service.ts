import { User } from "@/types/redux/user";
import axiosInstance from "@/utils/axios/instance";

const UserService = {
	getMe: async (): Promise<User> => {
		try {
			const response = await axiosInstance.get<User>("/user/");
			console.log(response.data);
			return response.data;
		} catch (error) {
			console.error("Ошибка", error);
			throw error;
		}
	},
};

export default UserService;
