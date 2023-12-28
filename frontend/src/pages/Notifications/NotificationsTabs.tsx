import { Link } from 'react-router-dom';

import styles from '../../assets/styles/pages/Notifications/NotificationTabs.module.scss';

type Props = {
	links: { text: string; link: string }[];
	activeTabIndex: number;
	setActiveTabIndex: (index: number) => void;
};

const NotificationsTabs: React.FC<Props> = props => {
	const handleTabChange = (index: number) => {
		props.setActiveTabIndex(index);
	}

	return (
		<div className={styles['tabs-bar']}>
			{props.links.map((link, index) => (
				<Link
					to={link.link}
					className={`${styles['tab']} ${
						index === props.activeTabIndex ? styles['active'] : ''
					}`}
					key={link.text}
					onClick={() => handleTabChange(index)}
				>
					{link.text}
				</Link>
			))}
		</div>
	);
};

export default NotificationsTabs;
