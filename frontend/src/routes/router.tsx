import CreateTweet from '@/pages/CreateTweet/CreateTweet';
import { RootState } from '@/redux/store';
import { User } from '@/types/redux/user';
import { ReactNode } from 'react';
import { useSelector } from 'react-redux';
import { createBrowserRouter, Navigate } from 'react-router-dom';
import Authorization from '../pages/Authorization/Authorization';
import DefaultLayout from '../pages/DefaultLayout';
import Feed from '../pages/Feed/Feed';
import Profile from '../pages/Profile/Profile';
import LikesTab from '../pages/Profile/Tabs/LikesTab';
import MediaTab from '../pages/Profile/Tabs/MediaTab';
import Notifications from '@/pages/Notifications/Notifications';
import TweetsAndRepliesTab from '../pages/Profile/Tabs/TweetsAndRepliesTab';
import TweetsTab from '../pages/Profile/Tabs/TweetsTab';
import Registration from '../pages/Registration/Registration';
import RegistrationCode from '../pages/Registration/RegistrationCode';
import RegistrationConfirmation from '../pages/Registration/RegistrationConfirmation';
import RegistrationEnd from '../pages/Registration/RegistrationEnd';
import RegistrationStart from '../pages/Registration/RegistrationStart';
import TweetPage from '../pages/TweetPage/TweetPage';
import Welcome from '../pages/Welcome/Welcome';

interface ProtectedRouteProps {
	children: ReactNode;
}

const ProtectedRoute: React.FC<ProtectedRouteProps> = ({ children }) => {
	const authorizedUser: User | null = useSelector(
		(state: RootState) => state.user.authorizedUser
	);
	console.log('children', children);
	console.log('authorizedUser', authorizedUser);

	if (!authorizedUser) {
		// Перенаправление на страницу авторизации
		return <Navigate to='/welcome' />;
	}

	return children;
};

const router = createBrowserRouter([
	{
		path: '/',
		element: (
			// <ProtectedRoute>
				<DefaultLayout />
			// </ProtectedRoute>
		),
		children: [
			{
				path: '/',
				element: <Navigate to='/feed' />,
			},
			{
				path: '/feed',
				element: <Feed />,
			},
			{
				path: '/notifications',
				element: (
					<Notifications
						links={[
							{ text: 'Все', link: '/notifications/all' },
							{
								text: 'Mentions',
								link: '/notifications/mentions',
							},
						]}
					/>
				),
				children: [
					{
						path: '/notifications',
						element: <Navigate to='/notifications/' />,
					},
					{
						path: '/notifications/:link',
						element: <Navigate to='/notifications/:id' />,
					},
				],
			},
			{
				path: '/tweet',
				element: <TweetPage />,
			},
			{
				path: '/create',
				element: <CreateTweet />,
			},
			{
				path: '/profile/:id',
				element: <Profile />,
				children: [
					{
						path: '/profile/:id',
						element: <Navigate to='tweets' />,
					},
					{
						path: 'tweets',
						element: <TweetsTab />,
					},
					{
						path: 'tweets-with-replies',
						element: <TweetsAndRepliesTab />,
					},
					{
						path: 'media',
						element: <MediaTab />,
					},
					{
						path: 'likes',
						element: <LikesTab />,
					},
				],
			},
		],
	},
	{
		path: '/welcome',
		element: <Welcome />,
	},
	{
		path: '/auth',
		element: <Authorization />,
	},
	{
		path: '/registration',
		element: <Registration />,
		children: [
			{
				path: '/registration',
				element: <Navigate to='/registration/start' />,
			},
			{
				path: '/registration/start',
				element: <RegistrationStart />,
			},
			{
				path: '/registration/confirm/:registrationId',
				element: <RegistrationConfirmation />,
			},
			{
				path: '/registration/code/:registrationId',
				element: <RegistrationCode />,
			},
			{
				path: '/registration/end/:registrationId',
				element: <RegistrationEnd />,
			},
		],
	},
]);

export default router;
