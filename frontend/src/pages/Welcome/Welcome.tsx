import { AppDispatch, RootState } from "@/redux/store";
import { User } from "@/types/redux/user";
import Cookies from "js-cookie";
import { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { Link, NavigateFunction, useNavigate } from "react-router-dom";
import { getMeAsync } from "../../redux/slices/user.slice";

const Welcome: React.FC = () => {
	const dispatch = useDispatch<AppDispatch>();
	const navigate: NavigateFunction = useNavigate();
	// const loggedIn: boolean = useSelector(
	// 	(state: RootState) => state.auth.loggedIn
	// );
	const error: string | null = useSelector(
		(state: RootState) => state.user.error
	);
	const user: User | null = useSelector(
		(state: RootState) => state.user.user
	);

	useEffect(() => {
		if (!user && Cookies.get("access_token") && !error) {
			dispatch(getMeAsync());
		}
		if (user) {
			navigate("/feed");
		}
	}, [user, error]);

	return (
		<div>
			<Link to="/auth">Авторизация</Link>
			<Link to="/registration">Регистрация</Link>
		</div>
	);
};

export default Welcome;
