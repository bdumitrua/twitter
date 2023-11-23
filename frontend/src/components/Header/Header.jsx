import React from "react";
import { useLocation } from "react-router-dom";
import { Link } from "react-router-dom";

import accountImage from "../../assets/images/Header/accountImage.svg";
import somthingIcon from "../../assets/images/Header/somethingIcon.svg";
import twitterLogo from "../../assets/images/Header/twitterLogo.svg";
import leftArrowIcon from "../../assets/images/Header/leftArrowIcon.svg";
import styles from "../../assets/styles/components/Header.module.scss";

const Header = (props) => {
	const location = useLocation();

	return (
		<div className={styles["header"]}>
			{location.pathname === "/tweet" || location.pathname === "/profile" ? (
				<>
					<Link to="/">
						<img src={leftArrowIcon} alt="" />
					</Link>
					<span className={styles["header__title"]}>Tweet</span>
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
