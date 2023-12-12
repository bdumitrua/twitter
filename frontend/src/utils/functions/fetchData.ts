import axiosInstance from "../axios/instance";

export const fetchData = async (path: string) => {
	const response = await axiosInstance.get(path);

	return response.data;
};
