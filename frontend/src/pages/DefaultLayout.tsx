import styles from "@/assets/styles/DefaultLayout.scss";
import useScrollToTop from "@/utils/hooks/useScrollToTop";
import { Outlet } from "react-router-dom";
import Footer from "../components/Footer/Footer";
import Header from "../components/Header/Header";

const DefaultLayout: React.FC = () => {
	const haveUnwatched: boolean = true;

	useScrollToTop();

	return (
		<div className={styles["default-layout"]}>
			<Header haveUnwatched={haveUnwatched} />
			<Outlet />
			<Footer />
		</div>
	);
};

export default DefaultLayout;
