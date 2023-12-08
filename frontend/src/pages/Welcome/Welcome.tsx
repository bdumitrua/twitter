import { AppDispatch, RootState } from "@/redux/store";
import Cookies from "js-cookie";
import { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { Link, useNavigate } from "react-router-dom";
import { getMeAsync } from "../../redux/slices/user.slice";

const Welcome = () => {
	const dispatch = useDispatch<AppDispatch>();
	const navigate = useNavigate();
	const loggedIn = useSelector((state: RootState) => state.auth.loggedIn);
	const error = useSelector((state: RootState) => state.user.error);
	const user = useSelector((state: RootState) => state.user.user);

	useEffect(() => {
		if (!error && !user && !loggedIn && Cookies.get("access_token")) {
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
