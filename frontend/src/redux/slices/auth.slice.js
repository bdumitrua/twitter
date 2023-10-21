import { createAsyncThunk, createSlice } from "@reduxjs/toolkit";
import AuthService from "../services/auth.service";
import { resetUser } from "./user.slice";

// Создаем асинхронный Thunk для выполнения запроса на авторизацию
export const loginAsync = createAsyncThunk(
	"auth/login",
	async ({ email, password }) => {
		const access_token = await AuthService.login(email, password);
		saveToken(access_token);
		return access_token;
	}
);

export const saveToken = (token) => {
	return {
		type: "auth/saveToken",
		payload: token,
	};
};

const initialState = {
	accessToken: null,
	loading: false,
	error: null,
	loggedIn: false,
};

const authSlice = createSlice({
	name: "auth",
	initialState,
	reducers: {
		logout: (state) => {
			state.accessToken = null;
			localStorage.removeItem("access_token");
			resetUser();
		},
		setLoggedIn: (state, action) => {
			state.loggedIn = action.payload;
		},
		setLoggedOut: (state, action) => {
			state.loggedIn = action.payload;
		},
	},

	extraReducers: (builder) => {
		builder
			.addCase(loginAsync.pending, (state) => {
				state.loading = true;
				state.error = null;
			})
			.addCase(loginAsync.fulfilled, (state, action) => {
				state.loading = false;
				state.accessToken = action.payload;
				state.error = null;
				state.loggedIn = true;
			})
			.addCase(loginAsync.rejected, (state, action) => {
				state.loading = false;
				state.error = action.error.message;
			});
	},
});

export const { logout, setLoggedIn, setLoggedOut } = authSlice.actions;
export default authSlice.reducer;
