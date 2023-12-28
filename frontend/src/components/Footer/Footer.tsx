import CreateTweetButton from './CreateTweetButton';

import homeIcon from '../../assets/images/Footer/homeIcon.svg';
import messageIcon from '../../assets/images/Footer/messageIcon.svg';
import notificationsIcon from '../../assets/images/Footer/notificationsIcon.svg';
import searchIcon from '../../assets/images/Footer/searchIcon.svg';
import styles from '../../assets/styles/components/Footer/Footer.module.scss';
import { Link } from 'react-router-dom';

const Footer: React.FC = () => {
	return (
		<>
			<div className={styles['footer']}>
				<a href=' '>
					<img src={homeIcon} alt='' />
				</a>
				<a href=' '>
					<img src={searchIcon} alt='' />
				</a>
				<Link to='/notifications'>
					<img src={notificationsIcon} alt='' />
				</Link>
				<a href=' '>
					<img src={messageIcon} alt='' />
				</a>
			</div>
			<CreateTweetButton />
		</>
	);
};
export default Footer;
