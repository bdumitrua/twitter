import styles from "@/assets/styles/pages/Welcome/Welcome.module.scss";
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

	const error: string | null = useSelector(
		(state: RootState) => state.user.error
	);
	const user: User | null = useSelector(
		(state: RootState) => state.user.authorizedUser
	);

	useEffect(() => {
		if (!user && Cookies.get("accessToken") && !error) {
			dispatch(getMeAsync());
		}
		if (user) {
			navigate("/feed");
		}
	}, [user, error]);

	return (
		<>
			<div className={styles["welcome"]}>
				<h1 className={styles["welcome__title"]}>
					В курсе происходящего
				</h1>
				<div className={styles["welcome__content"]}>
					<h3 className={styles["welcome__content-title"]}>
						Присоединяйтесь сегодня.
					</h3>
					<div className={styles["welcome__register"]}>
						<div className={styles["welcome__register-services"]}>
							<Link
								to="/"
								className={
									styles["welcome__register-button--black"]
								}
							>
								Войти с Google
							</Link>
							<Link
								to="/"
								className={
									styles["welcome__register-button--black"]
								}
							>
								Зарегистрироваться с Apple ID
							</Link>
						</div>
						<div className={styles["welcome__divider"]}>
							<p className={styles["welcome__divider-text"]}>
								или
							</p>
						</div>
						<div className={styles["welcome__register-standart"]}>
							<Link
								to="/registration"
								className={
									styles["welcome__register-button--blue"]
								}
							>
								Зарегистрироваться
							</Link>
							<span className={styles["welcome__agreement"]}>
								Регистрируюясь, вы соглашаетесь с{" "}
								<Link
									to="/"
									className={
										styles["welcome__agreement-links"]
									}
								>
									Условиями предоставления услуг
								</Link>{" "}
								и{" "}
								<Link
									to="/"
									className={
										styles["welcome__agreement-links"]
									}
								>
									Политикой конфиденциальности
								</Link>
								, а также с{" "}
								<Link
									to="/"
									className={
										styles["welcome__agreement-links"]
									}
								>
									Политикой использования файлов cookie
								</Link>
							</span>
						</div>
					</div>

					<div className={styles["welcome__auth"]}>
						<h4 className={styles["welcome__auth-title"]}>
							Уже зарегистрированы?
						</h4>
						<Link
							to="/auth"
							className={
								styles["welcome__register-button--white"]
							}
						>
							Войти
						</Link>
					</div>

					<footer className={styles["welcome__footer"]}>
						<Link
							to="/"
							className={styles["welcome__footer-button"]}
						>
							О нас
						</Link>
						<Link
							to="/"
							className={styles["welcome__footer-button"]}
						>
							Скачать приложение
						</Link>
						<Link
							to="/"
							className={styles["welcome__footer-button"]}
						>
							Справочный центр
						</Link>
						<Link
							to="/"
							className={styles["welcome__footer-button"]}
						>
							Условия предоставления услуг
						</Link>
						<Link
							to="/"
							className={styles["welcome__footer-button"]}
						>
							Политика конфиденциальности
						</Link>
						<Link
							to="/"
							className={styles["welcome__footer-button"]}
						>
							Политика в отношении файлов cookie
						</Link>
					</footer>
				</div>
			</div>
		</>
	);
};

export default Welcome;
