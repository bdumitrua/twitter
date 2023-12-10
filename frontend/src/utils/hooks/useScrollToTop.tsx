import { useEffect } from "react";
import { Location, useLocation } from "react-router-dom";

const useScrollToTop: () => void = () => {
	const { pathname }: Location<any> = useLocation();

	useEffect(() => {
		window.scrollTo(0, 0);
	}, [pathname]);
};

export default useScrollToTop;
