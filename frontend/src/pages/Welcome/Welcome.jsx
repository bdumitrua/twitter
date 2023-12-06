import Cookies from "js-cookie";
import React, { useEffect } from "react";
import { useDispatch, useSelector } from "react-redux";
import { Link } from "react-router-dom";
import { getMeAsync } from "../../redux/slices/user.slice";

const Welcome = () => {
	const dispatch = useDispatch();
	const loggedIn = useSelector((state) => state.auth.loggedIn);
	const error = useSelector((state) => state.user.error);
	const user = useSelector((state) => state.user.user);

	useEffect(() => {
		if (!error && !user && !loggedIn && Cookies.get("access_token")) {
			dispatch(getMeAsync());
		}
	}, [loggedIn, user, error]);

	return (
		<div>
			<Link to="/auth">Авторизация</Link>
			<Link to="/registration">Регистрация</Link>
		</div>
	);
};

export default Welcome;
