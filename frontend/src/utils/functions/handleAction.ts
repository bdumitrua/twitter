import axiosInstance from "../axios/instance";

export const handleAction: (
	currentState: any,
	setState: (value: any) => void,
	actions: any
) => Promise<void> = async (currentState, setState, actions) => {
	setState(!currentState);
	const action = currentState ? actions.uncheckAction : actions.checkAction;
	try {
		await axiosInstance({ method: action.method, url: action.url });
	} catch (error) {
		setState(!currentState); // Возвращаем состояние обратно, если произошла ошибка
		console.error(error);
	}
};
