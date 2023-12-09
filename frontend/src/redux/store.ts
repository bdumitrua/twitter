import { combineReducers, configureStore } from "@reduxjs/toolkit";
import authSlice from "./slices/auth.slice";
import createTweetSlice from "./slices/createTweet.slice";
import registerSlice from "./slices/register.slice";
import userSlice from "./slices/user.slice";

const rootReducer = combineReducers({
	auth: authSlice,
	register: registerSlice,
	user: userSlice,
	createTweet: createTweetSlice,
});

const store = configureStore({
	reducer: rootReducer,
});

export type AppDispatch = typeof store.dispatch;
export type RootState = ReturnType<typeof store.getState>;
export default store;
