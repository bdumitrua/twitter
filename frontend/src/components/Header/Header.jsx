import React from "react";
import { Link, useLocation } from "react-router-dom";

import accountImage from "../../assets/images/Header/accountImage.svg";
import leftArrowIcon from "../../assets/images/Header/leftArrowIcon.svg";
import somthingIcon from "../../assets/images/Header/somethingIcon.svg";
import twitterLogo from "../../assets/images/Header/twitterLogo.svg";
import styles from "../../assets/styles/components/Header.module.scss";

const Header = (props) => {
	const location = useLocation();

	const pathnames = ["tweet", "profile"];

	return (
		<div className={styles["header"]}>
			{pathnames.some(
				(pathname) => pathname === location.pathname.split("/")[1]
			) ? (
				<>
					<Link to="/">
						<img src={leftArrowIcon} alt="" />
					</Link>
					<span className={styles["header__title"]}>
						{location.pathname.split("/")[1]}
					</span>
				</>
			) : (
				<>
					<div className={styles["header__burger-icon"]}>
						<img src={accountImage} alt="" />
						{props.haveUnwatched && (
							<div
								className={styles["header__unwatched-circle"]}
							></div>
						)}
					</div>
					<div>
						<img src={twitterLogo} alt="" />
					</div>
					<div>
						<img src={somthingIcon} alt="" />
					</div>
				</>
			)}
		</div>
	);
};

export default Header;
