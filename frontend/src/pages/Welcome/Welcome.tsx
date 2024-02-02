import { Link } from "react-router-dom";

const Welcome: React.FC = () => {
	
	return (
		<div>
			<Link to="/auth">Авторизация</Link>
			<Link to="/registration">Регистрация</Link>
		</div>
	);
};

export default Welcome;
