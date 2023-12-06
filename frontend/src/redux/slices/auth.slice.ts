import { AuthState, LoginPayload } from "@/types/auth";
import { PayloadAction, createAsyncThunk, createSlice } from "@reduxjs/toolkit";
import Cookies from "js-cookie";
import { useDispatch } from "react-redux";
import AuthService from "../services/auth.service";
import { resetUser } from "./user.slice";

const dispatch = useDispatch();
// Создаем асинхронный Thunk для выполнения запроса на авторизацию
export const loginAsync = createAsyncThunk<string, LoginPayload>(
	"auth/login",
	async ({ email, password }) => {
		const access_token = await AuthService.login(email, password);
		console.log(access_token);
		saveToken(access_token);
		return access_token;
	}
);

export const saveToken = (token: string) => {
	return {
		type: "auth/saveToken",
		payload: token,
	};
};

const initialState: AuthState = {
	accessToken: Cookies.get("access_token") || null,
	loading: false,
	error: null,
	loggedIn: false,
};

const authSlice = createSlice({
	name: "auth",
	initialState,
	reducers: {
		logout: (state: AuthState) => {
			state.accessToken = null;
			state.loading = false;
			state.error = null;
			state.loggedIn = false;
			Cookies.remove("access_token");
			dispatch(resetUser());
		},
		setLoggedIn: (state: AuthState, action: PayloadAction<boolean>) => {
			state.loggedIn = action.payload;
		},
		setLoggedOut: (state: AuthState, action: PayloadAction<boolean>) => {
			state.loggedIn = action.payload;
		},
	},

	extraReducers: (builder) => {
		builder
			.addCase(loginAsync.pending, (state: AuthState) => {
				state.loading = true;
				state.error = null;
			})
			.addCase(
				loginAsync.fulfilled,
				(state: AuthState, action: PayloadAction<string>) => {
					state.loading = false;
					state.accessToken = action.payload;
					Cookies.set("access_token", action.payload, {
						expires: 14,
					});
					state.error = null;
					state.loggedIn = true;
				}
			)
			.addCase(loginAsync.rejected, (state: AuthState, action) => {
				state.loading = false;
				state.error = action.error.message || null;
			});
	},
});

export const { logout, setLoggedIn, setLoggedOut } = authSlice.actions;
export default authSlice.reducer;
