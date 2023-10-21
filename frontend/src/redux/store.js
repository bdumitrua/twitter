import { combineReducers, configureStore } from "@reduxjs/toolkit";
import authSlice from "./slices/auth.slice";
import registerSlice from "./slices/register.slice";
import userSlice from "./slices/user.slice";

const rootReducer = combineReducers({
	auth: authSlice,
	register: registerSlice,
	user: userSlice,
});

const store = configureStore({
	reducer: rootReducer,
});

export default store;
