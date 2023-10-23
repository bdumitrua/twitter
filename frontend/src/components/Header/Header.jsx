import React from "react";
import s from "../../assets/styles/components/Header.module.scss"
import header__accountImage from "../../assets/images/Header/Account.svg"
import header__twitterLogo from "../../assets/images/Header/Twitter Logo.svg"
import header__somthingIcon from "../../assets/images/Header/Feature stroke icon.svg"

const Header = () => {
  return (
    <div className={s.header}>
      <div> <img src={header__accountImage} alt="asasas" /></div>
      <div> <img src={header__twitterLogo} alt="" /></div>
      <div> <img src={header__somthingIcon} alt="" /></div>
    </div>
  );
}

export default Header;