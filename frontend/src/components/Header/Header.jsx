import React from "react";
import accountImage from "../../assets/images/Header/accountImage.svg";
import somthingIcon from "../../assets/images/Header/somethingIcon.svg";
import twitterLogo from "../../assets/images/Header/twitterLogo.svg";
import styles from "../../assets/styles/components/Header.module.scss";

const Header = (props) => {
	return (
		<div className={styles["header"]}>
			<div className={styles["header__burger-icon"]}>
				<img src={accountImage} alt="" />
				{props.haveUnwatched && (
					<div className={styles["header__unwatched-circle"]}></div>
				)}
			</div>
			<div>
				<img src={twitterLogo} alt="" />
			</div>
			<div>
				<img src={somthingIcon} alt="" />
			</div>
		</div>
	);
};

export default Header;
