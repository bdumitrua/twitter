import { Link, Location, useLocation } from "react-router-dom";

import { RootState } from "@/redux/store";
import { User } from "@/types/redux/user";
import { useSelector } from "react-redux";
import accountImage from "../../assets/images/Header/accountImage.svg";
import leftArrowIcon from "../../assets/images/Header/leftArrowIcon.svg";
import somthingIcon from "../../assets/images/Header/somethingIcon.svg";
import twitterLogo from "../../assets/images/Header/twitterLogo.svg";
import styles from "../../assets/styles/components/Header.module.scss";
import UserAvatarPlug from "../UserAvatar/UserAvatarPlug";

interface HeaderProps {
	haveUnwatched: boolean;
}

const Header: React.FC<HeaderProps> = (props) => {
	const location: Location<any> = useLocation();

	const entities: string[] = ["tweet", "profile"];
	const isEntity: boolean = entities.some(
		(entity) => entity === location.pathname.split("/")[1]
	);

	const authorizedUser: User | null = useSelector(
		(state: RootState) => state.user.authorizedUser
	);

	return (
		<div className={styles["header"]}>
			{isEntity ? (
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
					{authorizedUser?.avatar ? (
						<div className={styles["header__burger-icon"]}>
							<img src={accountImage} alt="" />
							{props.haveUnwatched && (
								<div
									className={
										styles["header__unwatched-circle"]
									}
								></div>
							)}
						</div>
					) : (
						<div className={styles["header__burger-icon"]}>
							<UserAvatarPlug
								authorId={authorizedUser?.id}
								userName={authorizedUser?.name}
							/>
						</div>
					)}
					<Link to="/">
						<img src={twitterLogo} alt="" />
					</Link>
					<div>
						<img src={somthingIcon} alt="" />
					</div>
				</>
			)}
		</div>
	);
};

export default Header;
