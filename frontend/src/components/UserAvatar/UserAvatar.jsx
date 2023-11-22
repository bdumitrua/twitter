import React from "react";
import userPhoto from "../../assets/images/Tweet/userPhoto.svg";
import styles from "../../assets/styles/components/UserAvatar/UserAvatar.module.scss";

const UserAvatar = () => {
	return (
		<img className={styles["user-avatar"]} src={userPhoto} alt="" />
	);
};

export default UserAvatar;
